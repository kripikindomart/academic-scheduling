<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lecturer\StoreLecturerRequest;
use App\Http\Requests\Lecturer\UpdateLecturerRequest;
use App\Services\LecturerService;
use App\Models\Lecturer;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LecturerController extends Controller
{
    protected LecturerService $lecturerService;

    public function __construct(LecturerService $lecturerService)
    {
        $this->lecturerService = $lecturerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'status' => $request->input('status'),
            'employment_type' => $request->input('employment_type'),
            'faculty' => $request->input('faculty'),
            'department' => $request->input('department'),
            'rank' => $request->input('rank'),
            'highest_education' => $request->input('highest_education'),
            'is_active' => $request->input('is_active'),
            'specialization' => $request->input('specialization'),
        ];

        $result = $this->lecturerService->getLecturers(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'name'),
            $request->input('sort_direction', 'asc')
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['data'],
            'meta' => $result['meta'] ?? []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Handle file upload and regular form data
        $data = $request->all();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = $file->store('lecturers/photos', 'public');
            $data['photo'] = $photoPath;
        }

        $lecturer = $this->lecturerService->createLecturer($data);

        return response()->json([
            'success' => true,
            'message' => 'Lecturer created successfully',
            'data' => $lecturer
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lecturer $lecturer): JsonResponse
    {
        $lecturer->load([
            'programStudy',
            'user',
            'courses',
            'creator',
            'updater'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lecturer retrieved successfully',
            'data' => $lecturer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lecturer $lecturer): JsonResponse
    {
        // Handle file upload and regular form data
        $data = $request->all();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = $file->store('lecturers/photos', 'public');
            $data['photo'] = $photoPath;
        }

        $updatedLecturer = $this->lecturerService->updateLecturer($lecturer, $data);

        return response()->json([
            'success' => true,
            'message' => 'Lecturer updated successfully',
            'data' => $updatedLecturer
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lecturer $lecturer): JsonResponse
    {
        $this->lecturerService->deleteLecturer($lecturer);

        return response()->json([
            'success' => true,
            'message' => 'Lecturer deleted successfully'
        ]);
    }

    /**
     * Get lecturer statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $statistics = $this->lecturerService->getLecturerStatistics(
            $request->input('program_study_id'),
            $request->boolean('include_trash', false)
        );

        return response()->json([
            'success' => true,
            'message' => 'Lecturer statistics retrieved successfully',
            'data' => $statistics
        ]);
    }

    /**
     * Get lecturer teaching load.
     */
    public function teachingLoad(Lecturer $lecturer): JsonResponse
    {
        $teachingLoad = $this->lecturerService->getLecturerTeachingLoad($lecturer);

        return response()->json(["success" => true, "message" => 'Lecturer teaching load retrieved successfully', "data" => $teachingLoad]);
    }

    /**
     * Get available lecturers for course assignment.
     */
    public function availableForCourse(Course $course): JsonResponse
    {
        $availableLecturers = $this->lecturerService->getAvailableLecturers($course);

        return response()->json(["success" => true, "message" => 'Available lecturers retrieved successfully', "data" => $availableLecturers]);
    }

    /**
     * Assign course to lecturer.
     */
    public function assignCourse(Request $request, Lecturer $lecturer, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'nullable|in:lecturer,assistant,coordinator',
            'academic_year' => 'nullable|integer',
            'semester' => 'nullable|in:ganjil,genap',
        ]);

        $assignment = $this->lecturerService->assignCourseToLecturer($lecturer, $course, $validated);

        return response()->json(["success" => true, "message" => 'Course assigned to lecturer successfully', "data" => $assignment]);
    }

    /**
     * Get lecturers by program study.
     */
    public function getByProgramStudy(Request $request, $programStudyId): JsonResponse
    {
        $filters = [
            'program_study_id' => $programStudyId,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'employment_type' => $request->input('employment_type'),
        ];

        $lecturers = $this->lecturerService->getLecturers($filters);

        return response()->json([
            'success' => true,
            'message' => $lecturers['message'],
            'data' => $lecturers['data'],
            'meta' => $lecturers['meta'] ?? []
        ]);
    }

    /**
     * Get active lecturers only.
     */
    public function getActive(Request $request): JsonResponse
    {
        $filters = [
            'is_active' => true,
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'status' => 'active',
        ];

        $lecturers = $this->lecturerService->getLecturers($filters);

        return response()->json([
            'success' => true,
            'message' => $lecturers['message'],
            'data' => $lecturers['data'],
            'meta' => $lecturers['meta'] ?? []
        ]);
    }

    /**
     * Get lecturers by faculty.
     */
    public function getByFaculty(Request $request, $faculty): JsonResponse
    {
        $filters = [
            'faculty' => $faculty,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'employment_type' => $request->input('employment_type'),
        ];

        $lecturers = $this->lecturerService->getLecturers($filters);

        return response()->json([
            'success' => true,
            'message' => $lecturers['message'],
            'data' => $lecturers['data'],
            'meta' => $lecturers['meta'] ?? []
        ]);
    }

    /**
     * Get lecturers for scheduling.
     */
    public function getForScheduling(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'day' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $lecturers = $this->lecturerService->getLecturersForScheduling(
            $validated['day'],
            $validated['time']
        );

        return response()->json(["success" => true, "message" => 'Available lecturers for scheduling retrieved successfully', "data" => $lecturers]);
    }

    /**
     * Bulk update lecturers.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lecturer_ids' => 'required|array',
            'lecturer_ids.*' => 'exists:lecturers,id',
            'updates' => 'required|array',
            'updates.status' => 'sometimes|in:active,inactive,on_leave,terminated,retired',
            'updates.employment_type' => 'sometimes|in:permanent,contract,part_time,guest',
            'updates.is_active' => 'sometimes|boolean',
            'updates.faculty' => 'sometimes|string|max:255',
            'updates.department' => 'sometimes|string|max:255',
        ]);

        $updatedCount = $this->lecturerService->bulkUpdateLecturers(
            $validated['lecturer_ids'],
            $validated['updates']
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updatedCount} lecturers",
            'data' => ['updated_count' => $updatedCount]
        ]);
    }

    /**
     * Bulk delete lecturers.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:lecturers,id',
        ]);

        $deletedCount = $this->lecturerService->bulkDeleteLecturers($validated['ids']);

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} lecturers",
            'data' => ['deleted_count' => $deletedCount]
        ]);
    }

    /**
     * Import lecturers from CSV/Excel.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // Max 10MB
            'program_study_id' => 'nullable|exists:program_studies,id',
        ]);

        $result = $this->lecturerService->importLecturers(
            $request->file('file'),
            $request->input('program_study_id'),
            $request->user()
        );

        return response()->json(["success" => true, "message" => 'Lecturers import completed', "data" => $result]);
    }

    /**
     * Export lecturers to CSV/Excel.
     */
    public function export(Request $request): JsonResponse
    {
        $filters = $request->only([
            'program_study_id',
            'status',
            'employment_type',
            'faculty',
            'department',
            'is_active',
        ]);

        $format = $request->input('format', 'csv');
        $filePath = $this->lecturerService->exportLecturers($filters, $format);

        return response()->json([
            'success' => true,
            'message' => 'Lecturers export completed',
            'data' => ['download_url' => asset($filePath)]
        ]);
    }

    /**
     * Get lecturer search suggestions.
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->input('query');
        $limit = $request->input('limit', 10);

        $suggestions = $this->lecturerService->getLecturerSearchSuggestions($query, $limit);

        return response()->json(["success" => true, "message" => 'Search suggestions retrieved successfully', "data" => $suggestions]);
    }

    /**
     * Get lecturers with high workload.
     */
    public function getHighWorkloadLecturers(Request $request): JsonResponse
    {
        $threshold = $request->input('threshold', 90);
        $lecturers = $this->lecturerService->getLecturersWithHighWorkload($threshold);

        return response()->json(["success" => true, "message" => 'High workload lecturers retrieved successfully', "data" => $lecturers]);
    }

    /**
     * Restore deleted lecturer.
     */
    public function restore($id): JsonResponse
    {
        $lecturer = $this->lecturerService->restoreLecturer($id);

        return response()->json(["success" => true, "message" => 'Lecturer restored successfully', "data" => $lecturer]);
    }

    /**
     * Permanently delete lecturer.
     */
    public function forceDelete($id): JsonResponse
    {
        $this->lecturerService->forceDeleteLecturer($id);

        return response()->json(["success" => true, "message" => 'Lecturer permanently deleted', "data" => null]);
    }

    /**
     * Update lecturer status.
     */
    public function updateStatus(Request $request, Lecturer $lecturer): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,on_leave,terminated,retired',
            'termination_date' => 'required_if:status,terminated|required_if:status,retired|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $updatedLecturer = $this->lecturerService->updateLecturerStatus($lecturer, $validated);

        return response()->json(["success" => true, "message" => 'Lecturer status updated successfully', "data" => $updatedLecturer]);
    }

    /**
     * Get lecturer attendance summary.
     */
    public function attendanceSummary(Lecturer $lecturer, Request $request): JsonResponse
    {
        $semester = $request->input('semester');
        $academicYear = $request->input('academic_year');

        $summary = $this->lecturerService->getLecturerAttendanceSummary($lecturer, $semester, $academicYear);

        return response()->json(["success" => true, "message" => 'Lecturer attendance summary retrieved successfully', "data" => $summary]);
    }

    /**
     * Get lecturers by employment type.
     */
    public function getByEmploymentType(Request $request, $type): JsonResponse
    {
        $filters = [
            'employment_type' => $type,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'faculty' => $request->input('faculty'),
        ];

        $lecturers = $this->lecturerService->getLecturers($filters);

        return response()->json([
            'success' => true,
            'message' => $lecturers['message'],
            'data' => $lecturers['data'],
            'meta' => $lecturers['meta'] ?? []
        ]);
    }

    /**
     * Get trashed lecturers.
     */
    public function trash(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'faculty' => $request->input('faculty'),
            'department' => $request->input('department'),
            'only_trashed' => $request->boolean('only_trashed', true),
        ];

        $result = $this->lecturerService->getLecturers(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'name'),
            $request->input('sort_direction', 'asc')
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['data'],
            'meta' => $result['meta'] ?? []
        ]);
    }

    /**
     * Bulk restore deleted lecturers.
     */
    public function bulkRestore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:lecturers,id',
        ]);

        $restoredCount = $this->lecturerService->bulkRestoreLecturers($validated['ids']);

        return response()->json([
            'success' => true,
            'message' => "Successfully restored {$restoredCount} lecturers",
            'data' => ['restored_count' => $restoredCount]
        ]);
    }

    /**
     * Bulk permanently delete lecturers.
     */
    public function bulkForceDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:lecturers,id',
        ]);

        $deletedCount = $this->lecturerService->bulkForceDeleteLecturers($validated['ids']);

        return response()->json([
            'success' => true,
            'message' => "Successfully permanently deleted {$deletedCount} lecturers",
            'data' => ['deleted_count' => $deletedCount]
        ]);
    }

    /**
     * Duplicate a lecturer.
     */
    public function duplicate(Lecturer $lecturer, Request $request): JsonResponse
    {
        try {
            $duplicatedLecturer = $this->lecturerService->duplicateLecturer($lecturer->id, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Lecturer duplicated successfully',
                'data' => $duplicatedLecturer
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate lecturer: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
