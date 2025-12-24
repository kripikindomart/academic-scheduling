<?php

namespace App\Services;

use App\Models\ClassSchedule;
use App\Models\ClassScheduleDetail;
use App\Models\Schedule;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Room;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClassScheduleService
{
    /**
     * Get paginated class schedules with filtering and search capabilities.
     *
     * @param array $filters
     * @param int $perPage
     * @param string $sortBy
     * @param string $sortDirection
     * @return array
     */
    public function getClassSchedules(array $filters = [], int $perPage = 15, string $sortBy = 'created_at', string $sortDirection = 'desc'): array
    {
        $query = ClassSchedule::with([
            'programStudy:id,name',
            'schoolClass:id,name,code,batch_year,academic_year',
            'academicYear:id,academic_calendar_year,admission_period',
            'creator:id,name,email',
        ]);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply sorting
        $allowedSorts = ['created_at', 'updated_at', 'title', 'status', 'semester', 'academic_year'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at';
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

        $classSchedules = $query->orderBy($sortBy, $sortDirection)
                               ->paginate($perPage);

        return [
            'data' => $classSchedules->items(),
            'message' => 'Class schedules retrieved successfully',
            'meta' => [
                'current_page' => $classSchedules->currentPage(),
                'last_page' => $classSchedules->lastPage(),
                'per_page' => $classSchedules->perPage(),
                'total' => $classSchedules->total(),
                'from' => $classSchedules->firstItem(),
                'to' => $classSchedules->lastItem(),
            ],
        ];
    }

    /**
     * Create a new class schedule.
     *
     * @param array $data
     * @return ClassSchedule
     */
    public function createClassSchedule(array $data): ClassSchedule
    {
        return DB::transaction(function () use ($data) {
            // Generate unique schedule code
            $scheduleCode = $this->generateClassScheduleCode($data);

            $classSchedule = ClassSchedule::create(array_merge($data, [
                'schedule_code' => $scheduleCode,
                'status' => 'draft',
                'created_by' => auth('sanctum')->id(),
                'updated_by' => auth('sanctum')->id(),
                'created_from_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]));

            // Log activity
            Log::channel('activity')->info('Class schedule created', [
                'class_schedule_id' => $classSchedule->id,
                'schedule_code' => $classSchedule->schedule_code,
                'title' => $classSchedule->title,
                'program_study' => $classSchedule->programStudy->name,
                'class' => $classSchedule->schoolClass->name,
                'user_id' => auth('sanctum')->id(),
            ]);

            return $classSchedule;
        });
    }

    /**
     * Update an existing class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @param array $data
     * @return ClassSchedule
     */
    public function updateClassSchedule(ClassSchedule $classSchedule, array $data): ClassSchedule
    {
        return DB::transaction(function () use ($classSchedule, $data) {
            $originalData = $classSchedule->only([
                'title', 'status', 'online_percentage', 'offline_percentage'
            ]);

            $classSchedule->update(array_merge($data, [
                'updated_by' => auth('sanctum')->id(),
            ]));

            // Log significant changes
            $this->logClassScheduleChanges($classSchedule, $originalData);

            // Log activity
            Log::channel('activity')->info('Class schedule updated', [
                'class_schedule_id' => $classSchedule->id,
                'schedule_code' => $classSchedule->schedule_code,
                'changes' => array_keys($data),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $classSchedule->fresh();
        });
    }

    /**
     * Delete a class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @return bool
     */
    public function deleteClassSchedule(ClassSchedule $classSchedule): bool
    {
        return DB::transaction(function () use ($classSchedule) {
            $deleted = $classSchedule->delete();

            if ($deleted) {
                Log::channel('activity')->warning('Class schedule deleted', [
                    'class_schedule_id' => $classSchedule->id,
                    'schedule_code' => $classSchedule->schedule_code,
                    'title' => $classSchedule->title,
                    'user_id' => auth('sanctum')->id(),
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Get class schedule statistics.
     *
     * @return array
     */
    public function getClassScheduleStatistics(): array
    {
        $stats = [
            'total_class_schedules' => ClassSchedule::count(),
            'active_class_schedules' => ClassSchedule::where('status', 'active')->count(),
            'draft_class_schedules' => ClassSchedule::where('status', 'draft')->count(),
            'completed_class_schedules' => ClassSchedule::where('status', 'completed')->count(),
            'cancelled_class_schedules' => ClassSchedule::where('status', 'cancelled')->count(),
        ];

        // This week class schedules
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $stats['this_week'] = ClassSchedule::whereBetween('created_at', [$weekStart, $weekEnd])->count();

        // This month class schedules
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $stats['this_month'] = ClassSchedule::whereBetween('created_at', [$monthStart, $monthEnd])->count();

        return $stats;
    }

    /**
     * Add course to class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @param array $data
     * @return ClassScheduleDetail
     */
    public function addCourseToClassSchedule(ClassSchedule $classSchedule, array $data): ClassScheduleDetail
    {
        return DB::transaction(function () use ($classSchedule, $data) {
            // Convert day name to lowercase for consistency
            $dayOfWeek = strtolower($data['day']);

            // Use frontend's total_meetings if provided, otherwise calculate from date range
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $totalMeetings = isset($data['total_meetings']) && $data['total_meetings'] > 0 
                ? (int) $data['total_meetings'] 
                : $this->calculateTotalMeetings($startDate, $endDate, $dayOfWeek);

            // Prepare data for creation
            $createData = [
                'class_schedule_id' => $classSchedule->id,
                'course_id' => $data['course_id'],
                'day_of_week' => $dayOfWeek,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'total_meetings' => $totalMeetings,
                'sessions_per_meeting' => $data['sessions_per_meeting'] ?? 1,
                'is_online' => $data['is_online'] ?? false,
                'meeting_type' => $data['meeting_type'] ?? 'lecture',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth('sanctum')->id(),
                'updated_by' => auth('sanctum')->id(),
            ];

            $classScheduleDetail = ClassScheduleDetail::create($createData);

            // Sync all lecturers to pivot table (many-to-many)
            if (isset($data['lecturer_ids']) && is_array($data['lecturer_ids'])) {
                $lecturersSync = [];
                foreach ($data['lecturer_ids'] as $index => $lecturerId) {
                    $lecturersSync[$lecturerId] = ['is_primary' => $index === 0];
                }
                $classScheduleDetail->lecturers()->sync($lecturersSync);
            }

            // Sync all rooms to pivot table (many-to-many)
            if (isset($data['room_ids']) && is_array($data['room_ids'])) {
                $roomsSync = [];
                foreach ($data['room_ids'] as $index => $roomId) {
                    $roomsSync[$roomId] = ['is_primary' => $index === 0];
                }
                $classScheduleDetail->rooms()->sync($roomsSync);
            }

            // Log activity
            Log::channel('activity')->info('Course added to class schedule', [
                'class_schedule_id' => $classSchedule->id,
                'class_schedule_detail_id' => $classScheduleDetail->id,
                'course_id' => $data['course_id'],
                'lecturer_ids' => $data['lecturer_ids'] ?? [],
                'room_ids' => $data['room_ids'] ?? [],
                'user_id' => auth('sanctum')->id(),
            ]);

            return $classScheduleDetail->load(['course', 'lecturer', 'room', 'lecturers', 'rooms']);
        });
    }

    /**
     * Remove course from class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @param int $detailId
     * @return bool
     */
    public function removeCourseFromClassSchedule(ClassSchedule $classSchedule, int $detailId): bool
    {
        return DB::transaction(function () use ($classSchedule, $detailId) {
            $detail = ClassScheduleDetail::where('class_schedule_id', $classSchedule->id)
                                        ->where('id', $detailId)
                                        ->firstOrFail();

            $deleted = $detail->delete();

            if ($deleted) {
                Log::channel('activity')->warning('Course removed from class schedule', [
                    'class_schedule_id' => $classSchedule->id,
                    'class_schedule_detail_id' => $detailId,
                    'user_id' => auth('sanctum')->id(),
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Generate schedules from class schedule.
     * Uses online_percentage and offline_percentage to determine session types.
     * Rotates rooms for offline sessions from the available rooms in detail.
     *
     * @param ClassSchedule $classSchedule
     * @return array
     */
    public function generateSchedules(ClassSchedule $classSchedule): array
    {
        return DB::transaction(function () use ($classSchedule) {
            $generatedSchedules = [];
            $conflicts = [];

            // Get online/offline percentages from class schedule
            $onlinePercentage = $classSchedule->online_percentage ?? 0;
            $offlinePercentage = $classSchedule->offline_percentage ?? 100;

            // 1. Identification of Locked Schedules
            // We map locked schedules by detail_id and meeting_number to avoid regenerating them
            $lockedSchedules = Schedule::where('class_schedule_id', $classSchedule->id)
                ->where('is_locked', true)
                ->get()
                ->groupBy('class_schedule_detail_id')
                ->map(function ($items) {
                    return $items->keyBy('meeting_number');
                });

            // 2. Clean up existing non-locked schedules
            // We force delete to avoid unique constraint violations on schedule_code
            Schedule::where('class_schedule_id', $classSchedule->id)
                ->where('is_locked', false)
                ->forceDelete();

            foreach ($classSchedule->details as $detail) {
                $scheduleDates = $this->generateScheduleDates($detail);
                $totalMeetings = count($scheduleDates);
                
                // Determine total sessions per meeting based on total meetings count
                // 8 pertemuan = 2 sesi per pertemuan, 16 pertemuan = 1 sesi per pertemuan
                $sessionsPerMeeting = $this->getSessionsPerMeeting($totalMeetings);
                
                // Calculate how many sessions should be online vs offline
                $onlineMeetings = (int) round($totalMeetings * ($onlinePercentage / 100));
                $offlineMeetings = $totalMeetings - $onlineMeetings;
                
                // Get rooms for this detail (for offline sessions)
                $rooms = $detail->rooms->pluck('id')->toArray();
                $roomCount = count($rooms);
                
                // Get lecturers for this detail
                // Sort by 'is_primary' (descending) so the first input lecturer (Primary) gets the first block of meetings
                $lecturerIds = $detail->lecturers->sortByDesc('pivot.is_primary')->pluck('id')->values()->toArray();

                foreach ($scheduleDates as $index => $date) {
                    try {
                        // Meeting number is the index + 1 (1-indexed)
                        $meetingNumber = $index + 1;

                        // Skip if schedule is locked
                        if (isset($lockedSchedules[$detail->id][$meetingNumber])) {
                             $generatedSchedules[] = $lockedSchedules[$detail->id][$meetingNumber];
                             continue;
                        }

                        $sessionNumber = 1; // Always session 1 when generating
                        
                        // Determine if this session is online or offline
                        $isOnline = $index >= $offlineMeetings;
                        
                        // For offline sessions, rotate rooms
                        $currentRoomId = null;
                        if (!$isOnline && $roomCount > 0) {
                            $roomIndex = $index % $roomCount;
                            $currentRoomId = $rooms[$roomIndex];
                        }

                        // Determine primary lecturer using Sequential Block Distribution
                        $lecturerCount = count($lecturerIds);
                        $primaryLecturerId = null;

                        if ($lecturerCount > 0) {
                            $baseAllocation = floor($totalMeetings / $lecturerCount);
                            $remainder = $totalMeetings % $lecturerCount;
                            
                            $accumulated = 0;
                            foreach($lecturerIds as $idx => $lid) {
                                $allocation = $baseAllocation + ($idx < $remainder ? 1 : 0);
                                
                                if ($index < ($accumulated + $allocation)) {
                                    $primaryLecturerId = $lid;
                                    break;
                                }
                                $accumulated += $allocation;
                            }
                        }
                        
                        $schedule = Schedule::create([
                            'class_schedule_id' => $classSchedule->id,
                            'class_schedule_detail_id' => $detail->id,
                            'schedule_code' => $this->generateScheduleCode($classSchedule, $detail, $meetingNumber),
                            'title' => $detail->course->course_name,
                            'description' => "Generated from class schedule: {$classSchedule->title}" . 
                                           ($isOnline ? " (Online)" : " (Offline)"),
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $detail->start_time,
                            'end_time' => $detail->end_time,
                            'day_of_week' => $detail->day_of_week,
                            'duration_minutes' => $detail->getDurationInMinutes(),
                            'schedule_type' => $detail->meeting_type,
                            'is_recurring' => false,
                            'course_id' => $detail->course_id,
                            'program_study_id' => $classSchedule->program_study_id,
                            'class_id' => $classSchedule->class_id,
                            'lecturer_id' => $primaryLecturerId,
                            'room_id' => $currentRoomId,
                            'academic_year' => $classSchedule->academicYear->academic_calendar_year ?? '',
                            'meeting_number' => $meetingNumber,
                            'session_number' => $sessionNumber,
                            'total_sessions' => $sessionsPerMeeting, // 2 for 8 meetings, 1 for 16+
                            'status' => 'approved',
                            'conflict_status' => 'none',
                            'session_type' => $this->determineSessionType($meetingNumber, $totalMeetings),
                            'is_mandatory' => true,
                            'is_online' => $isOnline,
                            'meeting_link' => $isOnline ? '' : null,
                            'is_published' => true,
                            'is_locked' => false,
                            'created_by' => auth('sanctum')->id(),
                            'updated_by' => auth('sanctum')->id(),
                            'created_from_ip' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);

                        // Sync lecturer
                        if ($primaryLecturerId) {
                            $schedule->lecturers()->sync([
                                $primaryLecturerId => ['is_primary' => true]
                            ]);
                        }

                        // Sync room
                        if (!$isOnline && $currentRoomId) {
                            $schedule->rooms()->sync([
                                $currentRoomId => ['is_primary' => true]
                            ]);
                        }

                        $generatedSchedules[] = $schedule;

                    } catch (\Exception $e) {
                        $conflicts[] = [
                            'detail_id' => $detail->id,
                            'course_name' => $detail->course->course_name,
                            'date' => $date->format('Y-m-d'),
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }

            // Update class schedule status
            $classSchedule->update([
                'status' => empty($conflicts) ? 'active' : 'active',
                'updated_by' => auth('sanctum')->id(),
            ]);

            // Log activity
            Log::channel('activity')->info('Schedules generated from class schedule', [
                'class_schedule_id' => $classSchedule->id,
                'total_generated' => count($generatedSchedules),
                'total_conflicts' => count($conflicts),
                'online_percentage' => $onlinePercentage,
                'offline_percentage' => $offlinePercentage,
                'user_id' => auth('sanctum')->id(),
            ]);

            return [
                'generated_schedules' => $generatedSchedules,
                'conflicts' => $conflicts,
                'total_generated' => count($generatedSchedules),
                'total_conflicts' => count($conflicts),
                'online_count' => collect($generatedSchedules)->where('is_online', true)->count(),
                'offline_count' => collect($generatedSchedules)->where('is_online', false)->count(),
            ];
        });
    }

    /**
     * Get generated schedules for class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @param array $filters
     * @return array
     */
    public function getGeneratedSchedules(ClassSchedule $classSchedule, array $filters = []): array
    {
        $query = Schedule::where('class_schedule_id', $classSchedule->id)
                        ->with(['course:id,course_code,course_name', 'lecturer:id,name', 'room:id,room_code']);

        // Apply filters
        if (!empty($filters['start_date'])) {
            $query->whereDate('date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('date', '<=', $filters['end_date']);
        }

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (!empty($filters['lecturer_id'])) {
            $query->where('lecturer_id', $filters['lecturer_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $schedules = $query->orderBy('date')->orderBy('start_time')->get();

        return [
            'data' => $schedules,
            'total' => $schedules->count(),
        ];
    }

    /**
     * Update class schedule status.
     *
     * @param ClassSchedule $classSchedule
     * @param array $data
     * @return ClassSchedule
     */
    public function updateStatus(ClassSchedule $classSchedule, array $data): ClassSchedule
    {
        return DB::transaction(function () use ($classSchedule, $data) {
            $classSchedule->update(array_merge($data, [
                'updated_by' => auth('sanctum')->id(),
            ]));

            // Log activity
            Log::channel('activity')->info('Class schedule status updated', [
                'class_schedule_id' => $classSchedule->id,
                'old_status' => $classSchedule->getOriginal('status'),
                'new_status' => $data['status'],
                'user_id' => auth('sanctum')->id(),
            ]);

            return $classSchedule->fresh();
        });
    }

    /**
     * Bulk update class schedules.
     *
     * @param array $classScheduleIds
     * @param array $updates
     * @return int
     */
    public function bulkUpdateClassSchedules(array $classScheduleIds, array $updates): int
    {
        return DB::transaction(function () use ($classScheduleIds, $updates) {
            $classSchedules = ClassSchedule::whereIn('id', $classScheduleIds)->get();
            $updatedCount = 0;

            foreach ($classSchedules as $classSchedule) {
                try {
                    $classSchedule->update(array_merge($updates, [
                        'updated_by' => auth('sanctum')->id(),
                    ]));
                    $updatedCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to update class schedule in bulk operation', [
                        'class_schedule_id' => $classSchedule->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Log activity
            Log::channel('activity')->info('Class schedules bulk updated', [
                'updated_count' => $updatedCount,
                'total_requested' => count($classScheduleIds),
                'updates' => array_keys($updates),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $updatedCount;
        });
    }

    /**
     * Bulk delete class schedules.
     *
     * @param array $classScheduleIds
     * @return int
     */
    public function bulkDeleteClassSchedules(array $classScheduleIds): int
    {
        return DB::transaction(function () use ($classScheduleIds) {
            $classSchedules = ClassSchedule::whereIn('id', $classScheduleIds)->get();
            $deletedCount = 0;

            foreach ($classSchedules as $classSchedule) {
                try {
                    $classSchedule->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to delete class schedule in bulk operation', [
                        'class_schedule_id' => $classSchedule->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Log activity
            Log::channel('activity')->warning('Class schedules bulk deleted', [
                'deleted_count' => $deletedCount,
                'total_requested' => count($classScheduleIds),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $deletedCount;
        });
    }

    /**
     * Generate class schedule code.
     *
     * @param array $data
     * @return string
     */
    private function generateClassScheduleCode(array $data): string
    {
        $prefix = 'CSCH';
        $date = date('Ymd');
        $programCode = $data['program_study_id'] ? substr($data['program_study_id'], -3) : 'GEN';
        $random = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));

        return "{$prefix}-{$date}-{$programCode}-{$random}";
    }

    /**
     * Generate schedule code.
     *
     * @param ClassSchedule $classSchedule
     * @param ClassScheduleDetail $detail
     * @param int $meetingNumber
     * @return string
     */
    private function generateScheduleCode(ClassSchedule $classSchedule, ClassScheduleDetail $detail, int $meetingNumber): string
    {
        $prefix = 'SCH';
        $date = date('Ymd');
        $courseCode = $detail->course->course_code ?? 'GEN';
        $meetingNum = str_pad($meetingNumber, 2, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$courseCode}-{$meetingNum}";
    }

    /**
     * Apply filters to the query.
     *
     * @param mixed $query
     * @param array $filters
     * @return void
     */
    private function applyFilters($query, array $filters): void
    {
        foreach ($filters as $key => $value) {
            if (empty($value)) continue;

            switch ($key) {
                case 'search':
                    $query->where('title', 'like', "%{$value}%")
                          ->orWhere('schedule_code', 'like', "%{$value}%");
                    break;
                case 'program_study_id':
                    $query->where('program_study_id', $value);
                    break;
                case 'class_id':
                    $query->where('class_id', $value);
                    break;
                case 'academic_year_id':
                    $query->where('academic_year_id', $value);
                    break;
                case 'semester':
                    $query->where('semester', $value);
                    break;
                case 'status':
                    $query->where('status', $value);
                    break;
                // Ignore unsupported filters like 'day' and 'room_id' for ClassSchedule
                case 'day':
                case 'room_id':
                    // These filters are not applicable to ClassSchedule queries
                    break;
            }
        }
    }

    /**
     * Calculate total meetings between dates for specific day of week.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $dayOfWeek
     * @return int
     */
    private function calculateTotalMeetings(Carbon $startDate, Carbon $endDate, string $dayOfWeek): int
    {
        $meetings = 0;
        $current = $startDate->copy();

        while ($current <= $endDate) {
            if (strtolower($current->dayName) === $dayOfWeek) {
                $meetings++;
            }
            $current->addDay();
        }

        return $meetings;
    }

    /**
     * Generate schedule dates based on class schedule detail.
     *
     * @param ClassScheduleDetail $detail
     * @return array
     */
    private function generateScheduleDates(ClassScheduleDetail $detail): array
    {
        $dates = [];
        
        // Handle null dates
        if (!$detail->start_date || !$detail->end_date) {
            Log::warning('generateScheduleDates: Missing start_date or end_date', [
                'detail_id' => $detail->id,
                'start_date' => $detail->start_date,
                'end_date' => $detail->end_date,
            ]);
            return $dates;
        }
        
        $startDate = Carbon::parse($detail->start_date);
        $endDate = Carbon::parse($detail->end_date);
        $meetingCount = 0;
        
        // Map Indonesian day names to English (Carbon uses English)
        $dayMapping = [
            'senin' => 'monday',
            'selasa' => 'tuesday',
            'rabu' => 'wednesday',
            'kamis' => 'thursday',
            'jumat' => 'friday',
            'sabtu' => 'saturday',
            'minggu' => 'sunday',
        ];
        
        // Convert Indonesian day to English for comparison
        $targetDay = strtolower($detail->day_of_week);
        $englishDay = $dayMapping[$targetDay] ?? $targetDay;

        $current = $startDate->copy();
        while ($current <= $endDate && $meetingCount < $detail->total_meetings) {
            if (strtolower($current->dayName) === $englishDay) {
                $dates[] = $current->copy();
                $meetingCount++;
            }
            $current->addDay();
        }
        
        Log::info('generateScheduleDates result', [
            'detail_id' => $detail->id,
            'day_of_week' => $detail->day_of_week,
            'target_day' => $targetDay,
            'english_day' => $englishDay,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'total_meetings' => $detail->total_meetings,
            'dates_generated' => count($dates),
        ]);

        return $dates;
    }

    /**
     * Log significant class schedule changes.
     *
     * @param ClassSchedule $classSchedule
     * @param array $originalData
     * @return void
     */
    private function logClassScheduleChanges(ClassSchedule $classSchedule, array $originalData): void
    {
        $changes = [];

        foreach ($originalData as $key => $oldValue) {
            $newValue = $classSchedule->{$key};
            if ($oldValue != $newValue) {
                $changes[] = [
                    'field' => $key,
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            Log::channel('activity')->info('Class schedule fields changed', [
                'class_schedule_id' => $classSchedule->id,
                'changes' => $changes,
                'user_id' => auth('sanctum')->id(),
            ]);
        }
    }

    /**
     * Determine session type based on meeting number.
     * Schema:
     * - For 8 meetings: Meeting 4 = UTS, Meeting 8 = UAS, rest = Kuliah
     * - For 4 meetings: Meeting 4 = UTS (acts as final), rest = Kuliah
     * - For 1 meeting: Kuliah
     * - For other counts: UTS at midpoint, UAS at last meeting
     *
     * @param int $meetingNumber 1-indexed meeting number
     * @param int $totalMeetings Total meetings for this course
     * @return string 'kuliah', 'uts', or 'uas'
     */
    private function determineSessionType(int $meetingNumber, int $totalMeetings): string
    {
        // For single meeting, always kuliah
        if ($totalMeetings <= 1) {
            return 'kuliah';
        }

        // For 4 meetings: Meeting 4 = UTS (no UAS)
        if ($totalMeetings == 4) {
            if ($meetingNumber == 4) {
                return 'uts';
            }
            return 'kuliah';
        }

        // For 8 meetings (standard): Meeting 4 = UTS, Meeting 8 = UAS
        if ($totalMeetings == 8) {
            if ($meetingNumber == 4) {
                return 'uts';
            }
            if ($meetingNumber == 8) {
                return 'uas';
            }
            return 'kuliah';
        }

        // For other meeting counts:
        // UTS at midpoint (or closest to middle for odd counts)
        // UAS at last meeting
        $midpoint = (int) ceil($totalMeetings / 2);
        
        if ($meetingNumber == $midpoint) {
            return 'uts';
        }
        if ($meetingNumber == $totalMeetings) {
            return 'uas';
        }
        
        return 'kuliah';
    }

    /**
     * Determine sessions per meeting based on total meetings count.
     * - 8 pertemuan = 2 sesi per pertemuan
     * - 16 pertemuan = 1 sesi per pertemuan
     *
     * @param int $totalMeetings Total meetings
     * @return int Number of sessions per meeting
     */
    private function getSessionsPerMeeting(int $totalMeetings): int
    {
        // 8 pertemuan or less: 2 sessions per meeting
        if ($totalMeetings <= 8) {
            return 2;
        }
        
        // 16+ pertemuan: 1 session per meeting
        return 1;
    }
}