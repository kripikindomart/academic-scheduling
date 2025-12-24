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
use DateTime;
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
            $status = $filters['status'];
            if (strtolower($status) === 'active') {
                $status = 'Aktif';
            } elseif (strtolower($status) === 'inactive') {
                $status = 'Tidak';
            }
            $query->where('status', $status);
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
            $isActive = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN);
            $query->where('is_active', $isActive);
        }

        if (isset($filters['only_trashed']) && filter_var($filters['only_trashed'], FILTER_VALIDATE_BOOLEAN)) {
            $query->onlyTrashed();
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
            // Set default values for required fields if not provided
            $defaults = [
                'gender' => 'L',
                'nationality' => 'Indonesia',
                'birth_date' => now()->subYears(25)->format('Y-m-d'), // Default 25 years old
                'birth_place' => '', // Default empty string
                'address' => '', // Default empty string
                'city' => '', // Default empty string
                'province' => '', // Default empty string
                'postal_code' => '', // Default empty string
                'religion' => '', // Default empty string
                'phone' => '', // Default empty string
                'status' => 'active',
                'employment_type' => 'permanent',
                'employment_status' => 'Active',
                'hire_date' => now()->format('Y-m-d'),
                'position' => 'Lecturer',
                'department' => '', // Default empty string
                'faculty' => 'Sekolah Pascasarjana',
                'is_active' => true,
                'created_by' => auth()->id(),
            ];

            $data = array_merge($defaults, $data);

            // Remove id_card_number if empty to avoid duplicate entry error
            if (empty($data['id_card_number'])) {
                unset($data['id_card_number']);
            }

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

            // Remove id_card_number if empty to avoid duplicate entry error
            if (isset($data['id_card_number']) && empty($data['id_card_number'])) {
                unset($data['id_card_number']);
            }

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
    public function getLecturerStatistics(?int $programStudyId = null, bool $includeTrash = false): array
    {
        $query = Lecturer::query();

        // Include only trashed records if requested
        if ($includeTrash) {
            $query->onlyTrashed();
        }

        if ($programStudyId) {
            $query->where('program_study_id', $programStudyId);
        }

        $total = $query->count();
        $active = $query->clone()->where('status', 'active')->where('is_active', true)->count();
        $inactive = $query->clone()->where('status', 'inactive')->count();
        $onLeave = $query->clone()->where('status', 'on_leave')->count();
        $terminated = $query->clone()->where('status', 'terminated')->count();
        $retired = $query->clone()->where('status', 'retired')->count();

        try {
            $employmentTypeStats = $query->clone()
                ->select('employment_type', \DB::raw('COUNT(*) as count'))
                ->groupBy('employment_type')
                ->pluck('count', 'employment_type')
                ->toArray();

            $educationStats = $query->clone()
                ->select('highest_education', \DB::raw('COUNT(*) as count'))
                ->whereNotNull('highest_education')
                ->groupBy('highest_education')
                ->pluck('count', 'highest_education')
                ->toArray();

            $rankStats = $query->clone()
                ->select('rank', \DB::raw('COUNT(*) as count'))
                ->whereNotNull('rank')
                ->groupBy('rank')
                ->orderBy('rank')
                ->pluck('count', 'rank')
                ->toArray();

            $facultyStats = $query->clone()
                ->select('faculty', \DB::raw('COUNT(*) as count'))
                ->groupBy('faculty')
                ->orderBy('faculty')
                ->pluck('count', 'faculty')
                ->toArray();

            // Calculate average service years
            $avgServiceYears = $query->clone()
                ->whereNotNull('hire_date')
                ->select(\DB::raw('AVG(DATEDIFF(NOW(), hire_date) / 365) as avg_service'))
                ->value('avg_service');
        } catch (\Exception $e) {
            // Log the error and provide fallback values
            \Log::error('Error in lecturer statistics query: ' . $e->getMessage());

            $employmentTypeStats = [];
            $educationStats = [];
            $rankStats = [];
            $facultyStats = [];
            $avgServiceYears = 0;
        }

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
     * Bulk delete lecturers.
     */
    public function bulkDeleteLecturers(array $lecturerIds): int
    {
        return DB::transaction(function () use ($lecturerIds) {
            $deleted = Lecturer::whereIn('id', $lecturerIds)
                ->delete();

            Log::info('Bulk lecturer delete', [
                'lecturer_count' => $deleted,
                'lecturer_ids' => $lecturerIds,
                'deleted_by' => auth()->id()
            ]);

            return $deleted;
        });
    }

    /**
     * Import lecturers from file.
     */
    public function importLecturers($file, $user, ?int $programStudyId = null): array
    {
        try {
            $import = new LecturersImport($programStudyId, $user);
            Excel::import($import, $file);

            // Process actual import from validated data
            $results = $import->getResults();
            $importedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $failedCount = 0;

            // Process all valid data
            foreach ($results['all_data'] as $data) {
                if ($data['is_valid'] && !$data['is_duplicate']) {
                    try {
                        // Create lecturer from validated data
                        $lecturer = $this->createLecturerFromValidatedData($data['mapped_data'], $user, $programStudyId);
                        if ($lecturer) {
                            $importedCount++;
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to import lecturer row', [
                            'row_number' => $data['row_number'],
                            'data' => $data['mapped_data'],
                            'error' => $e->getMessage()
                        ]);
                        $failedCount++;
                    }
                } elseif ($data['is_duplicate']) {
                    $skippedCount++;
                } else {
                    $failedCount++;
                }
            }

            return [
                'success' => true,
                'imported' => $importedCount,
                'updated' => $updatedCount,
                'skipped' => $skippedCount,
                'failed' => $failedCount,
                'total_processed' => $importedCount + $updatedCount + $skippedCount + $failedCount,
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
     * Create lecturer from validated data
     */
    public function createLecturerFromValidatedData(array $data, $user, ?int $programStudyId = null): Lecturer
    {
        // Validate email format - same as in LecturersImport
        if (!empty($data['email_wajib']) && !filter_var($data['email_wajib'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Format email tidak valid: " . $data['email_wajib']);
        }

        // Map enum values
        $gender = $this->mapGender($data['jenis_kelamin'] ?? '');
        $employmentType = $this->mapEmploymentType($data['jenis_pegawai_wajib'] ?? '');
        $status = $this->mapStatus($data['status_dosen_wajib'] ?? '');

        // Validate gender mapping
        if (!empty($data['jenis_kelamin']) && empty($gender)) {
            throw new \Exception("Jenis kelamin tidak valid. Gunakan 'L' atau 'P'.");
        }

        // Find program study if specified - STRICT VALIDATION
        $programStudyId = $programStudyId;
        if (!$programStudyId && (!empty($data['departemen']) || !empty($data['fakultas']))) {
            $departemen = trim($data['departemen'] ?? '');
            $fakultas = trim($data['fakultas'] ?? '');

            Log::info('Looking for program study', [
                'departemen' => $departemen,
                'fakultas' => $fakultas
            ]);

            // Try exact match first, then partial match
            $programStudy = ProgramStudy::where(function($query) use ($departemen, $fakultas) {
                if (!empty($departemen)) {
                    // Exact match first
                    $query->where('name', '=', $departemen);
                }
            })->orWhere(function($query) use ($departemen, $fakultas) {
                if (!empty($departemen)) {
                    // Partial match for departemen
                    $query->where('name', 'like', '%' . $departemen . '%');
                }
            })->orWhere(function($query) use ($fakultas) {
                if (!empty($fakultas)) {
                    // Match by faculty
                    $query->where('faculty', 'like', '%' . $fakultas . '%');
                }
            })->first();

            if (!$programStudy) {
                // Program study not found - throw error
                $availablePrograms = ProgramStudy::select('name', 'faculty')->get()->toArray();
                $programList = implode(', ', array_map(fn($p) => $p['name'], $availablePrograms));

                throw new \Exception(
                    "Program studi tidak ditemukan. '" . ($departemen ?: $fakultas) . "' tidak ada di database. " .
                    "Program studi yang tersedia: " . $programList
                );
            }

            $programStudyId = $programStudy->id;

            Log::info('Program study found', [
                'program_study_id' => $programStudyId,
                'program_study_name' => $programStudy->name,
                'faculty' => $programStudy->faculty
            ]);
        }

        return Lecturer::create([
            'employee_number' => $data['nip_nidn_wajib'] ?? '',
            'name' => $data['nama_wajib'] ?? '',
            'email' => $data['email_wajib'] ?? '',
            'phone' => $data['no_hp_wajib'] ?? '',
            'gender' => $gender,
            'birth_date' => $this->formatDate($data['tanggal_lahir'] ?? null),
            'birth_place' => $data['tempat_lahir'] ?? '',
            'address' => $data['alamat'] ?? '',
            'city' => $data['kota'] ?? '',
            'province' => $data['provinsi'] ?? '',
            'postal_code' => $data['kode_pos'] ?? '',
            'nationality' => $data['kebangsaan'] ?: 'Indonesia',
            'religion' => $data['agama'] ?? '',
            'blood_type' => $this->mapBloodType($data['golongan_darah'] ?? ''),
            'id_card_number' => $data['no_ktp'] ?? '',
            'status' => $status,
            'employment_status' => $data['status_kepegawaian_wajib'] ?? '',
            'employment_type' => $employmentType,
            'hire_date' => $this->formatDate($data['tanggal_masuk'] ?? null),
            'position' => $data['jabatan'] ?? '',
            'rank' => $data['gelar'] ?? '',
            'specialization' => $data['bidang_keahlian'] ?? '',
            'department' => $data['departemen'] ?? '',
            'faculty' => $data['fakultas'] ?? '',
            'highest_education' => $this->mapHighestEducation($data['pendidikan_tertinggi'] ?? ''),
            'education_institution' => $data['institusi_pendidikan'] ?? '',
            'education_major' => $data['jurusan_pendidikan'] ?? '',
            'graduation_year' => $this->formatYear($data['tahun_lulus'] ?? null),
            'office_room' => $data['no_ruang_kantor'] ?? '',
            'notes' => $data['catatan'] ?? '',
            'program_study_id' => $programStudyId,
            'is_active' => $status === 'Aktif',
            'created_by' => $user->id ?? null,
        ]);
    }

    /**
     * Helper methods for data mapping
     */
    private function mapGender($gender): string
    {
        if (empty($gender)) return 'L'; // Default to 'L' for empty

        $gender = strtolower(trim($gender));
        if (in_array($gender, ['l', 'laki-laki', 'pria', 'male'])) {
            return 'L';  // Fixed: Database enum is ['L', 'P']
        } elseif (in_array($gender, ['p', 'perempuan', 'female'])) {
            return 'P';  // Fixed: Database enum is ['L', 'P']
        }

        // If it's already L or P, return as-is
        if (in_array(strtoupper($gender), ['L', 'P'])) {
            return strtoupper($gender);
        }

        // Default fallback
        return 'L';
    }

    private function mapEmploymentType($type): string
    {
        if (empty($type)) return '';

        $type = strtolower(trim($type));
        if (in_array($type, ['tetap', 'permanent'])) {
            return 'Tetap';  // Fixed: Database enum is ['Tetap', 'Kontrak', 'Paruh', 'Tamu']
        } elseif (in_array($type, ['kontrak', 'contract'])) {
            return 'Kontrak';  // Fixed: Database enum is ['Tetap', 'Kontrak', 'Paruh', 'Tamu']
        } elseif (in_array($type, ['paruh waktu', 'part_time', 'paruh'])) {
            return 'Paruh';  // Fixed: Database enum is ['Tetap', 'Kontrak', 'Paruh', 'Tamu']
        } elseif (in_array($type, ['honorer', 'guest', 'tamu'])) {
            return 'Tamu';  // Fixed: Database enum is ['Tetap', 'Kontrak', 'Paruh', 'Tamu']
        }

        // If it's already a valid Indonesian enum value, return as-is
        if (in_array(ucfirst($type), ['Tetap', 'Kontrak', 'Paruh', 'Tamu'])) {
            return ucfirst($type);
        }

        return $type;
    }

    private function mapStatus($status): string
    {
        if (empty($status)) return '';

        $status = strtolower(trim($status));
        if (in_array($status, ['aktif', 'active'])) {
            return 'Aktif';
        } elseif (in_array($status, ['cuti', 'on_leave'])) {
            return 'Cuti';
        } elseif (in_array($status, ['tidak aktif', 'inactive'])) {
            return 'Tidak Aktif';
        }

        return $status;
    }

    private function mapBloodType($bloodType): ?string
    {
        if (empty($bloodType)) return null;

        $bloodType = strtoupper(trim($bloodType));
        if (in_array($bloodType, ['A', 'B', 'AB', 'O'])) {
            return $bloodType;
        }

        return null;
    }

    private function formatDate($date): ?string
    {
        if (empty($date)) return null;

        try {
            // Handle Excel serial numbers
            if (is_numeric($date)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            } else {
                // Try various date formats
                $dateObj = DateTime::createFromFormat('Y-m-d', $date);
                if (!$dateObj) {
                    $dateObj = DateTime::createFromFormat('d/m/Y', $date);
                }
                if (!$dateObj) {
                    $dateObj = DateTime::createFromFormat('d-m-Y', $date);
                }
                if (!$dateObj) {
                    $dateObj = DateTime::createFromFormat('m/d/Y', $date);
                }

                if ($dateObj) {
                    $date = $dateObj->format('Y-m-d');
                }
            }

            return $date;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function mapHighestEducation($education): string
    {
        if (empty($education)) return 'S1';

        $education = strtolower(trim($education));

        // Handle formats like "S1/S2/S3" or "S1,S2,S3"
        if (strpos($education, '/') !== false || strpos($education, ',') !== false) {
            // Split by / or , and take the highest level
            $levels = preg_split('[/,/]', $education);
            $validLevels = ['s1', 's2', 's3', 'strata-1', 'strata-2', 'strata-3'];

            // Find highest level
            $highestLevel = 'S1';
            foreach ($validLevels as $level) {
                if (in_array($level, $levels)) {
                    $highestLevel = strtoupper(str_replace('strata-', 'S', $level));
                }
            }

            return $highestLevel;
        }

        // Map individual values
        $mapping = [
            'strata-1' => 'S1',
            'strata-2' => 'S2',
            'strata-3' => 'S3',
            's1' => 'S1',
            's2' => 'S2',
            's3' => 'S3',
            'sarjana' => 'S1',
            'magister' => 'S2',
            'doktor' => 'S3'
        ];

        return $mapping[$education] ?? 'S1';
    }

    private function formatYear($year): ?int
    {
        if (empty($year)) return null;

        $year = (int) $year;
        return ($year >= 1950 && $year <= (date('Y') + 5)) ? $year : null;
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

    /**
     * Bulk restore lecturers from trash.
     */
    public function bulkRestoreLecturers(array $lecturerIds): int
    {
        return DB::transaction(function () use ($lecturerIds) {
            $restored = Lecturer::withTrashed()
                ->whereIn('id', $lecturerIds)
                ->restore();

            Log::info('Bulk lecturer restore', [
                'lecturer_count' => $restored,
                'lecturer_ids' => $lecturerIds,
                'restored_by' => auth()->id()
            ]);

            return $restored;
        });
    }

    /**
     * Bulk permanently delete lecturers.
     */
    public function bulkForceDeleteLecturers(array $lecturerIds): int
    {
        return DB::transaction(function () use ($lecturerIds) {
            $deleted = Lecturer::withTrashed()
                ->whereIn('id', $lecturerIds)
                ->forceDelete();

            Log::info('Bulk lecturer force delete', [
                'lecturer_count' => $deleted,
                'lecturer_ids' => $lecturerIds,
                'deleted_by' => auth()->id()
            ]);

            return $deleted;
        });
    }

    /**
     * Duplicate a lecturer.
     */
    public function duplicateLecturer(int $id, $user): array
    {
        return DB::transaction(function () use ($id, $user) {
            $originalLecturer = Lecturer::findOrFail($id);

            $newLecturer = $originalLecturer->replicate();
            $newLecturer->employee_number = $this->generateUniqueEmployeeNumber($originalLecturer->employee_number);
            $newLecturer->name = $this->generateDuplicateName($originalLecturer->name);
            $newLecturer->email = $this->generateUniqueEmail($originalLecturer->email);
            $newLecturer->id_card_number = $this->generateUniqueIdCardNumber($originalLecturer->id_card_number);
            $newLecturer->phone = $this->generateUniquePhone($originalLecturer->phone);
            $newLecturer->passport_number = $this->generateUniquePassportNumber($originalLecturer->passport_number);
            $newLecturer->created_by = $user->id;
            $newLecturer->updated_by = null;
            $newLecturer->created_at = now();
            $newLecturer->updated_at = null;

            $newLecturer->save();

            // Log activity
            Log::channel('activity')->info('Lecturer duplicated', [
                'original_lecturer_id' => $originalLecturer->id,
                'new_lecturer_id' => $newLecturer->id,
                'employee_number' => $newLecturer->employee_number,
                'name' => $newLecturer->name,
                'duplicated_by' => $user->id
            ]);

            return $newLecturer->load(['programStudy', 'creator'])->toArray();
        });
    }

    /**
     * Generate a unique employee number for duplicated lecturer.
     */
    private function generateUniqueEmployeeNumber(string $originalNumber): string
    {
        // Extract base number without any existing copy suffix
        $baseNumber = preg_replace('/_COPY_\d+$/', '', $originalNumber);

        // Find the highest existing copy number for this base number
        $existingPattern = $baseNumber . '_COPY_';
        $highestCopy = Lecturer::where('employee_number', 'like', $existingPattern . '%')
            ->get()
            ->map(function($lecturer) use ($baseNumber, $existingPattern) {
                $suffix = str_replace($existingPattern, '', $lecturer->employee_number);
                return is_numeric($suffix) ? (int)$suffix : 0;
            })
            ->max() ?? 0;

        $nextCopy = $highestCopy + 1;
        return $baseNumber . '_COPY_' . $nextCopy;
    }

    /**
     * Generate a unique email for duplicated lecturer.
     */
    private function generateUniqueEmail(string $originalEmail): string
    {
        // Extract base email without any existing copy suffix
        $baseEmail = preg_replace('/_COPY_\d+@/', '@', $originalEmail);

        // Extract parts before and after @ for the base email
        list($emailLocal, $emailDomain) = explode('@', $baseEmail);

        // Find the highest existing copy number for this base email
        $highestCopy = Lecturer::where('email', 'like', $emailLocal . '_COPY_%@' . $emailDomain)
            ->get()
            ->map(function($lecturer) use ($emailLocal, $emailDomain) {
                // Extract the copy number between _COPY_ and @
                $pattern = '/^' . preg_quote($emailLocal, '/') . '_COPY_(\d+)@' . preg_quote($emailDomain, '/') . '$/';
                if (preg_match($pattern, $lecturer->email, $matches)) {
                    return (int)$matches[1];
                }
                return 0;
            })
            ->max() ?? 0;

        $nextCopy = $highestCopy + 1;
        return $emailLocal . '_COPY_' . $nextCopy . '@' . $emailDomain;
    }

    /**
     * Generate a duplicate name.
     */
    private function generateDuplicateName(string $originalName): string
    {
        // Extract base name without any existing copy suffix
        $baseName = preg_replace('/\s*\(Copy(\s*\d+)?\)\s*$/', '', $originalName);

        // Count existing copies to determine the next number
        $copyPattern = $baseName . ' (Copy';
        $existingCount = Lecturer::where('name', 'like', $copyPattern . '%')->count();

        // If no existing copies, just use (Copy), otherwise use (Copy X)
        if ($existingCount == 0) {
            return $baseName . ' (Copy)';
        } else {
            return $baseName . ' (Copy ' . ($existingCount + 1) . ')';
        }
    }

    /**
     * Generate a unique ID card number for duplicated lecturer.
     */
    private function generateUniqueIdCardNumber(?string $originalNumber): ?string
    {
        if (!$originalNumber) {
            return null;
        }

        // Extract base ID card number without any existing copy suffix
        $baseNumber = preg_replace('/_COPY_\d+$/', '', $originalNumber);

        // Find the highest existing copy number for this base number
        $existingPattern = $baseNumber . '_COPY_';
        $highestCopy = Lecturer::where('id_card_number', 'like', $existingPattern . '%')
            ->get()
            ->map(function($lecturer) use ($baseNumber, $existingPattern) {
                $suffix = str_replace($existingPattern, '', $lecturer->id_card_number);
                return is_numeric($suffix) ? (int)$suffix : 0;
            })
            ->max() ?? 0;

        $nextCopy = $highestCopy + 1;
        return $baseNumber . '_COPY_' . $nextCopy;
    }

    /**
     * Generate a unique phone number for duplicated lecturer.
     */
    private function generateUniquePhone(?string $originalPhone): ?string
    {
        if (!$originalPhone) {
            return null;
        }

        // Extract base phone without any existing copy suffix
        $basePhone = preg_replace('/\s*\(Copy\)\s*$/', '', $originalPhone);

        $counter = 1;
        do {
            $newPhone = $basePhone . ' (Copy)';
            if ($counter > 1) {
                $newPhone = $basePhone . ' (Copy ' . $counter . ')';
            }
            $exists = Lecturer::where('phone', $newPhone)->exists();
            $counter++;
        } while ($exists);

        return $newPhone;
    }

    /**
     * Generate a unique passport number for duplicated lecturer.
     */
    private function generateUniquePassportNumber(?string $originalPassport): ?string
    {
        if (!$originalPassport) {
            return null;
        }

        // Extract base passport number without any existing copy suffix
        $basePassport = preg_replace('/_COPY_\d*$/', '', $originalPassport);

        $counter = 1;
        do {
            $newPassport = $basePassport . '_COPY_' . $counter;
            $exists = Lecturer::where('passport_number', $newPassport)->exists();
            $counter++;
        } while ($exists);

        return $newPassport;
    }
}