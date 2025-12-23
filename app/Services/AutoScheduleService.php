<?php

namespace App\Services;

use App\Models\ClassSchedule;
use App\Models\ClassScheduleDetail;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Room;
use App\Models\AcademicYear;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoScheduleService
{
    /**
     * Auto-generate schedule details for a class schedule based on course dates and preferences
     *
     * @param ClassSchedule $classSchedule
     * @param array $params Parameters including start_date, end_date, study_days, courses
     * @return array
     */
    public function generateAutoSchedule(ClassSchedule $classSchedule, array $params): array
    {
        return DB::transaction(function () use ($classSchedule, $params) {
            $startDate = Carbon::parse($params['start_date']);
            $endDate = Carbon::parse($params['end_date']);
            $studyDays = $params['study_days'] ?? []; // Array of days: ['senin', 'selasa', etc.]
            $courses = $params['courses'] ?? []; // Array of course data
            $onlinePercentage = $classSchedule->online_percentage ?? 0;
            $offlinePercentage = $classSchedule->offline_percentage ?? 100;

            // Clear existing details to avoid duplicates
            $this->clearExistingDetails($classSchedule);

            $generatedDetails = [];
            $conflicts = [];
            $usedCourses = [];
            $roomUsageStats = $this->initializeRoomUsageStats();

            // Group courses by credits and required sessions
            $courseSessions = $this->calculateCourseSessions($courses, $startDate, $endDate, $studyDays);

            // Generate schedule for each course session
            foreach ($courseSessions as $courseData) {
                $course = $courseData['course'];
                $sessionCount = $courseData['sessions'];

                // Skip if course already used
                if (in_array($course->id, $usedCourses)) {
                    $conflicts[] = [
                        'course_id' => $course->id,
                        'course_name' => $course->course_name,
                        'error' => 'Course already scheduled'
                    ];
                    continue;
                }

                $usedCourses[] = $course->id;

                // Generate sessions for this course
                for ($session = 0; $session < $sessionCount; $session++) {
                    try {
                        $detail = $this->createScheduleDetail(
                            $classSchedule,
                            $course,
                            $startDate,
                            $endDate,
                            $studyDays,
                            $onlinePercentage,
                            $roomUsageStats,
                            $session,
                            $sessionCount
                        );

                        if ($detail) {
                            $generatedDetails[] = $detail;
                            $this->updateRoomUsageStats($roomUsageStats, $detail->room_id);
                        }
                    } catch (\Exception $e) {
                        $conflicts[] = [
                            'course_id' => $course->id,
                            'course_name' => $course->course_name,
                            'session' => $session + 1,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            }

            // Log the generation
            Log::channel('activity')->info('Auto-schedule generated', [
                'class_schedule_id' => $classSchedule->id,
                'total_details' => count($generatedDetails),
                'total_conflicts' => count($conflicts),
                'user_id' => auth('sanctum')->id(),
            ]);

            return [
                'generated_details' => $generatedDetails,
                'conflicts' => $conflicts,
                'room_usage_stats' => $roomUsageStats,
                'total_generated' => count($generatedDetails),
                'total_conflicts' => count($conflicts),
            ];
        });
    }

    /**
     * Clear existing schedule details for a class schedule
     */
    private function clearExistingDetails(ClassSchedule $classSchedule): void
    {
        $classSchedule->details()->delete();
    }

    /**
     * Initialize room usage statistics for tracking
     */
    private function initializeRoomUsageStats(): array
    {
        $rooms = Room::where('availability_status', 'available')->where('is_active', true)->get();
        $stats = [];

        foreach ($rooms as $room) {
            $stats[$room->id] = [
                'room' => $room,
                'usage_count' => 0,
                'last_used' => null,
                'capacity' => $room->capacity,
                'facilities' => $room->facilities,
            ];
        }

        return $stats;
    }

    /**
     * Calculate how many sessions each course needs
     */
    private function calculateCourseSessions(array $courses, Carbon $startDate, Carbon $endDate, array $studyDays): array
    {
        $totalWeeks = $startDate->diffInWeeks($endDate);
        $totalStudyDays = $totalWeeks * count($studyDays);

        $courseSessions = [];
        $totalCredits = collect($courses)->sum('credits');

        foreach ($courses as $courseData) {
            // Get course ID from the course data
            $courseId = $courseData['course_id'];

            // Load the course model from database
            $course = Course::find($courseId);

            if (!$course) {
                Log::warning("Course with ID {$courseId} not found during auto-schedule generation");
                continue;
            }

            // Calculate sessions based on credits (1 credit = 1 session per week typically)
            $sessionsPerWeek = $courseData['credits'] ?? $course->credits ?? 1;
            $totalSessions = $sessionsPerWeek * $totalWeeks;

            // Adjust if course has specific meeting requirements
            if (isset($courseData['meetings_per_week'])) {
                $totalSessions = $courseData['meetings_per_week'] * $totalWeeks;
            }

            $courseSessions[] = [
                'course' => $course,
                'sessions' => min($totalSessions, $totalStudyDays), // Don't exceed available study days
                'credits' => $course->credits ?? 1,
            ];
        }

        // Sort by credits (higher credits first for better scheduling)
        usort($courseSessions, function ($a, $b) {
            return $b['credits'] - $a['credits'];
        });

        return $courseSessions;
    }

    /**
     * Create a single schedule detail
     */
    private function createScheduleDetail(
        ClassSchedule $classSchedule,
        Course $course,
        Carbon $startDate,
        Carbon $endDate,
        array $studyDays,
        int $onlinePercentage,
        array &$roomUsageStats,
        int $sessionIndex,
        int $totalSessions
    ): ?ClassScheduleDetail {
        // Determine if this session should be online
        $isOnline = $this->determineOnlineStatus($sessionIndex, $totalSessions, $onlinePercentage);

        // Find suitable room if offline
        $roomId = null;
        if (!$isOnline) {
            $roomId = $this->selectOptimalRoom($roomUsageStats, $course);
        }

        // Select available lecturer
        $lecturerId = $this->selectAvailableLecturer($course);

        if (!$lecturerId) {
            Log::warning('No available lecturer for course: ' . $course->course_name);
            return null;
        }

        // Calculate session dates
        $sessionDates = $this->generateSessionDates($startDate, $endDate, $studyDays, $sessionIndex, $totalSessions);

        // Determine time slot (can be enhanced with preferences)
        $timeSlot = $this->selectTimeSlot($sessionIndex, $studyDays);

        // Create the detail
        $detail = ClassScheduleDetail::create([
            'class_schedule_id' => $classSchedule->id,
            'course_id' => $course->id,
            'lecturer_id' => $lecturerId,
            'room_id' => $roomId,
            'day_of_week' => $sessionDates['day'],
            'start_time' => $timeSlot['start'],
            'end_time' => $timeSlot['end'],
            'start_date' => $sessionDates['start_date'],
            'end_date' => $sessionDates['end_date'],
            'is_online' => $isOnline,
            'meeting_type' => $isOnline ? 'online' : 'offline',
            'sessions_per_meeting' => 1,
            'total_meetings' => $sessionDates['total_meetings'],
            'notes' => "Auto-generated session " . ($sessionIndex + 1) . " of {$totalSessions}",
        ]);

        return $detail;
    }

    /**
     * Determine if a session should be online based on percentage
     */
    private function determineOnlineStatus(int $sessionIndex, int $totalSessions, int $onlinePercentage): bool
    {
        if ($onlinePercentage === 0) return false;
        if ($onlinePercentage === 100) return true;

        $onlineSessions = ceil(($onlinePercentage / 100) * $totalSessions);
        return $sessionIndex < $onlineSessions;
    }

    /**
     * Select optimal room based on usage statistics and course requirements
     */
    private function selectOptimalRoom(array &$roomUsageStats, Course $course): ?int
    {
        // Filter rooms that meet course requirements
        $suitableRooms = collect($roomUsageStats)->filter(function ($stats) use ($course) {
            // Check if room has adequate capacity
            $room = $stats['room'];

            // Basic capacity check (can be enhanced with course-specific requirements)
            if ($room->capacity < 20) return false; // Minimum capacity

            // Check if room is available
            if ($room->availability_status !== 'available' || !$room->is_active) return false;

            // Additional checks based on course type can be added here
            // For now, we'll skip the lab check since session_type doesn't exist in courses table
            // TODO: Implement proper course type filtering when course types are defined

            return true;
        });

        if ($suitableRooms->isEmpty()) {
            // If no suitable rooms found, just return null and let the system handle it
            Log::warning('No suitable rooms available for course: ' . $course->course_name);
            return null;
        }

        // Sort by usage count (prefer less used rooms for balance)
        $sortedRooms = $suitableRooms->sortBy('usage_count');

        // Get the least used room
        $selectedRoom = $sortedRooms->first();

        return $selectedRoom['room']->id;
    }

    /**
     * Update room usage statistics
     */
    private function updateRoomUsageStats(array &$roomUsageStats, int $roomId): void
    {
        if (isset($roomUsageStats[$roomId])) {
            $roomUsageStats[$roomId]['usage_count']++;
            $roomUsageStats[$roomId]['last_used'] = now();
        }
    }

    /**
     * Select available lecturer for a course
     */
    private function selectAvailableLecturer(Course $course): ?int
    {
        // For now, just get any active lecturer
        // TODO: Implement proper course-lecturer assignment based on expertise or department
        $lecturer = Lecturer::where('status', 'active')
                           ->where('is_active', true)
                           ->first();

        return $lecturer ? $lecturer->id : null;
    }

    /**
     * Generate session dates
     */
    private function generateSessionDates(Carbon $startDate, Carbon $endDate, array $studyDays, int $sessionIndex, int $totalSessions): array
    {
        // Distribute sessions across the study days
        $dayIndex = $sessionIndex % count($studyDays);
        $selectedDay = $studyDays[$dayIndex];

        // Calculate which week this session falls in
        $weekIndex = floor($sessionIndex / count($studyDays));

        // Find the date for this session
        $currentDate = $startDate->copy();
        $foundDate = null;
        $weekCounter = 0;

        // Map Indonesian day names to English
        $dayMap = [
            'senin' => 'monday',
            'selasa' => 'tuesday',
            'rabu' => 'wednesday',
            'kamis' => 'thursday',
            'jumat' => 'friday',
            'sabtu' => 'saturday',
            'minggu' => 'sunday'
        ];

        $englishDay = $dayMap[$selectedDay] ?? $selectedDay;

        while ($currentDate <= $endDate) {
            if (strtolower($currentDate->dayName) === $englishDay) {
                if ($weekCounter === $weekIndex) {
                    $foundDate = $currentDate->copy();
                    break;
                }
                $weekCounter++;
            }
            $currentDate->addDay();
        }

        if (!$foundDate) {
            throw new \Exception("Could not find suitable date for session");
        }

        return [
            'day' => $selectedDay,
            'start_date' => $foundDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_meetings' => 1, // Each detail represents one meeting pattern
        ];
    }

    /**
     * Select time slot for a session
     */
    private function selectTimeSlot(int $sessionIndex, array $studyDays): array
    {
        // Define available time slots
        $timeSlots = [
            ['start' => '07:00', 'end' => '08:40'],
            ['start' => '08:50', 'end' => '10:30'],
            ['start' => '10:40', 'end' => '12:20'],
            ['start' => '13:00', 'end' => '14:40'],
            ['start' => '14:50', 'end' => '16:30'],
            ['start' => '16:40', 'end' => '18:20'],
        ];

        // Select slot based on session index to distribute evenly
        $slotIndex = $sessionIndex % count($timeSlots);

        return $timeSlots[$slotIndex];
    }

    /**
     * Get available courses for auto-scheduling
     */
    public function getAvailableCourses(int $programStudyId): Collection
    {
        return Course::where('program_study_id', $programStudyId)
                    ->where('is_active', true)
                    ->with(['lecturers'])
                    ->get();
    }

    /**
     * Get room usage statistics for reporting
     */
    public function getRoomUsageStatistics(ClassSchedule $classSchedule): array
    {
        $details = $classSchedule->details()->with('room')->get();
        $roomUsage = [];

        foreach ($details as $detail) {
            if ($detail->room_id) {
                $roomId = $detail->room_id;
                if (!isset($roomUsage[$roomId])) {
                    $roomUsage[$roomId] = [
                        'room' => $detail->room,
                        'usage_count' => 0,
                        'online_sessions' => 0,
                        'offline_sessions' => 0,
                    ];
                }

                $roomUsage[$roomId]['usage_count']++;
                if ($detail->is_online) {
                    $roomUsage[$roomId]['online_sessions']++;
                } else {
                    $roomUsage[$roomId]['offline_sessions']++;
                }
            }
        }

        return $roomUsage;
    }
}