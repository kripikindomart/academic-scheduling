<?php

namespace App\Services;

use App\Models\ProgramStudy;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramStudyService extends BaseService
{
    /**
     * Get all program studies with filtering and pagination
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllProgramStudies(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = ProgramStudy::with([
            'creator',
            'courses' => function ($query) {
                $query->select('id', 'program_study_id', 'course_code', 'course_name', 'is_active');
            },
            'lecturers' => function ($query) {
                $query->select('users.id', 'name', 'email')
                      ->withPivot('role');
            }
        ]);

        // Apply filters
        if (!empty($filters['faculty'])) {
            $query->byFaculty($filters['faculty']);
        }

        if (!empty($filters['level'])) {
            $query->byLevel($filters['level']);
        }

        if (!empty($filters['degree'])) {
            $query->byDegree($filters['degree']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('faculty', 'like', "%{$search}%")
                  ->orWhere('head_of_program', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('faculty')
                    ->orderBy('level')
                    ->orderBy('code')
                    ->paginate($perPage);
    }

    /**
     * Create a new program study
     *
     * @param array $data
     * @return array
     */
    public function createProgramStudy(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $program = ProgramStudy::create([
                'code' => strtoupper($data['code']),
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'faculty' => $data['faculty'],
                'level' => $data['level'] ?? 'undergraduate',
                'degree' => $data['degree'] ?? 'S1',
                'duration_years' => $data['duration_years'] ?? 4,
                'minimum_credits' => $data['minimum_credits'] ?? 144,
                'head_of_program' => $data['head_of_program'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'office_location' => $data['office_location'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => auth('sanctum')->id(),
                'updated_by' => auth('sanctum')->id(),
            ]);

            // Log activity
            Log::channel('activity')->info('Program study created', [
                'program_study_id' => $program->id,
                'code' => $program->code,
                'name' => $program->name,
                'faculty' => $program->faculty,
                'user_id' => auth('sanctum')->id(),
            ]);

            return $program->load(['creator'])->toArray();
        });
    }

    /**
     * Update an existing program study
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateProgramStudy(int $id, array $data): array
    {
        return DB::transaction(function () use ($id, $data) {
            $program = ProgramStudy::findOrFail($id);

            $program->update([
                'code' => isset($data['code']) ? strtoupper($data['code']) : $program->code,
                'name' => $data['name'] ?? $program->name,
                'description' => $data['description'] ?? $program->description,
                'faculty' => $data['faculty'] ?? $program->faculty,
                'level' => $data['level'] ?? $program->level,
                'degree' => $data['degree'] ?? $program->degree,
                'duration_years' => $data['duration_years'] ?? $program->duration_years,
                'minimum_credits' => $data['minimum_credits'] ?? $program->minimum_credits,
                'head_of_program' => $data['head_of_program'] ?? $program->head_of_program,
                'email' => $data['email'] ?? $program->email,
                'phone' => $data['phone'] ?? $program->phone,
                'office_location' => $data['office_location'] ?? $program->office_location,
                'is_active' => $data['is_active'] ?? $program->is_active,
                'updated_by' => auth('sanctum')->id(),
            ]);

            // Log activity
            Log::channel('activity')->info('Program study updated', [
                'program_study_id' => $program->id,
                'code' => $program->code,
                'changes' => array_keys($data),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $program->fresh(['creator'])->toArray();
        });
    }

    /**
     * Delete a program study
     *
     * @param int $id
     * @return void
     */
    public function deleteProgramStudy(int $id): void
    {
        DB::transaction(function () use ($id) {
            $program = ProgramStudy::findOrFail($id);

            // Check if program has active courses
            if ($program->courses()->active()->count() > 0) {
                throw new \Exception('Cannot delete program study with active courses');
            }

            // Check if program has enrolled students
            if ($program->students()->count() > 0) {
                throw new \Exception('Cannot delete program study with enrolled students');
            }

            // Check if program has active schedules
            if ($program->schedules()->count() > 0) {
                throw new \Exception('Cannot delete program study with existing schedules');
            }

            $program->delete();

            // Log activity
            Log::channel('activity')->warning('Program study deleted', [
                'program_study_id' => $program->id,
                'code' => $program->code,
                'name' => $program->name,
                'faculty' => $program->faculty,
                'user_id' => auth('sanctum')->id(),
            ]);
        });
    }

    /**
     * Get program study statistics
     *
     * @return array
     */
    public function getProgramStudyStatistics(): array
    {
        $totalPrograms = ProgramStudy::count();
        $activePrograms = ProgramStudy::active()->count();
        $trashedPrograms = ProgramStudy::onlyTrashed()->count();

        $programsByLevel = ProgramStudy::selectRaw('level, COUNT(*) as count')
            ->groupBy('level')
            ->pluck('count', 'level')
            ->toArray();

        $programsByFaculty = ProgramStudy::selectRaw('faculty, COUNT(*) as count')
            ->groupBy('faculty')
            ->pluck('count', 'faculty')
            ->toArray();

        $programsByDegree = ProgramStudy::selectRaw('degree, COUNT(*) as count')
            ->groupBy('degree')
            ->pluck('count', 'degree')
            ->toArray();

        // Get program with most courses
        $programWithMostCourses = ProgramStudy::withCount('courses')
            ->orderBy('courses_count', 'desc')
            ->first();

        // Get program with most students
        $programWithMostStudents = ProgramStudy::withCount('students')
            ->orderBy('students_count', 'desc')
            ->first();

        return [
            'total_programs' => $totalPrograms,
            'active_programs' => $activePrograms,
            'inactive_programs' => $totalPrograms - $activePrograms,
            'trashed_programs' => $trashedPrograms,
            'programs_by_level' => $programsByLevel,
            'programs_by_faculty' => $programsByFaculty,
            'programs_by_degree' => $programsByDegree,
            'program_with_most_courses' => $programWithMostCourses ? [
                'name' => $programWithMostCourses->name,
                'code' => $programWithMostCourses->code,
                'course_count' => $programWithMostCourses->courses_count
            ] : null,
            'program_with_most_students' => $programWithMostStudents ? [
                'name' => $programWithMostStudents->name,
                'code' => $programWithMostStudents->code,
                'student_count' => $programWithMostStudents->students_count
            ] : null,
        ];
    }

    /**
     * Get list of faculties
     *
     * @return array
     */
    public function getFaculties(): array
    {
        return ProgramStudy::selectRaw('faculty as name, COUNT(*) as count')
            ->groupBy('faculty')
            ->orderBy('faculty')
            ->get()
            ->toArray();
    }

    /**
     * Assign lecturer to program study
     *
     * @param int $programId
     * @param int $userId
     * @param string $role
     * @return void
     */
    public function assignLecturer(int $programId, int $userId, string $role): void
    {
        DB::transaction(function () use ($programId, $userId, $role) {
            $program = ProgramStudy::findOrFail($programId);
            $user = User::findOrFail($userId);

            // Check if user has lecturer role
            if (!$user->hasRole('lecturer')) {
                throw new \Exception('User must have lecturer role to be assigned to program study');
            }

            // Check if already assigned
            if ($program->lecturers()->where('users.id', $userId)->exists()) {
                throw new \Exception('Lecturer is already assigned to this program study');
            }

            $program->lecturers()->attach($userId, [
                'role' => $role,
                'assigned_at' => now(),
                'assigned_by' => auth('sanctum')->id(),
            ]);

            // Log activity
            Log::channel('activity')->info('Lecturer assigned to program study', [
                'program_study_id' => $programId,
                'program_code' => $program->code,
                'user_id' => $userId,
                'user_name' => $user->name,
                'role' => $role,
                'assigned_by' => auth('sanctum')->id(),
            ]);
        });
    }

    /**
     * Remove lecturer from program study
     *
     * @param int $programId
     * @param int $userId
     * @return void
     */
    public function removeLecturer(int $programId, int $userId): void
    {
        DB::transaction(function () use ($programId, $userId) {
            $program = ProgramStudy::findOrFail($programId);
            $user = User::findOrFail($userId);

            $program->lecturers()->detach($userId);

            // Log activity
            Log::channel('activity')->info('Lecturer removed from program study', [
                'program_study_id' => $programId,
                'program_code' => $program->code,
                'user_id' => $userId,
                'user_name' => $user->name,
                'removed_by' => auth('sanctum')->id(),
            ]);
        });
    }

    /**
     * Bulk update program studies
     *
     * @param array $programStudyIds
     * @param array $updates
     * @return array
     */
    public function bulkUpdateProgramStudies(array $programStudyIds, array $updates): array
    {
        return DB::transaction(function () use ($programStudyIds, $updates) {
            $programs = ProgramStudy::whereIn('id', $programStudyIds)->get();
            $updatedCount = 0;
            $errors = [];

            foreach ($programs as $program) {
                try {
                    $program->update([
                        'code' => isset($updates['code']) ? strtoupper($updates['code']) : $program->code,
                        'name' => $updates['name'] ?? $program->name,
                        'description' => $updates['description'] ?? $program->description,
                        'faculty' => $updates['faculty'] ?? $program->faculty,
                        'level' => $updates['level'] ?? $program->level,
                        'degree' => $updates['degree'] ?? $program->degree,
                        'duration_years' => $updates['duration_years'] ?? $program->duration_years,
                        'minimum_credits' => $updates['minimum_credits'] ?? $program->minimum_credits,
                        'head_of_program' => $updates['head_of_program'] ?? $program->head_of_program,
                        'email' => $updates['email'] ?? $program->email,
                        'phone' => $updates['phone'] ?? $program->phone,
                        'office_location' => $updates['office_location'] ?? $program->office_location,
                        'is_active' => $updates['is_active'] ?? $program->is_active,
                        'updated_by' => auth('sanctum')->id(),
                    ]);

                    $updatedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to update program study {$program->code}: " . $e->getMessage();
                }
            }

            // Log activity
            Log::channel('activity')->info('Program studies bulk updated', [
                'program_count' => $updatedCount,
                'total_requested' => count($programStudyIds),
                'user_id' => auth('sanctum')->id(),
            ]);

            return [
                'updated_count' => $updatedCount,
                'total_requested' => count($programStudyIds),
                'errors' => $errors
            ];
        });
    }

    /**
     * Bulk delete program studies
     *
     * @param array $programStudyIds
     * @return array
     */
    public function bulkDeleteProgramStudies(array $programStudyIds): array
    {
        return DB::transaction(function () use ($programStudyIds) {
            $programs = ProgramStudy::whereIn('id', $programStudyIds)->get();
            $deletedCount = 0;
            $errors = [];

            foreach ($programs as $program) {
                try {
                    // Check if program has active courses
                    if ($program->courses()->active()->count() > 0) {
                        $errors[] = "Cannot delete program study {$program->code}: Has active courses";
                        continue;
                    }

                    // Check if program has enrolled students
                    if ($program->students()->count() > 0) {
                        $errors[] = "Cannot delete program study {$program->code}: Has enrolled students";
                        continue;
                    }

                    // Check if program has active schedules
                    if ($program->schedules()->count() > 0) {
                        $errors[] = "Cannot delete program study {$program->code}: Has existing schedules";
                        continue;
                    }

                    $program->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to delete program study {$program->code}: " . $e->getMessage();
                }
            }

            // Log activity
            Log::channel('activity')->warning('Program studies bulk deleted', [
                'deleted_count' => $deletedCount,
                'total_requested' => count($programStudyIds),
                'user_id' => auth('sanctum')->id(),
            ]);

            return [
                'deleted_count' => $deletedCount,
                'total_requested' => count($programStudyIds),
                'errors' => $errors
            ];
        });
    }

    /**
     * Bulk toggle program study status
     *
     * @param array $programStudyIds
     * @param bool $isActive
     * @return array
     */
    public function bulkToggleProgramStudyStatus(array $programStudyIds, bool $isActive): array
    {
        return DB::transaction(function () use ($programStudyIds, $isActive) {
            $updated = ProgramStudy::whereIn('id', $programStudyIds)
                ->update([
                    'is_active' => $isActive,
                    'updated_by' => auth('sanctum')->id(),
                ]);

            // Log activity
            Log::channel('activity')->info('Program studies bulk status updated', [
                'updated_count' => $updated,
                'status' => $isActive ? 'activated' : 'deactivated',
                'total_requested' => count($programStudyIds),
                'user_id' => auth('sanctum')->id(),
            ]);

            return [
                'updated_count' => $updated,
                'total_requested' => count($programStudyIds),
                'status' => $isActive ? 'activated' : 'deactivated'
            ];
        });
    }

    /**
     * Import program studies from file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    public function importProgramStudies($file): array
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $data = [];

            if ($extension === 'csv') {
                $data = $this->importFromCsv($file);
            } else {
                $data = $this->importFromExcel($file);
            }

            $importedCount = 0;
            $errors = [];

            foreach ($data as $row) {
                try {
                    $this->createProgramStudy($row);
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($importedCount + 1) . ": " . $e->getMessage();
                }
            }

            // Log activity
            Log::channel('activity')->info('Program studies imported from file', [
                'imported_count' => $importedCount,
                'total_rows' => count($data),
                'file_name' => $file->getClientOriginalName(),
                'user_id' => auth('sanctum')->id(),
            ]);

            return [
                'imported_count' => $importedCount,
                'total_rows' => count($data),
                'errors' => $errors
            ];
        } catch (\Exception $e) {
            Log::channel('activity')->error('Program study import failed', [
                'error' => $e->getMessage(),
                'file_name' => $file->getClientOriginalName(),
                'user_id' => auth('sanctum')->id(),
            ]);

            throw $e;
        }
    }

    /**
     * Export program studies to file
     *
     * @param array $filters
     * @return string
     */
    public function exportProgramStudies(array $filters = []): string
    {
        $programs = $this->getAllProgramStudies(10000, $filters)->items();
        $format = $filters['format'] ?? 'xlsx';
        $filename = 'program_studies_' . date('Y-m-d_H-i-s') . '.' . $format;

        // Log activity
        Log::channel('activity')->info('Program studies exported', [
            'count' => count($programs),
            'format' => $format,
            'filters' => $filters,
            'user_id' => auth('sanctum')->id(),
        ]);

        // This would typically use a library like Laravel Excel
        // For now, return a placeholder path
        return storage_path('app/exports/' . $filename);
    }

    /**
     * Import data from CSV file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    private function importFromCsv($file): array
    {
        $data = [];
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle !== false) {
            // Skip header row
            fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                $data[] = [
                    'code' => $row[0] ?? '',
                    'name' => $row[1] ?? '',
                    'description' => $row[2] ?? '',
                    'faculty' => $row[3] ?? '',
                    'level' => $row[4] ?? 'undergraduate',
                    'degree' => $row[5] ?? 'S1',
                    'duration_years' => (int) ($row[6] ?? 4),
                    'minimum_credits' => (int) ($row[7] ?? 144),
                    'head_of_program' => $row[8] ?? '',
                    'email' => $row[9] ?? '',
                    'phone' => $row[10] ?? '',
                    'office_location' => $row[11] ?? '',
                ];
            }

            fclose($handle);
        }

        return $data;
    }

    /**
     * Import data from Excel file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    private function importFromExcel($file): array
    {
        // This would typically use a library like Laravel Excel
        // For now, return empty array
        return [];
    }

    /**
     * Get trashed program studies
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getTrashedProgramStudies(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = ProgramStudy::onlyTrashed()->with(['creator']);

        // Apply filters to trashed items
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('faculty', 'like', "%{$search}%")
                  ->orWhere('head_of_program', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['faculty'])) {
            $query->where('faculty', $filters['faculty']);
        }

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        return $query->orderBy('deleted_at', 'desc')
                    ->paginate($perPage);
    }

    /**
     * Restore trashed program study
     *
     * @param int $id
     * @return array
     */
    public function restoreProgramStudy(int $id): array
    {
        return DB::transaction(function () use ($id) {
            $program = ProgramStudy::onlyTrashed()->findOrFail($id);

            $program->restore();

            $program->update([
                'updated_by' => auth('sanctum')->id(),
            ]);

            // Log activity
            Log::channel('activity')->info('Program study restored', [
                'program_study_id' => $program->id,
                'code' => $program->code,
                'name' => $program->name,
                'restored_by' => auth('sanctum')->id(),
            ]);

            return $program->load(['creator', 'lecturers'])->toArray();
        });
    }

    /**
     * Toggle program study status
     *
     * @param int $id
     * @param bool $isActive
     * @return array
     */
    public function toggleProgramStudyStatus(int $id, bool $isActive): array
    {
        return DB::transaction(function () use ($id, $isActive) {
            $program = ProgramStudy::findOrFail($id);

            $oldStatus = $program->is_active;
            $program->update([
                'is_active' => $isActive,
                'updated_by' => auth('sanctum')->id(),
            ]);

            // Log activity
            Log::channel('activity')->info('Program study status toggled', [
                'program_study_id' => $program->id,
                'code' => $program->code,
                'name' => $program->name,
                'old_status' => $oldStatus,
                'new_status' => $isActive,
                'updated_by' => auth('sanctum')->id(),
            ]);

            return $program->load(['creator', 'lecturers'])->toArray();
        });
    }

    /**
     * Duplicate program study
     *
     * @param int $id
     * @return array
     */
    public function duplicateProgramStudy(int $id): array
    {
        return DB::transaction(function () use ($id) {
            $originalProgram = ProgramStudy::findOrFail($id);

            $newProgram = $originalProgram->replicate();

            // Generate unique code
            $baseCode = $originalProgram->code;
            $suffix = 1;
            do {
                $newCode = $baseCode . '_COPY_' . $suffix;
                $exists = ProgramStudy::where('code', $newCode)->exists();
                $suffix++;
            } while ($exists);

            $newProgram->code = $newCode;
            $newProgram->name = $originalProgram->name . ' (Copy ' . ($suffix - 1) . ')';
            $newProgram->is_active = false; // Start as inactive
            $newProgram->created_by = auth('sanctum')->id();
            $newProgram->updated_by = auth('sanctum')->id();
            $newProgram->save();

            // Log activity
            Log::channel('activity')->info('Program study duplicated', [
                'original_program_id' => $originalProgram->id,
                'original_code' => $originalProgram->code,
                'new_program_id' => $newProgram->id,
                'new_code' => $newProgram->code,
                'duplicated_by' => auth('sanctum')->id(),
            ]);

            return $newProgram->load(['creator', 'lecturers'])->toArray();
        });
    }

    /**
     * Force delete program study permanently.
     *
     * @param int $id
     * @return bool
     */
    public function forceDeleteProgramStudy(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $program = ProgramStudy::onlyTrashed()->findOrFail($id);

            // Log activity before deletion
            Log::channel('activity')->warning('Program study permanently deleted', [
                'program_study_id' => $program->id,
                'code' => $program->code,
                'name' => $program->name,
                'deleted_by' => auth('sanctum')->id(),
            ]);

            return $program->forceDelete();
        });
    }
}