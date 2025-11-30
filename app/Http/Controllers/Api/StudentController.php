<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Services\StudentService;
use App\Services\ResponseService;
use App\Models\Student;
use App\Models\ProgramStudy;
use App\Exports\StudentsTemplateExport;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    protected StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
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
                // If user has program study, only show students from that program
                $request->merge(['program_study_id' => $user->program_study_id]);
            }

            $filters = [
                'search' => $request->input('search'),
                'program_study_id' => $request->input('program_study_id'),
                'status' => $request->input('status'),
                'batch_year' => $request->input('batch_year'),
                'gender' => $request->input('gender'),
                'class' => $request->input('class'),
                'semester' => $request->input('current_semester'),
                'is_active' => $request->input('is_active'),
                'is_regular' => $request->input('is_regular'),
            ];

            $result = $this->studentService->getStudents(
                $filters,
                $request->input('per_page', 15),
                $request->input('sort_by', 'name'),
                $request->input('sort_direction', 'asc')
            );

            return ResponseService::success($result['data'], $result['message'], $result['meta']);
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve students: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = $this->studentService->createStudent($request->validated());

        return ResponseService::success($student, 'Student created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Student $student): JsonResponse
    {
        try {
            $user = $request->user();

            // Check if user has access to this student's program study
            if ($user && !$user->isAdmin() && $user->program_study_id && $student->program_study_id !== $user->program_study_id) {
                return ResponseService::error(
                    'Unauthorized access to this student',
                    null,
                    403
                );
            }

            $student->load([
                'programStudy',
                'creator',
                'updater'
            ]);

            return ResponseService::success($student, 'Student retrieved successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve student: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        $updatedStudent = $this->studentService->updateStudent($student, $request->validated());

        return ResponseService::success($updatedStudent, 'Student updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): JsonResponse
    {
        $this->studentService->deleteStudent($student);

        return ResponseService::success(null, 'Student deleted successfully');
    }

    /**
     * Get student statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $statistics = $this->studentService->getStudentStatistics(
            $request->input('program_study_id'),
            $request->input('batch_year')
        );

        return ResponseService::success($statistics, 'Student statistics retrieved successfully');
    }

    /**
     * Get student academic progress.
     */
    public function academicProgress(Student $student): JsonResponse
    {
        $progress = $this->studentService->getStudentAcademicProgress($student);

        return ResponseService::success($progress, 'Student academic progress retrieved successfully');
    }

    /**
     * Get students by program study.
     */
    public function getByProgramStudy(Request $request, $programStudyId): JsonResponse
    {
        $filters = [
            'program_study_id' => $programStudyId,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'batch_year' => $request->input('batch_year'),
            'class' => $request->input('class'),
            'semester' => $request->input('current_semester'),
        ];

        $students = $this->studentService->getStudents($filters);

        return ResponseService::success($students['data'], $students['message'], $students['meta']);
    }

    /**
     * Get students by batch year.
     */
    public function getByBatchYear(Request $request, $batchYear): JsonResponse
    {
        $filters = [
            'batch_year' => $batchYear,
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'status' => $request->input('status'),
            'class' => $request->input('class'),
        ];

        $students = $this->studentService->getStudents($filters);

        return ResponseService::success($students['data'], $students['message'], $students['meta']);
    }

    /**
     * Get active students only.
     */
    public function getActive(Request $request): JsonResponse
    {
        $filters = [
            'is_active' => true,
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'status' => 'active',
            'batch_year' => $request->input('batch_year'),
        ];

        $students = $this->studentService->getStudents($filters);

        return ResponseService::success($students['data'], $students['message'], $students['meta']);
    }

    /**
     * Bulk update students.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'updates' => 'required|array',
            'updates.status' => 'sometimes|in:active,inactive,graduated,dropped_out,on_leave',
            'updates.class' => 'sometimes|string|max:50',
            'updates.current_semester' => 'sometimes|integer|min:1',
            'updates.current_year' => 'sometimes|integer|min:1',
            'updates.is_active' => 'sometimes|boolean',
            'updates.is_regular' => 'sometimes|boolean',
        ]);

        $updatedCount = $this->studentService->bulkUpdateStudents(
            $validated['student_ids'],
            $validated['updates']
        );

        return ResponseService::success(
            ['updated_count' => $updatedCount],
            "Successfully updated {$updatedCount} students"
        );
    }

    /**
     * Import students from CSV/Excel.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // Max 10MB
        ]);

        try {
            $import = new StudentsImport($request->user()->id);
            Excel::import($import, $request->file('file'));

            $results = $import->getImportResults();
            $summary = $import->getImportSummary();

            return ResponseService::success([
                'summary' => $summary,
                'details' => $results,
            ], 'Students import completed successfully');

        } catch (\Exception $e) {
            return ResponseService::error(
                'Import failed: ' . $e->getMessage(),
                400
            );
        }
    }

    /**
     * Export students to CSV/Excel.
     */
    public function export(Request $request): JsonResponse
    {
        $filters = $request->only([
            'program_study_id',
            'status',
            'batch_year',
            'class',
            'is_active',
            'gender',
            'is_regular',
            'gpa_min',
            'gpa_max',
            'search',
        ]);

        $format = $request->input('format', 'xlsx');
        $includeHeaders = $request->input('include_headers', true);

        try {
            $fileName = 'students_' . date('Y-m-d_H-i-s') . '.' . $format;
            $filePath = 'exports/' . $fileName;

            $export = new StudentsExport(null, $filters);
            $export->setIncludeHeaders($includeHeaders);

            Excel::store($export, $filePath, 'public');

            return ResponseService::success([
                'download_url' => Storage::url($filePath),
                'file_name' => $fileName,
                'file_size' => Storage::disk('public')->size($filePath),
                'exported_at' => now()->toISOString(),
            ], 'Students export completed successfully');

        } catch (\Exception $e) {
            return ResponseService::error(
                'Export failed: ' . $e->getMessage(),
                400
            );
        }
    }

    /**
     * Download student import template.
     */
    public function downloadTemplate(): JsonResponse
    {
        try {
            $fileName = 'student_import_template_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filePath = 'templates/' . $fileName;

            Excel::store(new StudentsTemplateExport(), $filePath, 'public');

            return ResponseService::success([
                'download_url' => Storage::url($filePath),
                'file_name' => $fileName,
                'file_size' => Storage::disk('public')->size($filePath),
                'generated_at' => now()->toISOString(),
            ], 'Student import template generated successfully');

        } catch (\Exception $e) {
            return ResponseService::error(
                'Template generation failed: ' . $e->getMessage(),
                400
            );
        }
    }

    /**
     * Get student search suggestions.
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->input('query');
        $limit = $request->input('limit', 10);

        $suggestions = $this->studentService->getStudentSearchSuggestions($query, $limit);

        return ResponseService::success($suggestions, 'Search suggestions retrieved successfully');
    }

    /**
     * Restore deleted student.
     */
    public function restore($id): JsonResponse
    {
        $student = $this->studentService->restoreStudent($id);

        return ResponseService::success($student, 'Student restored successfully');
    }

    /**
     * Permanently delete student.
     */
    public function forceDelete($id): JsonResponse
    {
        $this->studentService->forceDeleteStudent($id);

        return ResponseService::success(null, 'Student permanently deleted');
    }

    /**
     * Get students with low GPA.
     */
    public function getLowGpaStudents(Request $request): JsonResponse
    {
        $threshold = $request->input('threshold', 2.0);
        $students = $this->studentService->getStudentsWithLowGpa($threshold);

        return ResponseService::success($students, 'Low GPA students retrieved successfully');
    }

    /**
     * Update student status.
     */
    public function updateStatus(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,graduated,dropped_out,on_leave',
            'graduation_date' => 'required_if:status,graduated|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $updatedStudent = $this->studentService->updateStudentStatus($student, $validated);

        return ResponseService::success($updatedStudent, 'Student status updated successfully');
    }

    /**
     * Get student attendance summary.
     */
    public function attendanceSummary(Student $student, Request $request): JsonResponse
    {
        $semester = $request->input('semester');
        $academicYear = $request->input('academic_year');

        $summary = $this->studentService->getStudentAttendanceSummary($student, $semester, $academicYear);

        return ResponseService::success($summary, 'Student attendance summary retrieved successfully');
    }

    /**
     * Duplicate a student record.
     */
    public function duplicate(Request $request, Student $student): JsonResponse
    {
        try {
            $duplicateData = $request->validate([
                'student_number' => 'required|string|max:20|unique:students,student_number',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:students,email',
                'phone' => 'nullable|string|max:50',
                'batch_year' => 'nullable|string|size:4',
                'class' => 'nullable|string|max:50',
                'status' => 'required|in:active,inactive,graduated,dropped_out,on_leave',
                'gender' => 'nullable|in:L,P',
                'create_user_account' => 'nullable|boolean',
                'copy_photo' => 'nullable|boolean',
            ]);

            $duplicatedStudent = $this->studentService->duplicateStudent($student, $duplicateData);

            return ResponseService::success($duplicatedStudent, 'Student duplicated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::error(
                'Validation failed: ' . implode(', ', $e->validator->errors()->all()),
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to duplicate student: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get trashed students.
     */
    public function trash(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'batch_year' => $request->input('batch_year'),
            'class' => $request->input('class'),
            'gender' => $request->input('gender'),
        ];

        $result = $this->studentService->getTrashedStudents(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'deleted_at'),
            $request->input('sort_direction', 'desc')
        );

        return ResponseService::success($result['data'], $result['message'], $result['meta']);
    }

    /**
     * Bulk delete students (soft delete).
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $deletedCount = $this->studentService->bulkDeleteStudents($validated['student_ids']);

        return ResponseService::success(
            ['deleted_count' => $deletedCount],
            "Successfully deleted {$deletedCount} students"
        );
    }

    /**
     * Bulk force delete students (permanent delete).
     */
    public function bulkForceDelete(Request $request): JsonResponse
    {
        // Accept both 'student_ids' and 'ids' for compatibility
        $studentIds = $request->input('student_ids', $request->input('ids', []));

        $validated = $request->validate([
            'student_ids' => 'required_without:ids|array',
            'student_ids.*' => 'exists:students,id',
            'ids' => 'required_without:student_ids|array',
            'ids.*' => 'exists:students,id',
        ]);

        $studentIds = $validated['student_ids'] ?? $validated['ids'] ?? [];

        $deletedCount = $this->studentService->bulkForceDeleteStudents($studentIds);

        return ResponseService::success(
            ['deleted_count' => $deletedCount],
            "Successfully permanently deleted {$deletedCount} students"
        );
    }

    /**
     * Bulk restore students.
     */
    public function bulkRestore(Request $request): JsonResponse
    {
        // Accept both 'ids' and 'student_ids' for compatibility
        $studentIds = $request->input('ids', $request->input('student_ids', []));

        $validated = $request->validate([
            'ids' => 'required_without:student_ids|array',
            'ids.*' => 'exists:students,id',
            'student_ids' => 'required_without:ids|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $studentIds = $validated['ids'] ?? $validated['student_ids'] ?? [];

        $restoredCount = $this->studentService->bulkRestoreStudents($studentIds);

        return ResponseService::success(
            ['restored_count' => $restoredCount],
            "Successfully restored {$restoredCount} students"
        );
    }

    /**
     * Create user account for a student.
     */
    public function createUserAccount(Student $student): JsonResponse
    {
        $result = $this->studentService->createUserAccount($student);

        if ($result['success']) {
            return ResponseService::success($result['student'], $result['message']);
        } else {
            return ResponseService::error($result['message'], 400);
        }
    }

    /**
     * Bulk create user accounts for multiple students.
     */
    public function bulkCreateUserAccounts(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $results = $this->studentService->bulkCreateUserAccounts($validated['student_ids']);

        return ResponseService::success($results, 'Bulk user account creation completed');
    }
}
