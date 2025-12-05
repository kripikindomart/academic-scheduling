<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\StoreScheduleRequest;
use App\Http\Requests\Schedule\UpdateScheduleRequest;
use App\Services\ScheduleService;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseService;

class ScheduleController extends Controller
{
    protected ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Display a listing of schedules.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'course_id' => $request->input('course_id'),
            'lecturer_id' => $request->input('lecturer_id'),
            'room_id' => $request->input('room_id'),
            'program_study_id' => $request->input('program_study_id'),
            'class_id' => $request->input('class_id'),
            'semester' => $request->input('semester'),
            'academic_year' => $request->input('academic_year'),
            'status' => $request->input('status'),
            'schedule_type' => $request->input('schedule_type'),
            'session_type' => $request->input('session_type'),
            'is_published' => $request->input('is_published'),
            'is_online' => $request->input('is_online'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'conflict_status' => $request->input('conflict_status'),
        ];

        $result = $this->scheduleService->getSchedules(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'date'),
            $request->input('sort_direction', 'asc')
        );

        return ResponseService::success($result['data'], $result['message'], $result['meta']);
    }

    /**
     * Store a newly created schedule.
     */
    public function store(StoreScheduleRequest $request): JsonResponse
    {
        try {
            $schedule = $this->scheduleService->createSchedule($request->validated());

            return ResponseService::success($schedule, 'Schedule created successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to create schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Display the specified schedule.
     */
    public function show(Schedule $schedule): JsonResponse
    {
        $schedule->load([
            'course',
            'lecturer',
            'room',
            'programStudy',
            'schoolClass',
            'creator',
            'updater',
            'approver',
            'canceller',
        ]);

        return ResponseService::success($schedule, 'Schedule retrieved successfully');
    }

    /**
     * Update the specified schedule.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule): JsonResponse
    {
        try {
            $updatedSchedule = $this->scheduleService->updateSchedule($schedule, $request->validated());

            return ResponseService::success($updatedSchedule, 'Schedule updated successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(Schedule $schedule): JsonResponse
    {
        try {
            $this->scheduleService->deleteSchedule($schedule);

            return ResponseService::success(null, 'Schedule deleted successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get schedule statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->scheduleService->getScheduleStatistics();

            return ResponseService::success($statistics, 'Schedule statistics retrieved successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve schedule statistics: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Check schedule conflicts.
     */
    public function checkConflicts(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'room_id' => 'required|exists:rooms,id',
                'lecturer_id' => 'required|exists:lecturers,id',
                'class_id' => 'nullable|exists:classes,id',
            ]);

            $conflicts = $this->scheduleService->checkConflicts($validated);

            return ResponseService::success($conflicts, 'Conflict check completed');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to check conflicts: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get available rooms for scheduling.
     */
    public function getAvailableRooms(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'min_capacity' => 'nullable|integer|min:1',
            ]);

            $availableRooms = $this->scheduleService->getAvailableRooms(
                $validated['date'],
                $validated['start_time'],
                $validated['end_time'],
                $validated['min_capacity'] ?? 1
            );

            return ResponseService::success($availableRooms, 'Available rooms retrieved successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to get available rooms: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get available lecturers for scheduling.
     */
    public function getAvailableLecturers(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'program_study_id' => 'nullable|exists:program_studies,id',
            ]);

            $availableLecturers = $this->scheduleService->getAvailableLecturers(
                $validated['date'],
                $validated['start_time'],
                $validated['end_time'],
                $validated['program_study_id'] ?? null
            );

            return ResponseService::success($availableLecturers, 'Available lecturers retrieved successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to get available lecturers: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get schedules by date range.
     */
    public function getByDateRange(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'course_id' => 'nullable|exists:courses,id',
                'lecturer_id' => 'nullable|exists:lecturers,id',
                'room_id' => 'nullable|exists:rooms,id',
                'program_study_id' => 'nullable|exists:program_studies,id',
                'status' => 'nullable|in:draft,submitted,approved,rejected,cancelled,completed',
            ]);

            $filters = $validated;
            unset($filters['start_date'], $filters['end_date']);

            $result = $this->scheduleService->getSchedulesByDateRange(
                $validated['start_date'],
                $validated['end_date'],
                $filters
            );

            return ResponseService::success($result['data'], $result['message']);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to get schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get calendar view data.
     */
    public function getCalendarView(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'year' => 'required|integer|min:2020|max:2050',
                'month' => 'required|integer|min:1|max:12',
            ]);

            $result = $this->scheduleService->getCalendarView(
                $validated['year'],
                $validated['month']
            );

            return ResponseService::success($result, 'Calendar view data retrieved successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to get calendar view: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Approve schedule.
     */
    public function approve(Request $request, Schedule $schedule): JsonResponse
    {
        try {
            $validated = $request->validate([
                'approval_notes' => 'nullable|string|max:1000',
            ]);

            $approvedSchedule = $this->scheduleService->approveSchedule(
                $schedule,
                $validated['approval_notes'] ?? null
            );

            return ResponseService::success($approvedSchedule, 'Schedule approved successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to approve schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Reject schedule.
     */
    public function reject(Request $request, Schedule $schedule): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ]);

            $rejectedSchedule = $this->scheduleService->rejectSchedule(
                $schedule,
                $validated['rejection_reason']
            );

            return ResponseService::success($rejectedSchedule, 'Schedule rejected successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to reject schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Cancel schedule.
     */
    public function cancel(Request $request, Schedule $schedule): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cancellation_reason' => 'required|string|max:1000',
            ]);

            $cancelledSchedule = $this->scheduleService->cancelSchedule(
                $schedule,
                $validated['cancellation_reason']
            );

            return ResponseService::success($cancelledSchedule, 'Schedule cancelled successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to cancel schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk update schedules.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'schedule_ids' => 'required|array|min:1',
                'schedule_ids.*' => 'required|exists:schedules,id',
                'updates' => 'required|array',
                'updates.status' => 'sometimes|in:draft,submitted,approved,rejected,cancelled,completed',
                'updates.conflict_status' => 'sometimes|in:none,detected,resolved',
                'updates.is_published' => 'sometimes|boolean',
                'updates.is_locked' => 'sometimes|boolean',
            ]);

            $updatedCount = $this->scheduleService->bulkUpdateSchedules(
                $validated['schedule_ids'],
                $validated['updates']
            );

            return ResponseService::success(
                ['updated_count' => $updatedCount],
                "Successfully updated {$updatedCount} schedules"
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk update schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk delete schedules.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'schedule_ids' => 'required|array|min:1',
                'schedule_ids.*' => 'required|exists:schedules,id',
            ]);

            $deletedCount = $this->scheduleService->bulkDeleteSchedules($validated['schedule_ids']);

            return ResponseService::success(
                ['deleted_count' => $deletedCount],
                "Successfully deleted {$deletedCount} schedules"
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk delete schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Export schedules.
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'course_id',
                'lecturer_id',
                'room_id',
                'program_study_id',
                'semester',
                'academic_year',
                'status',
                'schedule_type',
                'date_from',
                'date_to',
                'format',
            ]);

            $format = $request->input('format', 'csv');
            $filePath = $this->scheduleService->exportSchedules($filters, $format);

            return ResponseService::success(
                ['download_url' => asset($filePath)],
                'Schedules export completed'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to export schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get schedules for specific course.
     */
    public function getByCourse(Request $request, $courseId): JsonResponse
    {
        try {
            $filters = array_merge($request->all(), ['course_id' => $courseId]);

            $result = $this->scheduleService->getSchedules(
                $filters,
                $request->input('per_page', 15),
                $request->input('sort_by', 'date'),
                $request->input('sort_direction', 'asc')
            );

            return ResponseService::success($result['data'], $result['message'], $result['meta']);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to get course schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get schedules for specific lecturer.
     */
    public function getByLecturer(Request $request, $lecturerId): JsonResponse
    {
        try {
            $filters = array_merge($request->all(), ['lecturer_id' => $lecturerId]);

            $result = $this->scheduleService->getSchedules(
                $filters,
                $request->input('per_page', 15),
                $request->input('sort_by', 'date'),
                $request->input('sort_direction', 'asc')
            );

            return ResponseService::success($result['data'], $result['message'], $result['meta']);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to get lecturer schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get schedules for specific room.
     */
    public function getByRoom(Request $request, $roomId): JsonResponse
    {
        try {
            $filters = array_merge($request->all(), ['room_id' => $roomId]);

            $result = $this->scheduleService->getSchedules(
                $filters,
                $request->input('per_page', 15),
                $request->input('sort_by', 'date'),
                $request->input('sort_direction', 'asc')
            );

            return ResponseService::success($result['data'], $result['message'], $result['meta']);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to get room schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get schedule recommendations.
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_id' => 'required|exists:courses,id',
                'class_id' => 'required|exists:classes,id',
                'semester' => 'required|integer|min:1|max:8',
                'academic_year' => 'required|string',
                'preference_time' => 'nullable|array',
                'preference_day' => 'nullable|array',
            ]);

            $recommendations = $this->scheduleService->getScheduleRecommendations($validated);

            return ResponseService::success($recommendations, 'Schedule recommendations retrieved successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to get schedule recommendations: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get schedules by academic year.
     */
    public function getByAcademicYear(Request $request, $academicYear): JsonResponse
    {
        try {
            $filters = array_merge($request->all(), ['academic_year' => $academicYear]);

            $result = $this->scheduleService->getSchedules(
                $filters,
                $request->input('per_page', 15),
                $request->input('sort_by', 'date'),
                $request->input('sort_direction', 'asc')
            );

            return ResponseService::success($result['data'], $result['message'], $result['meta']);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to get academic year schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get today's schedules.
     */
    public function getTodaySchedules(Request $request): JsonResponse
    {
        try {
            $filters = array_merge($request->all(), [
                'date_from' => now()->format('Y-m-d'),
                'date_to' => now()->format('Y-m-d')
            ]);

            $result = $this->scheduleService->getSchedules(
                $filters,
                $request->input('per_page', 50),
                $request->input('sort_by', 'start_time'),
                $request->input('sort_direction', 'asc')
            );

            return ResponseService::success($result['data'], $result['message'], $result['meta']);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to get today\'s schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Batch create schedules.
     */
    public function createBatch(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'schedules' => 'required|array|min:1|max:100',
                'schedules.*.course_id' => 'required|exists:courses,id',
                'schedules.*.lecturer_id' => 'required|exists:lecturers,id',
                'schedules.*.room_id' => 'required|exists:rooms,id',
                'schedules.*.class_id' => 'nullable|exists:classes,id',
                'schedules.*.date' => 'required|date|after_or_equal:today',
                'schedules.*.start_time' => 'required|date_format:H:i',
                'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
                'schedules.*.semester' => 'required|integer|min:1|max:8',
                'schedules.*.academic_year' => 'required|string',
            ]);

            $result = $this->scheduleService->batchCreateSchedules($validated['schedules']);

            return ResponseService::success($result, 'Batch schedule creation completed');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to create batch schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Import schedules.
     */
    public function import(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
                'academic_year' => 'required|string',
                'semester' => 'required|integer|min:1|max:8',
            ]);

            $result = $this->scheduleService->importSchedules(
                $validated['file'],
                $validated['academic_year'],
                $validated['semester']
            );

            return ResponseService::success($result, 'Schedules import completed');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to import schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
}
