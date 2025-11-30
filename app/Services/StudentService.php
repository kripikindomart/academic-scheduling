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
        $query = Student::with(['programStudy', 'creator', 'updater', 'user']);

        // Apply program study filtering based on user permissions
        $this->applyProgramStudyFilter($query, $filters);

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
            // Convert string boolean to proper boolean
            $isActive = $filters['is_active'];
            if (is_string($isActive)) {
                $isActive = $isActive === 'true' || $isActive === '1';
            }
            $query->where('is_active', $isActive);
        }

        if (isset($filters['is_regular'])) {
            // Convert string boolean to proper boolean
            $isRegular = $filters['is_regular'];
            if (is_string($isRegular)) {
                $isRegular = $isRegular === 'true' || $isRegular === '1';
            }
            $query->where('is_regular', $isRegular);
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
     * Bulk delete students (soft delete).
     */
    public function bulkDeleteStudents(array $studentIds): int
    {
        return DB::transaction(function () use ($studentIds) {
            $deletedCount = 0;

            Student::whereIn('id', $studentIds)->get()->each(function ($student) use (&$deletedCount) {
                if ($student->delete()) {
                    $deletedCount++;

                    Log::info('Student soft deleted (bulk)', [
                        'student_id' => $student->id,
                        'student_number' => $student->student_number,
                        'deleted_by' => auth()->id()
                    ]);
                }
            });

            Log::info('Bulk delete completed', [
                'student_count' => $deletedCount,
                'deleted_by' => auth()->id()
            ]);

            return $deletedCount;
        });
    }

    /**
     * Bulk force delete students (permanent delete for trashed students).
     */
    public function bulkForceDeleteStudents(array $studentIds): int
    {
        return DB::transaction(function () use ($studentIds) {
            $deletedCount = 0;

            Student::withTrashed()->whereIn('id', $studentIds)->get()->each(function ($student) use (&$deletedCount) {
                if ($student->forceDelete()) {
                    $deletedCount++;

                    Log::warning('Student permanently deleted (bulk)', [
                        'student_id' => $student->id,
                        'student_number' => $student->student_number,
                        'deleted_by' => auth()->id()
                    ]);
                }
            });

            Log::warning('Bulk force delete completed', [
                'student_count' => $deletedCount,
                'deleted_by' => auth()->id()
            ]);

            return $deletedCount;
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

    /**
     * Duplicate a student record.
     */
    public function duplicateStudent(Student $student, array $duplicateData): Student
    {
        return DB::transaction(function () use ($student, $duplicateData) {
            // Start with original student data
            $newStudentData = $student->toArray();

            // Remove fields that should be reset
            unset($newStudentData['id'], $newStudentData['created_at'], $newStudentData['updated_at']);

            // Override with user-provided data
            $newStudentData = array_merge($newStudentData, $duplicateData);

            // Remove unique identifiers that might conflict
            // Set proper default values for required fields
            $newStudentData['id_card_number'] = $duplicateData['id_card_number'] ?? 'TMP_' . time(); // Generate unique temp ID
            $newStudentData['passport_number'] = null;

            // Ensure required fields have proper values if not provided
            $newStudentData['gender'] = $newStudentData['gender'] ?? 'L'; // Default to L (Laki-laki) for new duplicates
            $newStudentData['birth_date'] = $newStudentData['birth_date'] ?? now()->subYears(18)->format('Y-m-d'); // Default to 18 years ago
            $newStudentData['birth_place'] = $newStudentData['birth_place'] ?? 'Unknown';
            $newStudentData['address'] = $newStudentData['address'] ?? 'No address provided';
            $newStudentData['city'] = $newStudentData['city'] ?? 'Unknown';
            $newStudentData['province'] = $newStudentData['province'] ?? 'Unknown';
            $newStudentData['postal_code'] = $newStudentData['postal_code'] ?? '00000';
            $newStudentData['nationality'] = $newStudentData['nationality'] ?? 'Indonesia';
            $newStudentData['religion'] = $newStudentData['religion'] ?? 'Islam';
            $newStudentData['status'] = $newStudentData['status'] ?? 'active';
            $newStudentData['enrollment_date'] = $newStudentData['enrollment_date'] ?? now()->format('Y-m-d');
            $newStudentData['class'] = $newStudentData['class'] ?? 'A';
            $newStudentData['batch_year'] = $newStudentData['batch_year'] ?? now()->format('Y');

            // Set current user as creator
            $newStudentData['created_by'] = auth()->id();
            $newStudentData['updated_by'] = auth()->id();

            // Handle photo copying
            if (!($duplicateData['copy_photo'] ?? true)) {
                $newStudentData['photo'] = null;
            }

            // Create the duplicate student
            $newStudent = Student::create($newStudentData);

            // Handle user account creation if requested
            if (($duplicateData['create_user_account'] ?? false) && !$newStudent->user_id) {
                $this->createUserAccountForStudent($newStudent);
            }

            Log::info('Student duplicated with custom data', [
                'original_student_id' => $student->id,
                'new_student_id' => $newStudent->id,
                'original_student_number' => $student->student_number,
                'new_student_number' => $newStudent->student_number,
                'original_email' => $student->email,
                'new_email' => $newStudent->email,
                'create_user_account' => $duplicateData['create_user_account'] ?? false,
                'copy_photo' => $duplicateData['copy_photo'] ?? true,
                'duplicated_by' => auth()->id()
            ]);

            return $newStudent->load(['programStudy', 'creator', 'user']);
        });
    }

    /**
     * Create user account for a student.
     */
    private function createUserAccountForStudent(Student $student): void
    {
        // Use student number as default password, with fallback if it's null or empty
        $defaultPassword = $student->student_number ?? 'password123';

        // If student number is empty, use a more secure default password
        if (empty(trim($defaultPassword))) {
            $defaultPassword = 'stud' . $student->id . '@2024'; // e.g., stud123@2024
        }

        $userData = [
            'name' => $student->name,
            'email' => $student->email,
            'password' => bcrypt($defaultPassword),
            'email_verified_at' => now(),
        ];

        $user = \App\Models\User::create($userData);

        // Assign student role
        $studentRole = \Spatie\Permission\Models\Role::where('name', 'student')->first();
        if ($studentRole) {
            $user->assignRole($studentRole);
        }

        // Update student with user_id
        $student->update(['user_id' => $user->id]);
    }

    /**
     * Create user account for a student (public method for API).
     */
    public function createUserAccount(Student $student): array
    {
        try {
            // Check if user account already exists
            if ($student->user_id) {
                return [
                    'success' => false,
                    'message' => 'Student already has a user account',
                    'user' => $student->user
                ];
            }

            // Check if email already exists in users table
            $existingUser = \App\Models\User::where('email', $student->email)->first();
            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'Email already exists in users table',
                    'user' => $existingUser
                ];
            }

            $this->createUserAccountForStudent($student);

            // Refresh the student to get the updated user relationship
            $student->refresh();

            // Calculate what password was actually used (same logic as in createUserAccountForStudent)
            $actualPassword = $student->student_number ?? 'password123';
            if (empty(trim($actualPassword))) {
                $actualPassword = 'stud' . $student->id . '@2024';
            }

            return [
                'success' => true,
                'message' => 'User account created successfully. Default password: ' . $actualPassword,
                'user' => $student->user,
                'student' => $student->load(['programStudy', 'user'])
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create user account: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk create user accounts for multiple students.
     */
    public function bulkCreateUserAccounts(array $studentIds): array
    {
        $results = [
            'success_count' => 0,
            'failed_count' => 0,
            'skipped_count' => 0,
            'details' => []
        ];

        $students = Student::whereIn('id', $studentIds)->get();

        foreach ($students as $student) {
            try {
                // Check if user account already exists
                if ($student->user_id) {
                    $results['skipped_count']++;
                    $results['details'][] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'status' => 'skipped',
                        'message' => 'Student already has a user account'
                    ];
                    continue;
                }

                // Check if email already exists in users table
                $existingUser = \App\Models\User::where('email', $student->email)->first();
                if ($existingUser) {
                    $results['skipped_count']++;
                    $results['details'][] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'status' => 'skipped',
                        'message' => 'Email already exists in users table'
                    ];
                    continue;
                }

                $this->createUserAccountForStudent($student);
                $student->refresh();

                $results['success_count']++;
                $results['details'][] = [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'user_id' => $student->user_id,
                    'status' => 'success',
                    'message' => 'User account created successfully'
                ];

            } catch (\Exception $e) {
                $results['failed_count']++;
                $results['details'][] = [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'status' => 'failed',
                    'message' => 'Failed to create user account: ' . $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Get trashed (soft deleted) students.
     */
    public function getTrashedStudents(array $filters = [], int $perPage = 15, string $sortBy = 'deleted_at', string $sortDirection = 'desc'): array
    {
        $query = Student::onlyTrashed()
            ->with(['programStudy', 'creator', 'updater']);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('student_number', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['batch_year'])) {
            $query->where('batch_year', $filters['batch_year']);
        }

        if (!empty($filters['class'])) {
            $query->where('class', $filters['class']);
        }

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortDirection);

        $students = $query->paginate($perPage);

        return [
            'data' => $students,
            'message' => 'Trashed students retrieved successfully',
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
                'from' => $students->firstItem(),
                'to' => $students->lastItem(),
            ]
        ];
    }

    /**
     * Bulk restore students (restore soft deleted students).
     */
    public function bulkRestoreStudents(array $studentIds): int
    {
        return DB::transaction(function () use ($studentIds) {
            $restoredCount = 0;

            Student::withTrashed()->whereIn('id', $studentIds)->get()->each(function ($student) use (&$restoredCount) {
                if ($student->restore()) {
                    $restoredCount++;

                    Log::info('Student restored (bulk)', [
                        'student_id' => $student->id,
                        'student_number' => $student->student_number,
                        'restored_by' => auth()->id()
                    ]);
                }
            });

            Log::info('Bulk restore completed', [
                'student_count' => $restoredCount,
                'restored_by' => auth()->id()
            ]);

            return $restoredCount;
        });
    }

    /**
     * Apply program study filtering based on user permissions.
     */
    private function applyProgramStudyFilter(Builder $query, array $filters = []): void
    {
        // If program_study_id is explicitly provided in filters, use it (admin override)
        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
            return;
        }

        // For authenticated users, apply program study filtering
        if (auth()->check()) {
            $user = auth()->user();

            // Admin can see all program studies
            if ($user->isAdmin()) {
                return; // No filtering for admin
            }

            // Non-admin users can only see their program study
            if ($user->program_study_id) {
                $query->where('program_study_id', $user->program_study_id);
            }
        }
    }
}