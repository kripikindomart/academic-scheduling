<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassSchedule\StoreClassScheduleRequest;
use App\Http\Requests\ClassSchedule\UpdateClassScheduleRequest;
use App\Http\Requests\ClassSchedule\AddCourseToClassScheduleRequest;
use App\Services\ClassScheduleService;
use App\Services\ResponseService;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClassScheduleController extends Controller
{
    protected ClassScheduleService $classScheduleService;

    public function __construct(ClassScheduleService $classScheduleService)
    {
        $this->classScheduleService = $classScheduleService;
    }

    /**
     * Display a listing of class schedules.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'class_id' => $request->input('class_id'),
            'academic_year_id' => $request->input('academic_year_id'),
            'semester' => $request->input('semester'),
            'status' => $request->input('status'),
        ];

        $result = $this->classScheduleService->getClassSchedules(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'created_at'),
            $request->input('sort_direction', 'desc')
        );

        return ResponseService::success($result['data'], $result['message'], $result['meta']);
    }

    /**
     * Store a newly created class schedule.
     */
    public function store(StoreClassScheduleRequest $request): JsonResponse
    {
        try {
            $classSchedule = $this->classScheduleService->createClassSchedule($request->validated());

            return ResponseService::success($classSchedule, 'Class schedule created successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to create class schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Display the specified class schedule.
     */
    public function show(ClassSchedule $classSchedule): JsonResponse
    {
        $classSchedule->load([
            'programStudy:id,name,faculty',
            'schoolClass:id,class_name,class_code',
            'academicYear:id,year,semester',
            'details.course:id,course_code,course_name',
            'details.lecturer:id,name,email',
            'details.room:id,room_code,name',
            'schedules',
            'creator:id,name,email',
            'updater:id,name,email',
        ]);

        return ResponseService::success($classSchedule, 'Class schedule retrieved successfully');
    }

    /**
     * Update the specified class schedule.
     */
    public function update(UpdateClassScheduleRequest $request, ClassSchedule $classSchedule): JsonResponse
    {
        try {
            $updatedClassSchedule = $this->classScheduleService->updateClassSchedule($classSchedule, $request->validated());

            return ResponseService::success($updatedClassSchedule, 'Class schedule updated successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update class schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Remove the specified class schedule.
     */
    public function destroy(ClassSchedule $classSchedule): JsonResponse
    {
        try {
            $this->classScheduleService->deleteClassSchedule($classSchedule);

            return ResponseService::success(null, 'Class schedule deleted successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete class schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get class schedule statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->classScheduleService->getClassScheduleStatistics();

            return ResponseService::success($statistics, 'Class schedule statistics retrieved successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve class schedule statistics: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Add course to class schedule.
     */
    public function addCourse(AddCourseToClassScheduleRequest $request, ClassSchedule $classSchedule): JsonResponse
    {
        try {
            $validated = $request->validated();

            $classScheduleDetail = $this->classScheduleService->addCourseToClassSchedule($classSchedule, $validated);

            return ResponseService::success($classScheduleDetail, 'Course added to class schedule successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to add course to class schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Remove course from class schedule.
     */
    public function removeCourse(ClassSchedule $classSchedule, $detailId): JsonResponse
    {
        try {
            $this->classScheduleService->removeCourseFromClassSchedule($classSchedule, $detailId);

            return ResponseService::success(null, 'Course removed from class schedule successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to remove course from class schedule: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Generate schedules from class schedule.
     */
    public function generateSchedules(ClassSchedule $classSchedule): JsonResponse
    {
        try {
            $result = $this->classScheduleService->generateSchedules($classSchedule);

            return ResponseService::success($result, 'Schedules generated successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to generate schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get generated schedules for class schedule.
     */
    public function getSchedules(ClassSchedule $classSchedule, Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'start_date',
                'end_date',
                'course_id',
                'lecturer_id',
                'status',
            ]);

            $schedules = $this->classScheduleService->getGeneratedSchedules($classSchedule, $filters);

            return ResponseService::success($schedules, 'Generated schedules retrieved successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve generated schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Update class schedule status.
     */
    public function updateStatus(Request $request, ClassSchedule $classSchedule): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:draft,active,completed,cancelled',
                'notes' => 'nullable|string|max:1000',
            ]);

            $updatedClassSchedule = $this->classScheduleService->updateStatus($classSchedule, $validated);

            return ResponseService::success($updatedClassSchedule, 'Class schedule status updated successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update class schedule status: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk update class schedules.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'class_schedule_ids' => 'required|array|min:1',
                'class_schedule_ids.*' => 'required|exists:class_schedules,id',
                'updates' => 'required|array',
                'updates.status' => 'sometimes|in:draft,active,completed,cancelled',
                'updates.online_percentage' => 'sometimes|numeric|min:0|max:100',
                'updates.offline_percentage' => 'sometimes|numeric|min:0|max:100',
            ]);

            $updatedCount = $this->classScheduleService->bulkUpdateClassSchedules(
                $validated['class_schedule_ids'],
                $validated['updates']
            );

            return ResponseService::success(
                ['updated_count' => $updatedCount],
                "Successfully updated {$updatedCount} class schedules"
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk update class schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk delete class schedules.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'class_schedule_ids' => 'required|array|min:1',
                'class_schedule_ids.*' => 'required|exists:class_schedules,id',
            ]);

            $deletedCount = $this->classScheduleService->bulkDeleteClassSchedules($validated['class_schedule_ids']);

            return ResponseService::success(
                ['deleted_count' => $deletedCount],
                "Successfully deleted {$deletedCount} class schedules"
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk delete class schedules: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
}