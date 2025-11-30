<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Student;
use App\Models\ClassStudent;
use App\Models\ProgramStudy;
use App\Services\AcademicYearService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ClassService
{
    /**
     * Get paginated list of classes with filtering.
     */
    public function getClasses(array $filters = [], int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'asc'): array
    {
        $query = Kelas::with(['programStudy', 'creator', 'updater']);

        // Apply program study filtering based on user permissions
        $this->applyProgramStudyFilter($query, $filters);

        // Apply academic year filtering (use active academic year as default)
        $this->applyAcademicYearFilter($query, $filters);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('room_number', 'like', "%{$search}%")
                  ->orWhere('academic_year', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['batch_year'])) {
            $query->where('batch_year', $filters['batch_year']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        if (isset($filters['is_active'])) {
            $isActive = $filters['is_active'];
            if (is_string($isActive)) {
                $isActive = $isActive === 'true' || $isActive === '1';
            }
            $query->where('is_active', $isActive);
        }

        if (!empty($filters['has_capacity'])) {
            $query->whereRaw('current_students < capacity');
        }

        // Apply sorting
        $allowedSorts = ['name', 'code', 'batch_year', 'semester', 'academic_year', 'current_students', 'capacity', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'name';
        $query->orderBy($sortBy, $sortDirection);

        $classes = $query->paginate($perPage);

        return [
            'data' => $classes,
            'message' => 'Classes retrieved successfully',
            'meta' => [
                'current_page' => $classes->currentPage(),
                'last_page' => $classes->lastPage(),
                'per_page' => $classes->perPage(),
                'total' => $classes->total(),
            ]
        ];
    }

    /**
     * Create a new class.
     */
    public function createClass(array $data): Kelas
    {
        return DB::transaction(function () use ($data) {
            // Generate class code if not provided
            if (empty($data['code'])) {
                $programStudy = ProgramStudy::find($data['program_study_id']);
                $data['code'] = $this->generateClassCode(
                    $programStudy->code,
                    $data['batch_year'],
                    $data['semester'],
                    $data['academic_year']
                );
            }

            // Set current user as creator
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            $class = Kelas::create($data);

            Log::info('Class created', [
                'class_id' => $class->id,
                'class_code' => $class->code,
                'created_by' => auth()->id()
            ]);

            return $class->load(['programStudy', 'creator']);
        });
    }

    /**
     * Update an existing class.
     */
    public function updateClass(Kelas $class, array $data): Kelas
    {
        return DB::transaction(function () use ($class, $data) {
            $data['updated_by'] = auth()->id();

            // Update capacity and adjust if needed
            if (isset($data['capacity']) && $data['capacity'] < $class->current_students) {
                throw new \Exception('Cannot set capacity lower than current number of students');
            }

            $class->update($data);

            Log::info('Class updated', [
                'class_id' => $class->id,
                'class_code' => $class->code,
                'updated_by' => auth()->id()
            ]);

            return $class->load(['programStudy', 'creator', 'updater']);
        });
    }

    /**
     * Delete a class.
     */
    public function deleteClass(Kelas $class): bool
    {
        return DB::transaction(function () use ($class) {
            // Check if class has students
            if ($class->current_students > 0) {
                throw new \Exception('Cannot delete class with enrolled students');
            }

            $class->delete();

            Log::info('Class deleted', [
                'class_id' => $class->id,
                'class_code' => $class->code,
                'deleted_by' => auth()->id()
            ]);

            return true;
        });
    }

    /**
     * Get class statistics.
     */
    public function getStatistics(array $filters = []): array
    {
        $query = Kelas::query();

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['batch_year'])) {
            $query->where('batch_year', $filters['batch_year']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        $totalClasses = $query->count();
        $activeClasses = $query->where('is_active', true)->count();
        $totalCapacity = $query->sum('capacity');
        $totalStudents = $query->sum('current_students');
        $averageUtilization = $totalCapacity > 0 ? round(($totalStudents / $totalCapacity) * 100, 1) : 0;

        $capacityStatus = [
            'full' => $query->whereRaw('current_students >= capacity')->count(),
            'available' => $query->whereRaw('current_students < capacity')->count(),
            'empty' => $query->where('current_students', 0)->count(),
        ];

        return [
            'total_classes' => $totalClasses,
            'active_classes' => $activeClasses,
            'total_capacity' => $totalCapacity,
            'total_students' => $totalStudents,
            'average_utilization' => $averageUtilization,
            'capacity_status' => $capacityStatus,
            'available_slots' => $totalCapacity - $totalStudents,
        ];
    }

    /**
     * Get available classes (with capacity).
     */
    public function getAvailableClasses(array $filters = []): array
    {
        $query = Kelas::with(['programStudy'])
            ->where('is_active', true)
            ->whereRaw('current_students < capacity');

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['batch_year'])) {
            $query->where('batch_year', $filters['batch_year']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        $classes = $query->get();

        return $classes->map(function ($class) {
            return [
                'id' => $class->id,
                'name' => $class->name,
                'code' => $class->code,
                'program_study' => $class->programStudy->name,
                'capacity' => $class->capacity,
                'current_students' => $class->current_students,
                'available_slots' => $class->available_slots,
                'capacity_percentage' => $class->capacity_percentage,
            ];
        })->toArray();
    }

    /**
     * Get students in a class.
     */
    public function getClassStudents(Kelas $class, array $filters = [], int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'asc'): array
    {
        $query = $class->students();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->wherePivot('status', $filters['status']);
        }

        // Apply sorting
        $allowedSorts = ['name', 'student_number', 'email', 'enrollment_date'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'name';
        $query->orderBy($sortBy, $sortDirection);

        $students = $query->paginate($perPage);

        return [
            'data' => $students,
            'message' => 'Class students retrieved successfully',
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ]
        ];
    }

    /**
     * Enroll students in a class.
     */
    public function enrollStudents(Kelas $class, array $studentIds, $enrollmentDate = null, $notes = null): array
    {
        return DB::transaction(function () use ($class, $studentIds, $enrollmentDate, $notes) {
            // Convert enrollmentDate to Carbon instance if it's a string
            if (is_string($enrollmentDate)) {
                $enrollmentDate = \Carbon\Carbon::parse($enrollmentDate);
            } else {
                $enrollmentDate = $enrollmentDate ?: now();
            }
            $enrolledStudents = [];
            $failedEnrollments = [];

            foreach ($studentIds as $studentId) {
                try {
                    $student = Student::findOrFail($studentId);

                    // Check if student is already enrolled
                    if ($class->students()->where('student_id', $studentId)->exists()) {
                        $failedEnrollments[] = [
                            'student_id' => $studentId,
                            'student_name' => $student->name,
                            'reason' => 'Already enrolled in this class'
                        ];
                        continue;
                    }

                    // Check capacity
                    if ($class->current_students >= $class->capacity) {
                        $failedEnrollments[] = [
                            'student_id' => $studentId,
                            'student_name' => $student->name,
                            'reason' => 'Class is full'
                        ];
                        continue;
                    }

                    // Enroll student
                    $class->students()->attach($studentId, [
                        'enrollment_date' => $enrollmentDate,
                        'status' => 'active',
                        'notes' => $notes,
                        'created_by' => auth()->id(),
                    ]);

                    $class->increment('current_students');

                    $enrolledStudents[] = [
                        'student_id' => $studentId,
                        'student_name' => $student->name,
                        'enrollment_date' => $enrollmentDate->format('Y-m-d')
                    ];

                    Log::info('Student enrolled in class', [
                        'class_id' => $class->id,
                        'student_id' => $studentId,
                        'enrolled_by' => auth()->id()
                    ]);

                } catch (\Exception $e) {
                    $failedEnrollments[] = [
                        'student_id' => $studentId,
                        'student_name' => $student->name ?? 'Unknown',
                        'reason' => $e->getMessage()
                    ];
                }
            }

            return [
                'enrolled_students' => $enrolledStudents,
                'failed_enrollments' => $failedEnrollments,
                'total_enrolled' => count($enrolledStudents),
                'total_failed' => count($failedEnrollments)
            ];
        });
    }

    /**
     * Remove student from class.
     */
    public function removeStudent(Kelas $class, int $studentId, $notes = null): array
    {
        return DB::transaction(function () use ($class, $studentId, $notes) {
            $student = $class->students()->where('student_id', $studentId)->first();

            if (!$student) {
                throw new \Exception('Student not found in this class');
            }

            // Completely remove the student from the class (delete from pivot table)
            $class->students()->detach($studentId);

            $class->decrement('current_students');

            Log::info('Student removed from class', [
                'class_id' => $class->id,
                'student_id' => $studentId,
                'removed_by' => auth()->id()
            ]);

            return [
                'student_id' => $studentId,
                'student_name' => $student->name,
                'message' => 'Student removed from class successfully'
            ];
        });
    }

    /**
     * Transfer student to another class.
     */
    public function transferStudent(Kelas $fromClass, int $studentId, int $toClassId, $notes = null): array
    {
        return DB::transaction(function () use ($fromClass, $studentId, $toClassId, $notes) {
            $toClass = Kelas::findOrFail($toClassId);

            // Check capacity in destination class
            if ($toClass->current_students >= $toClass->capacity) {
                throw new \Exception('Destination class is full');
            }

            $student = $fromClass->students()->where('student_id', $studentId)->first();
            if (!$student) {
                throw new \Exception('Student not found in source class');
            }

            $pivotData = $student->pivot;

            // Remove from current class
            $fromClass->students()->updateExistingPivot($studentId, [
                'status' => 'transferred',
                'notes' => $notes ?: 'Transferred to class ' . $toClass->name,
                'updated_by' => auth()->id(),
            ]);
            $fromClass->decrement('current_students');

            // Add to new class
            $toClass->students()->attach($studentId, [
                'enrollment_date' => $pivotData->enrollment_date,
                'status' => 'active',
                'notes' => 'Transferred from class ' . $fromClass->name,
                'created_by' => auth()->id(),
            ]);
            $toClass->increment('current_students');

            Log::info('Student transferred between classes', [
                'from_class_id' => $fromClass->id,
                'to_class_id' => $toClass->id,
                'student_id' => $studentId,
                'transferred_by' => auth()->id()
            ]);

            return [
                'student_id' => $studentId,
                'student_name' => $student->name,
                'from_class' => $fromClass->name,
                'to_class' => $toClass->name,
                'message' => 'Student transferred successfully'
            ];
        });
    }

    /**
     * Update student enrollment status.
     */
    public function updateStudentStatus(Kelas $class, int $studentId, string $status, $notes = null): array
    {
        return DB::transaction(function () use ($class, $studentId, $status, $notes) {
            $student = $class->students()->where('student_id', $studentId)->first();

            if (!$student) {
                throw new \Exception('Student not found in this class');
            }

            $oldStatus = $student->pivot->status;

            $class->students()->updateExistingPivot($studentId, [
                'status' => $status,
                'notes' => $notes,
                'updated_by' => auth()->id(),
            ]);

            // Update current students count based on status change
            if ($oldStatus === 'active' && $status !== 'active') {
                $class->decrement('current_students');
            } elseif ($oldStatus !== 'active' && $status === 'active') {
                $class->increment('current_students');
            }

            Log::info('Student status updated', [
                'class_id' => $class->id,
                'student_id' => $studentId,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'updated_by' => auth()->id()
            ]);

            return [
                'student_id' => $studentId,
                'student_name' => $student->name,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'message' => 'Student status updated successfully'
            ];
        });
    }

    /**
     * Bulk update classes.
     */
    public function bulkUpdateClasses(array $classIds, array $data): array
    {
        return DB::transaction(function () use ($classIds, $data) {
            $data['updated_by'] = auth()->id();

            $updatedCount = Kelas::whereIn('id', $classIds)->update($data);

            Log::info('Classes bulk updated', [
                'class_ids' => $classIds,
                'updated_count' => $updatedCount,
                'updated_by' => auth()->id()
            ]);

            return [
                'updated_count' => $updatedCount,
                'message' => 'Classes updated successfully'
            ];
        });
    }

    /**
     * Bulk delete classes.
     */
    public function bulkDeleteClasses(array $classIds): array
    {
        return DB::transaction(function () use ($classIds) {
            $classes = Kelas::whereIn('id', $classIds)->get();

            // Check for enrolled students
            $classesWithStudents = $classes->filter(function ($class) {
                return $class->current_students > 0;
            });

            if ($classesWithStudents->isNotEmpty()) {
                throw new \Exception('Cannot delete classes with enrolled students');
            }

            $deletedCount = Kelas::whereIn('id', $classIds)->delete();

            Log::info('Classes bulk deleted', [
                'class_ids' => $classIds,
                'deleted_count' => $deletedCount,
                'deleted_by' => auth()->id()
            ]);

            return [
                'deleted_count' => $deletedCount,
                'message' => 'Classes deleted successfully'
            ];
        });
    }

    /**
     * Get classes by program study.
     */
    public function getClassesByProgramStudy(ProgramStudy $programStudy, array $filters = []): array
    {
        $query = $programStudy->classes()->with(['creator', 'updater']);

        if (!empty($filters['batch_year'])) {
            $query->where('batch_year', $filters['batch_year']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        if (isset($filters['is_active'])) {
            $isActive = $filters['is_active'];
            if (is_string($isActive)) {
                $isActive = $isActive === 'true' || $isActive === '1';
            }
            $query->where('is_active', $isActive);
        }

        $classes = $query->orderBy('name')->get();

        return $classes->toArray();
    }

    /**
     * Get classes by batch year.
     */
    public function getClassesByBatchYear(int $batchYear, array $filters = []): array
    {
        $query = Kelas::where('batch_year', $batchYear)->with(['programStudy', 'creator']);

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        if (isset($filters['is_active'])) {
            $isActive = $filters['is_active'];
            if (is_string($isActive)) {
                $isActive = $isActive === 'true' || $isActive === '1';
            }
            $query->where('is_active', $isActive);
        }

        $classes = $query->orderBy('name')->get();

        return $classes->toArray();
    }

    /**
     * Get classes by academic year.
     */
    public function getClassesByAcademicYear(string $academicYear, array $filters = []): array
    {
        $query = Kelas::where('academic_year', $academicYear)->with(['programStudy', 'creator']);

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['batch_year'])) {
            $query->where('batch_year', $filters['batch_year']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (isset($filters['is_active'])) {
            $isActive = $filters['is_active'];
            if (is_string($isActive)) {
                $isActive = $isActive === 'true' || $isActive === '1';
            }
            $query->where('is_active', $isActive);
        }

        $classes = $query->orderBy('name')->get();

        return $classes->toArray();
    }

    /**
     * Get enrollment report.
     */
    public function getEnrollmentReport(array $filters = []): array
    {
        $query = Kelas::with(['programStudy', 'students']);

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['batch_year'])) {
            $query->where('batch_year', $filters['batch_year']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        $classes = $query->get();

        $report = [];
        foreach ($classes as $class) {
            $report[] = [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'class_code' => $class->code,
                'program_study' => $class->programStudy->name,
                'batch_year' => $class->batch_year,
                'semester' => $class->semester,
                'academic_year' => $class->academic_year,
                'capacity' => $class->capacity,
                'current_students' => $class->current_students,
                'available_slots' => $class->available_slots,
                'capacity_percentage' => $class->capacity_percentage,
                'capacity_status' => $class->capacity_status,
                'is_active' => $class->is_active,
                'room_number' => $class->room_number,
            ];
        }

        return [
            'classes' => $report,
            'summary' => $this->getStatistics($filters)
        ];
    }

    /**
     * Generate class codes.
     */
    public function generateClassCodes(int $programStudyId, int $batchYear, string $semester, string $academicYear, int $classCount, int $capacityPerClass): array
    {
        return DB::transaction(function () use ($programStudyId, $batchYear, $semester, $academicYear, $classCount, $capacityPerClass) {
            $programStudy = ProgramStudy::findOrFail($programStudyId);
            $createdClasses = [];

            for ($i = 1; $i <= $classCount; $i++) {
                $className = $programStudy->name . ' ' . chr(64 + $i); // A, B, C, etc.
                $classCode = $this->generateClassCode($programStudy->code, $batchYear, $semester, $academicYear, $i);

                $class = Kelas::create([
                    'name' => $className,
                    'code' => $classCode,
                    'program_study_id' => $programStudyId,
                    'batch_year' => $batchYear,
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                    'capacity' => $capacityPerClass,
                    'current_students' => 0,
                    'is_active' => true,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                $createdClasses[] = $class->load('programStudy');
            }

            Log::info('Class codes generated', [
                'program_study_id' => $programStudyId,
                'batch_year' => $batchYear,
                'semester' => $semester,
                'academic_year' => $academicYear,
                'class_count' => $classCount,
                'created_by' => auth()->id()
            ]);

            return [
                'created_classes' => $createdClasses,
                'message' => 'Class codes generated successfully'
            ];
        });
    }

    /**
     * Auto-enroll students to classes.
     */
    public function autoEnrollStudents(int $programStudyId, int $batchYear, string $semester, string $academicYear): array
    {
        return DB::transaction(function () use ($programStudyId, $batchYear, $semester, $academicYear) {
            // Get classes for this program, batch, semester, and academic year
            $classes = Kelas::where('program_study_id', $programStudyId)
                ->where('batch_year', $batchYear)
                ->where('semester', $semester)
                ->where('academic_year', $academicYear)
                ->where('is_active', true)
                ->whereRaw('current_students < capacity')
                ->get();

            if ($classes->isEmpty()) {
                throw new \Exception('No available classes found for the specified criteria');
            }

            // Get students that need to be enrolled
            $students = Student::where('program_study_id', $programStudyId)
                ->where('batch_year', $batchYear)
                ->where('status', 'active')
                ->where('is_active', true)
                ->whereDoesntHave('classes', function ($query) use ($academicYear) {
                    $query->where('academic_year', $academicYear)
                          ->wherePivot('status', 'active');
                })
                ->get();

            if ($students->isEmpty()) {
                return [
                    'enrolled_students' => [],
                    'unassigned_students' => [],
                    'message' => 'No students found that need enrollment'
                ];
            }

            $enrolledStudents = [];
            $unassignedStudents = [];
            $classIterator = $classes->cycle();

            foreach ($students as $student) {
                $class = null;
                $attempts = 0;
                $maxAttempts = $classes->count();

                // Try to find a class with capacity
                while ($attempts < $maxAttempts) {
                    $potentialClass = $classIterator->current();

                    if ($potentialClass->current_students < $potentialClass->capacity) {
                        $class = $potentialClass;
                        break;
                    }

                    $classIterator->next();
                    $attempts++;
                }

                if ($class) {
                    // Enroll student
                    $class->students()->attach($student->id, [
                        'enrollment_date' => now(),
                        'status' => 'active',
                        'notes' => 'Auto-enrolled',
                        'created_by' => auth()->id(),
                    ]);

                    $class->increment('current_students');

                    $enrolledStudents[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'student_number' => $student->student_number,
                        'class_name' => $class->name,
                        'class_code' => $class->code,
                    ];

                    Log::info('Student auto-enrolled', [
                        'class_id' => $class->id,
                        'student_id' => $student->id,
                        'enrolled_by' => auth()->id()
                    ]);
                } else {
                    $unassignedStudents[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->name,
                        'student_number' => $student->student_number,
                        'reason' => 'No available class capacity'
                    ];
                }
            }

            return [
                'enrolled_students' => $enrolledStudents,
                'unassigned_students' => $unassignedStudents,
                'total_enrolled' => count($enrolledStudents),
                'total_unassigned' => count($unassignedStudents),
                'message' => 'Auto-enrollment completed successfully'
            ];
        });
    }

    /**
     * Get trashed (deleted) classes.
     */
    public function getTrashedClasses(array $filters = [], int $perPage = 15, string $sortBy = 'deleted_at', string $sortDirection = 'desc'): array
    {
        $query = Kelas::onlyTrashed()->with(['programStudy', 'creator', 'updater']);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('room_number', 'like', "%{$search}%")
                  ->orWhere('academic_year', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['batch_year'])) {
            $query->where('batch_year', $filters['batch_year']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
        }

        // Apply sorting
        $allowedSorts = ['name', 'code', 'batch_year', 'semester', 'academic_year', 'deleted_at', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'deleted_at';
        $query->orderBy($sortBy, $sortDirection);

        $classes = $query->paginate($perPage);

        return [
            'data' => $classes,
            'message' => 'Trashed classes retrieved successfully',
            'meta' => [
                'current_page' => $classes->currentPage(),
                'last_page' => $classes->lastPage(),
                'per_page' => $classes->perPage(),
                'total' => $classes->total(),
            ]
        ];
    }

    /**
     * Restore a deleted class.
     */
    public function restoreClass(Kelas $class): Kelas
    {
        return DB::transaction(function () use ($class) {
            $class->restore();

            Log::info('Class restored', [
                'class_id' => $class->id,
                'class_code' => $class->code,
                'restored_by' => auth()->id()
            ]);

            return $class->load(['programStudy', 'creator', 'updater']);
        });
    }

    /**
     * Force delete a class permanently.
     */
    public function forceDeleteClass(Kelas $class): bool
    {
        return DB::transaction(function () use ($class) {
            // Delete related student enrollments permanently
            $class->students()->detach();

            // Force delete the class
            $class->forceDelete();

            Log::info('Class force deleted', [
                'class_id' => $class->id,
                'class_code' => $class->code,
                'deleted_by' => auth()->id()
            ]);

            return true;
        });
    }

    /**
     * Bulk restore classes.
     */
    public function bulkRestore(array $classIds): array
    {
        return DB::transaction(function () use ($classIds) {
            $classes = Kelas::onlyTrashed()->whereIn('id', $classIds)->get();
            $restoredCount = 0;
            $failedRestores = [];

            foreach ($classes as $class) {
                try {
                    $class->restore();
                    $restoredCount++;

                    Log::info('Class bulk restored', [
                        'class_id' => $class->id,
                        'class_code' => $class->code,
                        'restored_by' => auth()->id()
                    ]);
                } catch (\Exception $e) {
                    $failedRestores[] = [
                        'class_id' => $class->id,
                        'class_code' => $class->code,
                        'reason' => $e->getMessage()
                    ];
                }
            }

            return [
                'restored_count' => $restoredCount,
                'failed_restores' => $failedRestores,
                'message' => 'Bulk restore completed successfully'
            ];
        });
    }

    /**
     * Bulk force delete classes.
     */
    public function bulkForceDelete(array $classIds): array
    {
        return DB::transaction(function () use ($classIds) {
            $classes = Kelas::onlyTrashed()->whereIn('id', $classIds)->get();
            $deletedCount = 0;
            $failedDeletes = [];

            foreach ($classes as $class) {
                try {
                    // Delete related student enrollments permanently
                    $class->students()->detach();

                    // Force delete the class
                    $class->forceDelete();
                    $deletedCount++;

                    Log::info('Class bulk force deleted', [
                        'class_id' => $class->id,
                        'class_code' => $class->code,
                        'deleted_by' => auth()->id()
                    ]);
                } catch (\Exception $e) {
                    $failedDeletes[] = [
                        'class_id' => $class->id,
                        'class_code' => $class->code,
                        'reason' => $e->getMessage()
                    ];
                }
            }

            return [
                'deleted_count' => $deletedCount,
                'failed_deletes' => $failedDeletes,
                'message' => 'Bulk force delete completed successfully'
            ];
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

    /**
     * Apply academic year filtering (use active academic year as default).
     */
    private function applyAcademicYearFilter(Builder $query, array $filters = []): void
    {
        // If academic_year is explicitly provided in filters, use it
        if (!empty($filters['academic_year'])) {
            $query->where('academic_year', $filters['academic_year']);
            return;
        }

        // Otherwise, filter by active academic year
        $activeAcademicYear = AcademicYearService::getCurrentActiveAcademicYearId();
        if ($activeAcademicYear) {
            $query->where('academic_year', $activeAcademicYear);
        }
    }

    /**
     * Generate unique class code.
     */
    private function generateClassCode(string $programCode, int $batchYear, string $semester, string $academicYear, int $classNumber = 1): string
    {
        $semesterCode = strtoupper(substr($semester, 0, 1));
        $sequence = str_pad($classNumber, 2, '0', STR_PAD_LEFT);

        $baseCode = $programCode . $batchYear . $semesterCode . $sequence;

        // Ensure uniqueness
        $counter = 1;
        $code = $baseCode;

        while (Kelas::where('code', $code)->exists()) {
            $code = $baseCode . '-' . $counter;
            $counter++;
        }

        return $code;
    }
}