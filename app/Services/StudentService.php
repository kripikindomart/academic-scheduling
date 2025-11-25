<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ProgramStudy;
use App\Models\CourseEnrollment;
use App\Models\Attendance;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;

class StudentService
{
    /**
     * Get paginated list of students with filtering.
     */
    public function getStudents(array $filters = [], int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'asc'): array
    {
        $query = Student::with(['programStudy', 'creator', 'updater']);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['batch_year'])) {
            $query->where('batch_year', $filters['batch_year']);
        }

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (!empty($filters['class'])) {
            $query->where('class', $filters['class']);
        }

        if (!empty($filters['semester'])) {
            $query->where('current_semester', $filters['semester']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_regular'])) {
            $query->where('is_regular', $filters['is_regular']);
        }

        // Apply sorting
        $allowedSorts = ['name', 'student_number', 'email', 'gpa', 'current_semester', 'batch_year', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'name';
        $query->orderBy($sortBy, $sortDirection);

        $students = $query->paginate($perPage);

        return [
            'data' => $students,
            'message' => 'Students retrieved successfully',
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ]
        ];
    }

    /**
     * Create a new student.
     */
    public function createStudent(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            // Generate student number if not provided
            if (empty($data['student_number'])) {
                $programStudy = ProgramStudy::find($data['program_study_id']);
                $year = date('Y');
                $sequence = Student::whereYear('enrollment_date', $year)->count() + 1;
                $data['student_number'] = $programStudy->code . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }

            // Set current user as creator
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            $student = Student::create($data);

            Log::info('Student created', [
                'student_id' => $student->id,
                'student_number' => $student->student_number,
                'created_by' => auth()->id()
            ]);

            return $student->load(['programStudy', 'creator']);
        });
    }

    /**
     * Update an existing student.
     */
    public function updateStudent(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {
            $data['updated_by'] = auth()->id();

            $student->update($data);

            Log::info('Student updated', [
                'student_id' => $student->id,
                'student_number' => $student->student_number,
                'updated_by' => auth()->id()
            ]);

            return $student->load(['programStudy', 'creator', 'updater']);
        });
    }

    /**
     * Delete a student (soft delete).
     */
    public function deleteStudent(Student $student): bool
    {
        return DB::transaction(function () use ($student) {
            $studentId = $student->id;
            $studentNumber = $student->student_number;

            $deleted = $student->delete();

            if ($deleted) {
                Log::info('Student deleted', [
                    'student_id' => $studentId,
                    'student_number' => $studentNumber,
                    'deleted_by' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Restore a deleted student.
     */
    public function restoreStudent(int $studentId): Student
    {
        $student = Student::withTrashed()->findOrFail($studentId);

        DB::transaction(function () use ($student) {
            $student->restore();
            $student->update(['updated_by' => auth()->id()]);

            Log::info('Student restored', [
                'student_id' => $student->id,
                'student_number' => $student->student_number,
                'restored_by' => auth()->id()
            ]);
        });

        return $student->load(['programStudy', 'creator', 'updater']);
    }

    /**
     * Permanently delete a student.
     */
    public function forceDeleteStudent(int $studentId): bool
    {
        $student = Student::withTrashed()->findOrFail($studentId);

        return DB::transaction(function () use ($student) {
            $studentId = $student->id;
            $studentNumber = $student->student_number;

            $deleted = $student->forceDelete();

            if ($deleted) {
                Log::warning('Student permanently deleted', [
                    'student_id' => $studentId,
                    'student_number' => $studentNumber,
                    'deleted_by' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Get student statistics.
     */
    public function getStudentStatistics(?int $programStudyId = null, ?int $batchYear = null): array
    {
        $query = Student::query();

        if ($programStudyId) {
            $query->where('program_study_id', $programStudyId);
        }

        if ($batchYear) {
            $query->where('batch_year', $batchYear);
        }

        $total = $query->count();
        $active = $query->where('status', 'active')->where('is_active', true)->count();
        $inactive = $query->where('status', 'inactive')->count();
        $graduated = $query->where('status', 'graduated')->count();
        $droppedOut = $query->where('status', 'dropped_out')->count();
        $onLeave = $query->where('status', 'on_leave')->count();

        $gpaStats = $query->where('status', '!=', 'graduated')
            ->whereNotNull('gpa')
            ->selectRaw('AVG(gpa) as avg_gpa, MIN(gpa) as min_gpa, MAX(gpa) as max_gpa')
            ->first();

        $genderStats = $query->selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        $batchYearStats = $query->selectRaw('batch_year, COUNT(*) as count')
            ->groupBy('batch_year')
            ->orderBy('batch_year', 'desc')
            ->get()
            ->pluck('count', 'batch_year')
            ->toArray();

        return [
            'total_students' => $total,
            'by_status' => [
                'active' => $active,
                'inactive' => $inactive,
                'graduated' => $graduated,
                'dropped_out' => $droppedOut,
                'on_leave' => $onLeave,
            ],
            'gpa_statistics' => [
                'average' => round($gpaStats->avg_gpa ?? 0, 2),
                'minimum' => round($gpaStats->min_gpa ?? 0, 2),
                'maximum' => round($gpaStats->max_gpa ?? 0, 2),
            ],
            'by_gender' => $genderStats,
            'by_batch_year' => $batchYearStats,
        ];
    }

    /**
     * Get student academic progress.
     */
    public function getStudentAcademicProgress(Student $student): array
    {
        $enrollments = CourseEnrollment::where('student_id', $student->id)
            ->with(['course', 'grade'])
            ->get();

        $totalCredits = 0;
        $completedCredits = 0;
        $gpa = 0;
        $grades = [];

        foreach ($enrollments as $enrollment) {
            $totalCredits += $enrollment->course->credits;

            if ($enrollment->grade && $enrollment->grade->final_grade) {
                $completedCredits += $enrollment->course->credits;
                $gpa += $enrollment->grade->final_grade * $enrollment->course->credits;
                $grades[] = [
                    'course_name' => $enrollment->course->name,
                    'credits' => $enrollment->course->credits,
                    'grade' => $enrollment->grade->final_grade,
                    'semester' => $enrollment->semester,
                ];
            }
        }

        $averageGpa = $completedCredits > 0 ? round($gpa / $completedCredits, 2) : 0;

        return [
            'student' => $student->load('programStudy'),
            'current_semester' => $student->current_semester,
            'current_year' => $student->current_year,
            'total_credits' => $totalCredits,
            'completed_credits' => $completedCredits,
            'remaining_credits' => $totalCredits - $completedCredits,
            'average_gpa' => $averageGpa,
            'progress_percentage' => $student->progress_percentage,
            'study_duration' => $student->study_duration . ' years',
            'grades' => $grades,
        ];
    }

    /**
     * Bulk update students.
     */
    public function bulkUpdateStudents(array $studentIds, array $updates): int
    {
        return DB::transaction(function () use ($studentIds, $updates) {
            $updates['updated_by'] = auth()->id();

            $updated = Student::whereIn('id', $studentIds)
                ->update($updates);

            Log::info('Bulk student update', [
                'student_count' => $updated,
                'student_ids' => $studentIds,
                'updates' => $updates,
                'updated_by' => auth()->id()
            ]);

            return $updated;
        });
    }

    /**
     * Import students from file.
     */
    public function importStudents($file, int $programStudyId, $user): array
    {
        try {
            $import = new StudentsImport($programStudyId, $user);
            Excel::import($import, $file);

            return [
                'success' => true,
                'imported_count' => $import->getImportedCount(),
                'failed_count' => $import->getFailedCount(),
                'errors' => $import->getErrors(),
            ];
        } catch (\Exception $e) {
            Log::error('Student import failed', [
                'error' => $e->getMessage(),
                'program_study_id' => $programStudyId,
                'user_id' => $user->id
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Export students to file.
     */
    public function exportStudents(array $filters = [], string $format = 'csv'): string
    {
        $students = $this->getStudentsForExport($filters);

        $fileName = 'students_' . date('Y_m_d_H_i_s') . '.' . $format;
        $filePath = 'exports/' . $fileName;

        if ($format === 'excel') {
            Excel::store(new StudentsExport($students), $filePath, 'public', \Maatwebsite\Excel\Excel::XLSX);
        } else {
            Excel::store(new StudentsExport($students), $filePath, 'public', \Maatwebsite\Excel\Excel::CSV);
        }

        return Storage::url($filePath);
    }

    /**
     * Get students for export.
     */
    private function getStudentsForExport(array $filters): \Illuminate\Support\Collection
    {
        $query = Student::with(['programStudy']);

        foreach ($filters as $key => $value) {
            if ($value) {
                $query->where($key, $value);
            }
        }

        return $query->get();
    }

    /**
     * Get student search suggestions.
     */
    public function getStudentSearchSuggestions(string $query, int $limit = 10): array
    {
        return Student::where('name', 'like', "%{$query}%")
            ->orWhere('student_number', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit($limit)
            ->get(['id', 'name', 'student_number', 'email', 'program_study_id'])
            ->load('programStudy:name,id')
            ->toArray();
    }

    /**
     * Get students with low GPA.
     */
    public function getStudentsWithLowGpa(float $threshold): \Illuminate\Database\Eloquent\Collection
    {
        return Student::where('gpa', '<', $threshold)
            ->where('status', 'active')
            ->where('is_active', true)
            ->with(['programStudy'])
            ->orderBy('gpa', 'asc')
            ->get();
    }

    /**
     * Update student status.
     */
    public function updateStudentStatus(Student $student, array $data): Student
    {
        return DB::transaction(function () use ($student, $data) {
            $student->update([
                'status' => $data['status'],
                'graduation_date' => $data['graduation_date'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            // Add notes if provided
            if (!empty($data['notes'])) {
                $student->notes = ($student->notes ?? '') . "\n\n" . date('Y-m-d H:i:s') . ': ' . $data['notes'];
                $student->save();
            }

            Log::info('Student status updated', [
                'student_id' => $student->id,
                'student_number' => $student->student_number,
                'old_status' => $student->getOriginal('status'),
                'new_status' => $data['status'],
                'updated_by' => auth()->id()
            ]);

            return $student->load(['programStudy']);
        });
    }

    /**
     * Get student attendance summary.
     */
    public function getStudentAttendanceSummary(Student $student, ?int $semester = null, ?string $academicYear = null): array
    {
        $query = Attendance::where('student_id', $student->id);

        if ($semester) {
            $query->where('semester', $semester);
        }

        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }

        $attendances = $query->get();

        $totalSessions = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $excused = $attendances->where('status', 'excused')->count();

        $attendanceRate = $totalSessions > 0 ? round(($present / $totalSessions) * 100, 2) : 0;

        return [
            'student' => $student->only(['id', 'name', 'student_number']),
            'period' => [
                'semester' => $semester,
                'academic_year' => $academicYear,
            ],
            'summary' => [
                'total_sessions' => $totalSessions,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'excused' => $excused,
                'attendance_rate' => $attendanceRate . '%',
            ],
            'recent_attendances' => $attendances->take(10)->load(['courseSchedule.course']),
        ];
    }
}