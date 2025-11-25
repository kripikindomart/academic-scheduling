<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Services\StudentService;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

        return response()->success($result['data'], $result['message'], $result['meta']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = $this->studentService->createStudent($request->validated());

        return response()->success($student, 'Student created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): JsonResponse
    {
        $student->load([
            'programStudy',
            'creator',
            'updater'
        ]);

        return response()->success($student, 'Student retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        $updatedStudent = $this->studentService->updateStudent($student, $request->validated());

        return response()->success($updatedStudent, 'Student updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): JsonResponse
    {
        $this->studentService->deleteStudent($student);

        return response()->success(null, 'Student deleted successfully');
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

        return response()->success($statistics, 'Student statistics retrieved successfully');
    }

    /**
     * Get student academic progress.
     */
    public function academicProgress(Student $student): JsonResponse
    {
        $progress = $this->studentService->getStudentAcademicProgress($student);

        return response()->success($progress, 'Student academic progress retrieved successfully');
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

        return response()->success($students['data'], $students['message'], $students['meta']);
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

        return response()->success($students['data'], $students['message'], $students['meta']);
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

        return response()->success($students['data'], $students['message'], $students['meta']);
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

        return response()->success(
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
            'program_study_id' => 'required|exists:program_studies,id',
        ]);

        $result = $this->studentService->importStudents(
            $request->file('file'),
            $request->input('program_study_id'),
            $request->user()
        );

        return response()->success($result, 'Students import completed');
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
        ]);

        $format = $request->input('format', 'csv');
        $filePath = $this->studentService->exportStudents($filters, $format);

        return response()->success(
            ['download_url' => asset($filePath)],
            'Students export completed'
        );
    }

    /**
     * Get student search suggestions.
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->input('query');
        $limit = $request->input('limit', 10);

        $suggestions = $this->studentService->getStudentSearchSuggestions($query, $limit);

        return response()->success($suggestions, 'Search suggestions retrieved successfully');
    }

    /**
     * Restore deleted student.
     */
    public function restore($id): JsonResponse
    {
        $student = $this->studentService->restoreStudent($id);

        return response()->success($student, 'Student restored successfully');
    }

    /**
     * Permanently delete student.
     */
    public function forceDelete($id): JsonResponse
    {
        $this->studentService->forceDeleteStudent($id);

        return response()->success(null, 'Student permanently deleted');
    }

    /**
     * Get students with low GPA.
     */
    public function getLowGpaStudents(Request $request): JsonResponse
    {
        $threshold = $request->input('threshold', 2.0);
        $students = $this->studentService->getStudentsWithLowGpa($threshold);

        return response()->success($students, 'Low GPA students retrieved successfully');
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

        return response()->success($updatedStudent, 'Student status updated successfully');
    }

    /**
     * Get student attendance summary.
     */
    public function attendanceSummary(Student $student, Request $request): JsonResponse
    {
        $semester = $request->input('semester');
        $academicYear = $request->input('academic_year');

        $summary = $this->studentService->getStudentAttendanceSummary($student, $semester, $academicYear);

        return response()->success($summary, 'Student attendance summary retrieved successfully');
    }
}
