<?php

namespace App\Services;

use App\Models\Lecturer;
use App\Models\ProgramStudy;
use App\Models\Course;
use App\Models\CourseLecturer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LecturersExport;
use App\Imports\LecturersImport;

class LecturerService
{
    /**
     * Get paginated list of lecturers with filtering.
     */
    public function getLecturers(array $filters = [], int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'asc'): array
    {
        $query = Lecturer::with(['programStudy', 'user', 'creator', 'updater']);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        if (!empty($filters['faculty'])) {
            $query->where('faculty', $filters['faculty']);
        }

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        if (!empty($filters['rank'])) {
            $query->where('rank', $filters['rank']);
        }

        if (!empty($filters['highest_education'])) {
            $query->where('highest_education', $filters['highest_education']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['specialization'])) {
            $query->where('specialization', 'like', "%{$filters['specialization']}%");
        }

        // Apply sorting
        $allowedSorts = ['name', 'employee_number', 'email', 'rank', 'hire_date', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'name';
        $query->orderBy($sortBy, $sortDirection);

        $lecturers = $query->paginate($perPage);

        return [
            'data' => $lecturers,
            'message' => 'Lecturers retrieved successfully',
            'meta' => [
                'current_page' => $lecturers->currentPage(),
                'last_page' => $lecturers->lastPage(),
                'per_page' => $lecturers->perPage(),
                'total' => $lecturers->total(),
            ]
        ];
    }

    /**
     * Create a new lecturer.
     */
    public function createLecturer(array $data): Lecturer
    {
        return DB::transaction(function () use ($data) {
            // Generate employee number if not provided
            if (empty($data['employee_number'])) {
                $faculty = $data['faculty'] ?? 'FST';
                $year = date('Y');
                $sequence = Lecturer::whereYear('hire_date', $year)->count() + 1;
                $data['employee_number'] = $faculty . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }

            // Set current user as creator
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            // Handle JSON fields
            if (isset($data['specialization']) && is_array($data['specialization'])) {
                $data['specialization'] = json_encode($data['specialization']);
            }

            if (isset($data['office_hours']) && is_array($data['office_hours'])) {
                $data['office_hours'] = json_encode($data['office_hours']);
            }

            if (isset($data['certifications']) && is_array($data['certifications'])) {
                $data['certifications'] = json_encode($data['certifications']);
            }

            if (isset($data['research_interests']) && is_array($data['research_interests'])) {
                $data['research_interests'] = json_encode($data['research_interests']);
            }

            if (isset($data['publications']) && is_array($data['publications'])) {
                $data['publications'] = json_encode($data['publications']);
            }

            $lecturer = Lecturer::create($data);

            Log::info('Lecturer created', [
                'lecturer_id' => $lecturer->id,
                'employee_number' => $lecturer->employee_number,
                'created_by' => auth()->id()
            ]);

            return $lecturer->load(['programStudy', 'creator']);
        });
    }

    /**
     * Update an existing lecturer.
     */
    public function updateLecturer(Lecturer $lecturer, array $data): Lecturer
    {
        return DB::transaction(function () use ($lecturer, $data) {
            $data['updated_by'] = auth()->id();

            // Handle JSON fields
            if (isset($data['specialization']) && is_array($data['specialization'])) {
                $data['specialization'] = json_encode($data['specialization']);
            }

            if (isset($data['office_hours']) && is_array($data['office_hours'])) {
                $data['office_hours'] = json_encode($data['office_hours']);
            }

            if (isset($data['certifications']) && is_array($data['certifications'])) {
                $data['certifications'] = json_encode($data['certifications']);
            }

            if (isset($data['research_interests']) && is_array($data['research_interests'])) {
                $data['research_interests'] = json_encode($data['research_interests']);
            }

            if (isset($data['publications']) && is_array($data['publications'])) {
                $data['publications'] = json_encode($data['publications']);
            }

            $lecturer->update($data);

            Log::info('Lecturer updated', [
                'lecturer_id' => $lecturer->id,
                'employee_number' => $lecturer->employee_number,
                'updated_by' => auth()->id()
            ]);

            return $lecturer->load(['programStudy', 'creator', 'updater']);
        });
    }

    /**
     * Delete a lecturer (soft delete).
     */
    public function deleteLecturer(Lecturer $lecturer): bool
    {
        return DB::transaction(function () use ($lecturer) {
            $lecturerId = $lecturer->id;
            $employeeNumber = $lecturer->employee_number;

            $deleted = $lecturer->delete();

            if ($deleted) {
                Log::info('Lecturer deleted', [
                    'lecturer_id' => $lecturerId,
                    'employee_number' => $employeeNumber,
                    'deleted_by' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Restore a deleted lecturer.
     */
    public function restoreLecturer(int $lecturerId): Lecturer
    {
        $lecturer = Lecturer::withTrashed()->findOrFail($lecturerId);

        DB::transaction(function () use ($lecturer) {
            $lecturer->restore();
            $lecturer->update(['updated_by' => auth()->id()]);

            Log::info('Lecturer restored', [
                'lecturer_id' => $lecturer->id,
                'employee_number' => $lecturer->employee_number,
                'restored_by' => auth()->id()
            ]);
        });

        return $lecturer->load(['programStudy', 'creator', 'updater']);
    }

    /**
     * Permanently delete a lecturer.
     */
    public function forceDeleteLecturer(int $lecturerId): bool
    {
        $lecturer = Lecturer::withTrashed()->findOrFail($lecturerId);

        return DB::transaction(function () use ($lecturer) {
            $lecturerId = $lecturer->id;
            $employeeNumber = $lecturer->employee_number;

            $deleted = $lecturer->forceDelete();

            if ($deleted) {
                Log::warning('Lecturer permanently deleted', [
                    'lecturer_id' => $lecturerId,
                    'employee_number' => $employeeNumber,
                    'deleted_by' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Get lecturer statistics.
     */
    public function getLecturerStatistics(?int $programStudyId = null): array
    {
        $query = Lecturer::query();

        if ($programStudyId) {
            $query->where('program_study_id', $programStudyId);
        }

        $total = $query->count();
        $active = $query->where('status', 'active')->where('is_active', true)->count();
        $inactive = $query->where('status', 'inactive')->count();
        $onLeave = $query->where('status', 'on_leave')->count();
        $terminated = $query->where('status', 'terminated')->count();
        $retired = $query->where('status', 'retired')->count();

        $employmentTypeStats = $query->selectRaw('employment_type, COUNT(*) as count')
            ->groupBy('employment_type')
            ->pluck('count', 'employment_type')
            ->toArray();

        $educationStats = $query->selectRaw('highest_education, COUNT(*) as count')
            ->whereNotNull('highest_education')
            ->groupBy('highest_education')
            ->pluck('count', 'highest_education')
            ->toArray();

        $rankStats = $query->selectRaw('rank, COUNT(*) as count')
            ->whereNotNull('rank')
            ->groupBy('rank')
            ->orderBy('rank')
            ->get()
            ->pluck('count', 'rank')
            ->toArray();

        $facultyStats = $query->selectRaw('faculty, COUNT(*) as count')
            ->groupBy('faculty')
            ->orderBy('faculty')
            ->get()
            ->pluck('count', 'faculty')
            ->toArray();

        // Calculate average service years
        $avgServiceYears = $query->whereNotNull('hire_date')
            ->selectRaw('AVG(DATEDIFF(NOW(), hire_date) / 365) as avg_service')
            ->value('avg_service');

        return [
            'total_lecturers' => $total,
            'by_status' => [
                'active' => $active,
                'inactive' => $inactive,
                'on_leave' => $onLeave,
                'terminated' => $terminated,
                'retired' => $retired,
            ],
            'by_employment_type' => $employmentTypeStats,
            'by_education' => $educationStats,
            'by_rank' => $rankStats,
            'by_faculty' => $facultyStats,
            'average_service_years' => round($avgServiceYears ?? 0, 1),
        ];
    }

    /**
     * Get lecturer teaching load.
     */
    public function getLecturerTeachingLoad(Lecturer $lecturer): array
    {
        $currentCourses = $lecturer->courses()
            ->where('academic_year', Carbon::now()->year)
            ->where('semester', Carbon::now()->month > 6 ? 'ganjil' : 'genap')
            ->with('course')
            ->get();

        $totalCredits = $currentCourses->sum(function ($course) {
            return $course->course->credits ?? 3;
        });

        $maxLoad = $lecturer->academic_load ?? 12;
        $workloadPercentage = ($totalCredits / $maxLoad) * 100;

        return [
            'lecturer' => $lecturer->load('programStudy'),
            'current_courses' => $currentCourses,
            'total_courses' => $currentCourses->count(),
            'total_credits' => $totalCredits,
            'max_academic_load' => $maxLoad,
            'workload_percentage' => round($workloadPercentage, 1),
            'available_capacity' => max(0, $maxLoad - $totalCredits),
            'is_overloaded' => $totalCredits > $maxLoad,
        ];
    }

    /**
     * Get available lecturers for course assignment.
     */
    public function getAvailableLecturers(Course $course): array
    {
        $lecturers = Lecturer::where('status', 'active')
            ->where('is_active', true)
            ->where(function ($query) use ($course) {
                $query->where('program_study_id', $course->program_study_id)
                    ->orWhere('specialization', 'like', "%{$course->course_name}%")
                    ->orWhere('specialization', 'like', "%{$course->description}%");
            })
            ->with(['programStudy'])
            ->get();

        $availableLecturers = $lecturers->filter(function ($lecturer) use ($course) {
            return $lecturer->canTeach($course);
        });

        return [
            'available_lecturers' => $availableLecturers,
            'total_available' => $availableLecturers->count(),
            'course' => $course,
        ];
    }

    /**
     * Assign course to lecturer.
     */
    public function assignCourseToLecturer(Lecturer $lecturer, Course $course, array $assignmentData = []): array
    {
        return DB::transaction(function () use ($lecturer, $course, $assignmentData) {
            $assignmentData = array_merge([
                'academic_year' => Carbon::now()->year,
                'semester' => Carbon::now()->month > 6 ? 'ganjil' : 'genap',
                'role' => 'lecturer',
            ], $assignmentData);

            $existingAssignment = CourseLecturer::where('lecturer_id', $lecturer->id)
                ->where('course_id', $course->id)
                ->where('academic_year', $assignmentData['academic_year'])
                ->where('semester', $assignmentData['semester'])
                ->first();

            if ($existingAssignment) {
                throw new \Exception('Lecturer already assigned to this course for the current period');
            }

            $assignment = CourseLecturer::create([
                'lecturer_id' => $lecturer->id,
                'course_id' => $course->id,
                'role' => $assignmentData['role'],
                'academic_year' => $assignmentData['academic_year'],
                'semester' => $assignmentData['semester'],
                'created_by' => auth()->id(),
            ]);

            Log::info('Course assigned to lecturer', [
                'lecturer_id' => $lecturer->id,
                'course_id' => $course->id,
                'assignment_id' => $assignment->id,
                'assigned_by' => auth()->id()
            ]);

            return [
                'assignment' => $assignment->load(['lecturer', 'course']),
                'message' => 'Course assigned successfully',
            ];
        });
    }

    /**
     * Bulk update lecturers.
     */
    public function bulkUpdateLecturers(array $lecturerIds, array $updates): int
    {
        return DB::transaction(function () use ($lecturerIds, $updates) {
            $updates['updated_by'] = auth()->id();

            $updated = Lecturer::whereIn('id', $lecturerIds)
                ->update($updates);

            Log::info('Bulk lecturer update', [
                'lecturer_count' => $updated,
                'lecturer_ids' => $lecturerIds,
                'updates' => $updates,
                'updated_by' => auth()->id()
            ]);

            return $updated;
        });
    }

    /**
     * Import lecturers from file.
     */
    public function importLecturers($file, ?int $programStudyId = null, $user): array
    {
        try {
            $import = new LecturersImport($programStudyId, $user);
            Excel::import($import, $file);

            return [
                'success' => true,
                'imported_count' => $import->getImportedCount(),
                'failed_count' => $import->getFailedCount(),
                'errors' => $import->getErrors(),
            ];
        } catch (\Exception $e) {
            Log::error('Lecturer import failed', [
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
     * Export lecturers to file.
     */
    public function exportLecturers(array $filters = [], string $format = 'csv'): string
    {
        $lecturers = $this->getLecturersForExport($filters);

        $fileName = 'lecturers_' . date('Y_m_d_H_i_s') . '.' . $format;
        $filePath = 'exports/' . $fileName;

        if ($format === 'excel') {
            Excel::store(new LecturersExport($lecturers), $filePath, 'public', \Maatwebsite\Excel\Excel::XLSX);
        } else {
            Excel::store(new LecturersExport($lecturers), $filePath, 'public', \Maatwebsite\Excel\Excel::CSV);
        }

        return Storage::url($filePath);
    }

    /**
     * Get lecturers for export.
     */
    private function getLecturersForExport(array $filters): \Illuminate\Support\Collection
    {
        $query = Lecturer::with(['programStudy']);

        foreach ($filters as $key => $value) {
            if ($value) {
                $query->where($key, $value);
            }
        }

        return $query->get();
    }

    /**
     * Get lecturer search suggestions.
     */
    public function getLecturerSearchSuggestions(string $query, int $limit = 10): array
    {
        return Lecturer::where('name', 'like', "%{$query}%")
            ->orWhere('employee_number', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('specialization', 'like', "%{$query}%")
            ->limit($limit)
            ->get(['id', 'name', 'employee_number', 'email', 'rank', 'program_study_id'])
            ->load('programStudy:name,id')
            ->toArray();
    }

    /**
     * Get lecturers by availability for scheduling.
     */
    public function getLecturersForScheduling(string $day, string $time): array
    {
        $dayName = strtolower(date('l', strtotime($day)));

        $lecturers = Lecturer::where('status', 'active')
            ->where('is_active', true)
            ->where(function ($query) use ($dayName, $time) {
                $query->whereNull('office_hours')
                    ->orWhereJsonContains("office_hours->{$dayName}.start", '<=', $time)
                    ->orWhereJsonContains("office_hours->{$dayName}.end", '>=', $time);
            })
            ->with(['programStudy'])
            ->get();

        return [
            'available_lecturers' => $lecturers,
            'day' => $day,
            'time' => $time,
            'total_available' => $lecturers->count(),
        ];
    }

    /**
     * Update lecturer status.
     */
    public function updateLecturerStatus(Lecturer $lecturer, array $data): Lecturer
    {
        return DB::transaction(function () use ($lecturer, $data) {
            $updateData = [
                'status' => $data['status'],
                'updated_by' => auth()->id(),
            ];

            if ($data['status'] === 'terminated' || $data['status'] === 'retired') {
                $updateData['termination_date'] = $data['termination_date'] ?? now();
                $updateData['is_active'] = false;
            }

            if ($data['status'] === 'active') {
                $updateData['is_active'] = true;
            }

            $lecturer->update($updateData);

            // Add notes if provided
            if (!empty($data['notes'])) {
                $lecturer->notes = ($lecturer->notes ?? '') . "\n\n" . date('Y-m-d H:i:s') . ': ' . $data['notes'];
                $lecturer->save();
            }

            Log::info('Lecturer status updated', [
                'lecturer_id' => $lecturer->id,
                'employee_number' => $lecturer->employee_number,
                'old_status' => $lecturer->getOriginal('status'),
                'new_status' => $data['status'],
                'updated_by' => auth()->id()
            ]);

            return $lecturer->load(['programStudy']);
        });
    }

    /**
     * Get lecturer attendance summary.
     */
    public function getLecturerAttendanceSummary(Lecturer $lecturer, ?string $semester = null, ?string $academicYear = null): array
    {
        // Implementation would depend on Attendance model structure
        return [
            'lecturer' => $lecturer->only(['id', 'name', 'employee_number']),
            'period' => [
                'semester' => $semester,
                'academic_year' => $academicYear,
            ],
            'summary' => [
                'total_sessions' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
                'attendance_rate' => '0%',
            ],
        ];
    }

    /**
     * Get lecturers with highest workload.
     */
    public function getLecturersWithHighWorkload(int $threshold = 90): \Illuminate\Database\Eloquent\Collection
    {
        return Lecturer::where('status', 'active')
            ->where('is_active', true)
            ->with(['programStudy', 'courses'])
            ->get()
            ->filter(function ($lecturer) use ($threshold) {
                return $lecturer->workload_percentage >= $threshold;
            });
    }
}