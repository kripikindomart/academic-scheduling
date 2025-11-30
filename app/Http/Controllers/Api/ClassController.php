<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Class\StoreClassRequest;
use App\Http\Requests\Class\UpdateClassRequest;
use App\Services\ClassService;
use App\Services\ResponseService;
use App\Models\Kelas;
use App\Models\ProgramStudy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClassController extends Controller
{
    protected ClassService $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
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

            $filters = [
                'search' => $request->input('search'),
                'program_study_id' => $request->input('program_study_id'),
                'batch_year' => $request->input('batch_year'),
                'semester' => $request->input('semester'),
                'academic_year' => $request->input('academic_year'),
                'is_active' => $request->input('is_active'),
                'has_capacity' => $request->input('has_capacity'),
            ];

            $result = $this->classService->getClasses(
                $filters,
                $request->input('per_page', 15),
                $request->input('sort_by', 'name'),
                $request->input('sort_direction', 'asc')
            );

            return ResponseService::success($result['data'], $result['message'], $result['meta']);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve classes: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassRequest $request): JsonResponse
    {
        $class = $this->classService->createClass($request->validated());

        return ResponseService::success($class, 'Class created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $class): JsonResponse
    {
        $class->load([
            'programStudy',
            'students' => function ($query) {
                $query->wherePivot('status', 'active');
            },
            'creator',
            'updater'
        ]);

        return ResponseService::success($class, 'Class retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassRequest $request, Kelas $class): JsonResponse
    {
        $updatedClass = $this->classService->updateClass($class, $request->validated());

        return ResponseService::success($updatedClass, 'Class updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $class): JsonResponse
    {
        $this->classService->deleteClass($class);

        return ResponseService::success(null, 'Class deleted successfully');
    }

    /**
     * Get class statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $filters = [
            'program_study_id' => $request->input('program_study_id'),
            'batch_year' => $request->input('batch_year'),
            'semester' => $request->input('semester'),
            'academic_year' => $request->input('academic_year'),
        ];

        $statistics = $this->classService->getStatistics($filters);

        return ResponseService::success($statistics, 'Class statistics retrieved successfully');
    }

    /**
     * Get available classes (with capacity).
     */
    public function available(Request $request): JsonResponse
    {
        $filters = [
            'program_study_id' => $request->input('program_study_id'),
            'batch_year' => $request->input('batch_year'),
            'semester' => $request->input('semester'),
            'academic_year' => $request->input('academic_year'),
        ];

        $classes = $this->classService->getAvailableClasses($filters);

        return ResponseService::success($classes, 'Available classes retrieved successfully');
    }

    /**
     * Get students in a class.
     */
    public function students(Kelas $class, Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status', 'active'),
        ];

        $result = $this->classService->getClassStudents(
            $class,
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'name'),
            $request->input('sort_direction', 'asc')
        );

        return ResponseService::success($result['data'], $result['message'], $result['meta']);
    }

    /**
     * Enroll students in a class.
     */
    public function enrollStudents(Kelas $class, Request $request): JsonResponse
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'enrollment_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $result = $this->classService->enrollStudents(
            $class,
            $request->input('student_ids'),
            $request->input('enrollment_date', now()),
            $request->input('notes')
        );

        $message = $result['total_enrolled'] > 0
    ? "{$result['total_enrolled']} mahasiswa berhasil dienroll ke kelas {$class->name}"
    : 'Tidak ada mahasiswa yang dienroll';

// Refresh class data to get updated current_students count
$class->refresh();

// Add updated class info to response
$result['updated_class'] = [
    'id' => $class->id,
    'name' => $class->name,
    'current_students' => $class->current_students,
    'capacity' => $class->capacity
];

return ResponseService::success($result, $message);
    }

    /**
     * Remove student from class.
     */
    public function removeStudent(Kelas $class, Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'notes' => 'nullable|string'
        ]);

        $result = $this->classService->removeStudent(
            $class,
            $request->input('student_id'),
            $request->input('notes')
        );

        return ResponseService::success($result, 'Student removed from class successfully');
    }

    /**
     * Transfer student to another class.
     */
    public function transferStudent(Kelas $class, Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'to_class_id' => 'required|exists:classes,id',
            'notes' => 'nullable|string'
        ]);

        $result = $this->classService->transferStudent(
            $class,
            $request->input('student_id'),
            $request->input('to_class_id'),
            $request->input('notes')
        );

        return ResponseService::success($result, 'Student transferred successfully');
    }

    /**
     * Get trashed classes.
     */
    public function trashed(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'batch_year' => $request->input('batch_year'),
            'semester' => $request->input('semester'),
            'academic_year' => $request->input('academic_year'),
        ];

        $result = $this->classService->getTrashedClasses(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'deleted_at'),
            $request->input('sort_direction', 'desc')
        );

        return ResponseService::success($result['data'], 'Trashed classes retrieved successfully', $result['meta']);
    }

    /**
     * Restore a deleted class.
     */
    public function restore($id): JsonResponse
    {
        $class = Kelas::withTrashed()->findOrFail($id);

        $this->classService->restoreClass($class);

        return ResponseService::success($class, 'Class restored successfully');
    }

    /**
     * Force delete a class permanently.
     */
    public function forceDelete($id): JsonResponse
    {
        $class = Kelas::withTrashed()->findOrFail($id);

        $this->classService->forceDeleteClass($class);

        return ResponseService::success(null, 'Class deleted permanently');
    }

    /**
     * Bulk restore classes.
     */
    public function bulkRestore(Request $request): JsonResponse
    {
        $request->validate([
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id'
        ]);

        $result = $this->classService->bulkRestore($request->input('class_ids'));

        return ResponseService::success($result, 'Classes restored successfully');
    }

    /**
     * Bulk force delete classes.
     */
    public function bulkForceDelete(Request $request): JsonResponse
    {
        $request->validate([
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id'
        ]);

        $result = $this->classService->bulkForceDelete($request->input('class_ids'));

        return ResponseService::success($result, 'Classes deleted permanently');
    }

    /**
     * Update student enrollment status.
     */
    public function updateStudentStatus(Kelas $class, Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:active,inactive,transferred,dropped',
            'notes' => 'nullable|string'
        ]);

        $result = $this->classService->updateStudentStatus(
            $class,
            $request->input('student_id'),
            $request->input('status'),
            $request->input('notes')
        );

        return ResponseService::success($result, 'Student status updated successfully');
    }

    /**
     * Bulk operations for classes.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id',
            'data' => 'required|array',
            'data.is_active' => 'nullable|boolean',
            'data.capacity' => 'nullable|integer|min:0',
            'data.room_number' => 'nullable|string|max:50',
        ]);

        $result = $this->classService->bulkUpdateClasses(
            $request->input('class_ids'),
            $request->input('data')
        );

        return ResponseService::success($result, 'Classes updated successfully');
    }

    /**
     * Bulk delete classes.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id',
        ]);

        $result = $this->classService->bulkDeleteClasses($request->input('class_ids'));

        return ResponseService::success($result, 'Classes deleted successfully');
    }

    /**
     * Get classes by program study.
     */
    public function getByProgramStudy(ProgramStudy $programStudy, Request $request): JsonResponse
    {
        $filters = [
            'batch_year' => $request->input('batch_year'),
            'semester' => $request->input('semester'),
            'academic_year' => $request->input('academic_year'),
            'is_active' => $request->input('is_active'),
        ];

        $classes = $this->classService->getClassesByProgramStudy($programStudy, $filters);

        return ResponseService::success($classes, 'Classes by program study retrieved successfully');
    }

    /**
     * Get classes by batch year.
     */
    public function getByBatchYear($batchYear, Request $request): JsonResponse
    {
        $filters = [
            'program_study_id' => $request->input('program_study_id'),
            'semester' => $request->input('semester'),
            'academic_year' => $request->input('academic_year'),
            'is_active' => $request->input('is_active'),
        ];

        $classes = $this->classService->getClassesByBatchYear($batchYear, $filters);

        return ResponseService::success($classes, 'Classes by batch year retrieved successfully');
    }

    /**
     * Get classes by academic year.
     */
    public function getByAcademicYear($academicYear, Request $request): JsonResponse
    {
        $filters = [
            'program_study_id' => $request->input('program_study_id'),
            'batch_year' => $request->input('batch_year'),
            'semester' => $request->input('semester'),
            'is_active' => $request->input('is_active'),
        ];

        $classes = $this->classService->getClassesByAcademicYear($academicYear, $filters);

        return ResponseService::success($classes, 'Classes by academic year retrieved successfully');
    }

    /**
     * Get class enrollment report.
     */
    public function enrollmentReport(Request $request): JsonResponse
    {
        $filters = [
            'program_study_id' => $request->input('program_study_id'),
            'batch_year' => $request->input('batch_year'),
            'semester' => $request->input('semester'),
            'academic_year' => $request->input('academic_year'),
        ];

        $report = $this->classService->getEnrollmentReport($filters);

        return ResponseService::success($report, 'Class enrollment report generated successfully');
    }

    /**
     * Generate class codes for batch.
     */
    public function generateClassCodes(Request $request): JsonResponse
    {
        $request->validate([
            'program_study_id' => 'required|exists:program_studies,id',
            'batch_year' => 'required|integer',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string',
            'class_count' => 'required|integer|min:1',
            'capacity_per_class' => 'required|integer|min:1'
        ]);

        $result = $this->classService->generateClassCodes(
            $request->input('program_study_id'),
            $request->input('batch_year'),
            $request->input('semester'),
            $request->input('academic_year'),
            $request->input('class_count'),
            $request->input('capacity_per_class')
        );

        return ResponseService::success($result, 'Class codes generated successfully');
    }

    /**
     * Auto-enroll students to classes.
     */
    public function autoEnrollStudents(Request $request): JsonResponse
    {
        $request->validate([
            'program_study_id' => 'required|exists:program_studies,id',
            'batch_year' => 'required|integer',
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string',
        ]);

        $result = $this->classService->autoEnrollStudents(
            $request->input('program_study_id'),
            $request->input('batch_year'),
            $request->input('semester'),
            $request->input('academic_year')
        );

        return ResponseService::success($result, 'Students auto-enrolled successfully');
    }
}