<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Services\ResponseService;
use App\Services\CourseService;
use App\Exports\CoursesTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Exports\LecturersTemplateExport;

class CourseController extends Controller
{
    protected CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Apply program study filter for non-admin users
            if ($user && !$user->isAdmin() && $user->program_study_id) {
                $request->merge(['program_study_id' => $user->program_study_id]);
            }

            $filters = $request->only([
                'program_study_id',
                'semester',
                'course_type',
                'level',
                'is_active',
                'search'
            ]);

            $perPage = $request->get('per_page', 20);
            $courses = $this->courseService->getAllCourses($perPage, $filters);

            return ResponseService::paginated(
                $courses,
                'Courses retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve courses: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request): JsonResponse
    {
        try {
            $course = $this->courseService->createCourse($request->validated());

            return ResponseService::success(
                $course,
                'Course created successfully',
                201
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to create course: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): JsonResponse
    {
        try {
            $course->load([
                'programStudy',
                'creator',
                'lecturers',
                'prerequisites',
                'schedules'
            ]);

            return ResponseService::success(
                $course,
                'Course retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve course: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course): JsonResponse
    {
        try {
            $updatedCourse = $this->courseService->updateCourse($course->id, $request->validated());

            return ResponseService::success(
                $updatedCourse,
                'Course updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update course: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): JsonResponse
    {
        try {
            $this->courseService->deleteCourse($course->id);

            return ResponseService::success(
                null,
                'Course deleted successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete course: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get available courses for enrollment.
     */
    public function available(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'program_study_id',
                'semester',
                'academic_year',
                'level'
            ]);

            $courses = $this->courseService->getAvailableCourses($filters);

            return ResponseService::success(
                $courses,
                'Available courses retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve available courses: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Add prerequisite to course.
     */
    public function addPrerequisite(Request $request, Course $course): JsonResponse
    {
        try {
            $validated = $request->validate([
                'prerequisite_course_id' => 'required|exists:courses,id|different:course',
            ]);

            $this->courseService->addPrerequisite($course->id, $validated['prerequisite_course_id']);

            return ResponseService::success(
                null,
                'Prerequisite added successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to add prerequisite: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Remove prerequisite from course.
     */
    public function removePrerequisite(Request $request, Course $course): JsonResponse
    {
        try {
            $validated = $request->validate([
                'prerequisite_course_id' => 'required|exists:courses,id',
            ]);

            $this->courseService->removePrerequisite($course->id, $validated['prerequisite_course_id']);

            return ResponseService::success(
                null,
                'Prerequisite removed successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to remove prerequisite: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get course statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->courseService->getCourseStatistics();

            return ResponseService::success(
                $stats,
                'Course statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve course statistics: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk update courses.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_ids' => 'required|array|min:1',
                'course_ids.*' => 'required|integer|exists:courses,id',
                'updates' => 'required|array|min:1',
                'updates.*' => 'string'
            ]);

            $result = $this->courseService->bulkUpdateCourses($validated['course_ids'], $validated['updates']);

            return ResponseService::success(
                $result,
                'Courses updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk update courses: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk delete courses.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_ids' => 'required|array|min:1',
                'course_ids.*' => 'required|integer|exists:courses,id'
            ]);

            $result = $this->courseService->bulkDeleteCourses($validated['course_ids']);

            return ResponseService::success(
                $result,
                'Courses deleted successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk delete courses: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk activate/deactivate courses.
     */
    public function bulkToggleStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_ids' => 'required|array|min:1',
                'course_ids.*' => 'required|integer|exists:courses,id',
                'is_active' => 'required|boolean'
            ]);

            $result = $this->courseService->bulkToggleCourseStatus($validated['course_ids'], $validated['is_active']);

            return ResponseService::success(
                $result,
                'Course status updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk toggle course status: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Duplicate a course.
     */
    public function duplicate(Request $request, Course $course): JsonResponse
    {
        try {
            $user = $request->user();

            // Only admin can duplicate courses
            if (!$user || !$user->isAdmin()) {
                return ResponseService::error(
                    'Unauthorized. Only admin can duplicate courses.',
                    null,
                    403
                );
            }

            $duplicatedCourse = $this->courseService->duplicateCourse($course->id, $user->id);

            return ResponseService::success(
                $duplicatedCourse,
                'Course duplicated successfully',
                201
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to duplicate course: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Toggle active status for a course.
     */
    public function toggleStatus(Request $request, Course $course): JsonResponse
    {
        try {
            $user = $request->user();

            // Only admin can toggle course status
            if (!$user || !$user->isAdmin()) {
                return ResponseService::error(
                    'Unauthorized. Only admin can toggle course status.',
                    null,
                    403
                );
            }

            $updatedCourse = $this->courseService->toggleCourseStatus($course->id, $user->id);

            return ResponseService::success(
                $updatedCourse,
                'Course status updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to toggle course status: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Restore a soft-deleted course.
     */
    public function restore(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();

            // Only admin can restore courses
            if (!$user || !$user->isAdmin()) {
                return ResponseService::error(
                    'Unauthorized. Only admin can restore courses.',
                    null,
                    403
                );
            }

            $course = Course::withTrashed()->findOrFail($id);
            $course->restore();

            return ResponseService::success(
                $course,
                'Course restored successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to restore course: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Force delete a course.
     */
    public function forceDelete(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();

            // Only admin can force delete courses
            if (!$user || !$user->isAdmin()) {
                return ResponseService::error(
                    'Unauthorized. Only admin can permanently delete courses.',
                    null,
                    403
                );
            }

            $course = Course::withTrashed()->findOrFail($id);
            $course->forceDelete();

            return ResponseService::success(
                null,
                'Course permanently deleted'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to permanently delete course: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Import courses from file.
     */
    public function import(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
            ]);

            $result = $this->courseService->importCourses($validated['file']);

            return ResponseService::success(
                $result,
                'Courses imported successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to import courses: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Export courses to file.
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'program_study_id',
                'semester',
                'academic_year',
                'course_type',
                'level',
                'is_active',
                'format'
            ]);

            $file = $this->courseService->exportCourses($filters);

            return response()->download($file, 'courses.' . ($filters['format'] ?? 'xlsx'));
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to export courses: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Download template for import
     */
   public function downloadTemplate()
    {
        try {
            $filename = 'template-import-mata-kuliah-' . date('Y-m-d') . '.xlsx';

            // Create the export object
            $export = new CoursesTemplateExport();

            // Generate and get the file content
            $fileContent = Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);

            // Set headers for Excel download
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            return response($fileContent, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Template download failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trashed courses.
     */
    public function trash(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Apply program study filter for non-admin users
            if ($user && !$user->isAdmin() && $user->program_study_id) {
                $request->merge(['program_study_id' => $user->program_study_id]);
            }

            $filters = $request->only([
                'program_study_id',
                'semester',
                'course_type',
                'level',
                'search'
            ]);

            $perPage = $request->get('per_page', 20);
            $courses = $this->courseService->getTrashedCourses($perPage, $filters);

            return ResponseService::paginated(
                $courses,
                'Trashed courses retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve trashed courses: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
}
