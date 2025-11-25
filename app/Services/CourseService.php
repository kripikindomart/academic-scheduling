<?php

namespace App\Services;

use App\Models\Course;
use App\Models\ProgramStudy;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseService extends BaseService
{
    /**
     * Get all courses with filtering and pagination
     *
     * @param int $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllCourses(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Course::with([
            'programStudy',
            'creator',
            'lecturers'
        ]);

        // Apply filters
        if (!empty($filters['program_study_id'])) {
            $query->byProgram($filters['program_study_id']);
        }

        if (!empty($filters['semester'])) {
            $query->bySemester($filters['semester']);
        }

        if (!empty($filters['academic_year'])) {
            $query->byAcademicYear($filters['academic_year']);
        }

        if (!empty($filters['course_type'])) {
            $query->byType($filters['course_type']);
        }

        if (!empty($filters['level'])) {
            $query->byLevel($filters['level']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('course_code')
                    ->paginate($perPage);
    }

    /**
     * Create a new course
     *
     * @param array $data
     * @return array
     */
    public function createCourse(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $course = Course::create([
                'course_code' => strtoupper($data['course_code']),
                'course_name' => $data['course_name'],
                'description' => $data['description'] ?? null,
                'credits' => $data['credits'] ?? 3,
                'semester' => $data['semester'],
                'academic_year' => $data['academic_year'],
                'course_type' => $data['course_type'] ?? 'mandatory',
                'level' => $data['level'] ?? 'undergraduate',
                'capacity' => $data['capacity'] ?? 50,
                'current_enrollment' => 0,
                'is_active' => $data['is_active'] ?? true,
                'program_study_id' => $data['program_study_id'],
                'created_by' => auth('sanctum')->id(),
                'updated_by' => auth('sanctum')->id(),
            ]);

            // Log activity
            Log::channel('activity')->info('Course created', [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'user_id' => auth('sanctum')->id(),
            ]);

            return $course->load(['programStudy', 'creator'])->toArray();
        });
    }

    /**
     * Update an existing course
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateCourse(int $id, array $data): array
    {
        return DB::transaction(function () use ($id, $data) {
            $course = Course::findOrFail($id);

            $course->update([
                'course_code' => isset($data['course_code']) ? strtoupper($data['course_code']) : $course->course_code,
                'course_name' => $data['course_name'] ?? $course->course_name,
                'description' => $data['description'] ?? $course->description,
                'credits' => $data['credits'] ?? $course->credits,
                'semester' => $data['semester'] ?? $course->semester,
                'academic_year' => $data['academic_year'] ?? $course->academic_year,
                'course_type' => $data['course_type'] ?? $course->course_type,
                'level' => $data['level'] ?? $course->level,
                'capacity' => $data['capacity'] ?? $course->capacity,
                'is_active' => $data['is_active'] ?? $course->is_active,
                'program_study_id' => $data['program_study_id'] ?? $course->program_study_id,
                'updated_by' => auth('sanctum')->id(),
            ]);

            // Log activity
            Log::channel('activity')->info('Course updated', [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'changes' => array_keys($data),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $course->fresh(['programStudy', 'creator'])->toArray();
        });
    }

    /**
     * Delete a course
     *
     * @param int $id
     * @return void
     */
    public function deleteCourse(int $id): void
    {
        DB::transaction(function () use ($id) {
            $course = Course::findOrFail($id);

            // Check if course has enrollments
            if ($course->enrollments()->count() > 0) {
                throw new \Exception('Cannot delete course with existing enrollments');
            }

            // Check if course has schedules
            if ($course->schedules()->count() > 0) {
                throw new \Exception('Cannot delete course with existing schedules');
            }

            $course->delete();

            // Log activity
            Log::channel('activity')->warning('Course deleted', [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'user_id' => auth('sanctum')->id(),
            ]);
        });
    }

    /**
     * Get available courses for enrollment
     *
     * @param array $filters
     * @return array
     */
    public function getAvailableCourses(array $filters = []): array
    {
        $query = Course::active()
            ->whereRaw('current_enrollment < capacity')
            ->with(['programStudy', 'lecturers']);

        // Apply filters
        if (!empty($filters['program_study_id'])) {
            $query->byProgram($filters['program_study_id']);
        }

        if (!empty($filters['semester'])) {
            $query->bySemester($filters['semester']);
        }

        if (!empty($filters['academic_year'])) {
            $query->byAcademicYear($filters['academic_year']);
        }

        if (!empty($filters['level'])) {
            $query->byLevel($filters['level']);
        }

        return $query->orderBy('course_code')->get()->toArray();
    }

    /**
     * Add prerequisite to course
     *
     * @param int $courseId
     * @param int $prerequisiteCourseId
     * @return void
     */
    public function addPrerequisite(int $courseId, int $prerequisiteCourseId): void
    {
        DB::transaction(function () use ($courseId, $prerequisiteCourseId) {
            $course = Course::findOrFail($courseId);
            $prerequisite = Course::findOrFail($prerequisiteCourseId);

            // Check for circular dependency
            if ($this->wouldCreateCircularDependency($courseId, $prerequisiteCourseId)) {
                throw new \Exception('Adding this prerequisite would create a circular dependency');
            }

            $course->prerequisites()->syncWithoutDetaching([$prerequisiteCourseId]);

            // Log activity
            Log::channel('activity')->info('Course prerequisite added', [
                'course_id' => $courseId,
                'course_code' => $course->course_code,
                'prerequisite_id' => $prerequisiteCourseId,
                'prerequisite_code' => $prerequisite->course_code,
                'user_id' => auth('sanctum')->id(),
            ]);
        });
    }

    /**
     * Remove prerequisite from course
     *
     * @param int $courseId
     * @param int $prerequisiteCourseId
     * @return void
     */
    public function removePrerequisite(int $courseId, int $prerequisiteCourseId): void
    {
        DB::transaction(function () use ($courseId, $prerequisiteCourseId) {
            $course = Course::findOrFail($courseId);
            $prerequisite = Course::findOrFail($prerequisiteCourseId);

            $course->prerequisites()->detach($prerequisiteCourseId);

            // Log activity
            Log::channel('activity')->info('Course prerequisite removed', [
                'course_id' => $courseId,
                'course_code' => $course->course_code,
                'prerequisite_id' => $prerequisiteCourseId,
                'prerequisite_code' => $prerequisite->course_code,
                'user_id' => auth('sanctum')->id(),
            ]);
        });
    }

    /**
     * Get course statistics
     *
     * @return array
     */
    public function getCourseStatistics(): array
    {
        $totalCourses = Course::count();
        $activeCourses = Course::active()->count();
        $totalCapacity = Course::sum('capacity');
        $totalEnrollment = Course::sum('current_enrollment');

        $coursesByType = Course::selectRaw('course_type, COUNT(*) as count')
            ->groupBy('course_type')
            ->pluck('count', 'course_type')
            ->toArray();

        $coursesByLevel = Course::selectRaw('level, COUNT(*) as count')
            ->groupBy('level')
            ->pluck('count', 'level')
            ->toArray();

        $coursesByProgram = Course::join('program_studies', 'courses.program_study_id', '=', 'program_studies.id')
            ->selectRaw('program_studies.name as program_name, COUNT(*) as count')
            ->groupBy('program_studies.id', 'program_studies.name')
            ->pluck('count', 'program_name')
            ->toArray();

        return [
            'total_courses' => $totalCourses,
            'active_courses' => $activeCourses,
            'inactive_courses' => $totalCourses - $activeCourses,
            'total_capacity' => $totalCapacity,
            'total_enrollment' => $totalEnrollment,
            'enrollment_rate' => $totalCapacity > 0 ? round(($totalEnrollment / $totalCapacity) * 100, 2) : 0,
            'courses_by_type' => $coursesByType,
            'courses_by_level' => $coursesByLevel,
            'courses_by_program' => $coursesByProgram,
        ];
    }

    /**
     * Check if adding prerequisite would create circular dependency
     *
     * @param int $courseId
     * @param int $prerequisiteCourseId
     * @return bool
     */
    private function wouldCreateCircularDependency(int $courseId, int $prerequisiteCourseId): bool
    {
        $visited = [];
        return $this->hasCircularDependency($prerequisiteCourseId, $courseId, $visited);
    }

    /**
     * Recursive check for circular dependency
     *
     * @param int $currentCourseId
     * @param int $targetCourseId
     * @param array $visited
     * @return bool
     */
    private function hasCircularDependency(int $currentCourseId, int $targetCourseId, array &$visited): bool
    {
        if (in_array($currentCourseId, $visited)) {
            return false;
        }

        if ($currentCourseId === $targetCourseId) {
            return true;
        }

        $visited[] = $currentCourseId;

        $prerequisites = Course::findOrFail($currentCourseId)
            ->prerequisites()
            ->pluck('prerequisite_course_id')
            ->toArray();

        foreach ($prerequisites as $prereqId) {
            if ($this->hasCircularDependency($prereqId, $targetCourseId, $visited)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Bulk update courses
     *
     * @param array $courseIds
     * @param array $updates
     * @return array
     */
    public function bulkUpdateCourses(array $courseIds, array $updates): array
    {
        return DB::transaction(function () use ($courseIds, $updates) {
            $courses = Course::whereIn('id', $courseIds)->get();
            $updatedCount = 0;
            $errors = [];

            foreach ($courses as $course) {
                try {
                    $course->update([
                        'course_code' => isset($updates['course_code']) ? strtoupper($updates['course_code']) : $course->course_code,
                        'course_name' => $updates['course_name'] ?? $course->course_name,
                        'description' => $updates['description'] ?? $course->description,
                        'credits' => $updates['credits'] ?? $course->credits,
                        'semester' => $updates['semester'] ?? $course->semester,
                        'academic_year' => $updates['academic_year'] ?? $course->academic_year,
                        'course_type' => $updates['course_type'] ?? $course->course_type,
                        'level' => $updates['level'] ?? $course->level,
                        'capacity' => $updates['capacity'] ?? $course->capacity,
                        'is_active' => $updates['is_active'] ?? $course->is_active,
                        'program_study_id' => $updates['program_study_id'] ?? $course->program_study_id,
                        'updated_by' => auth('sanctum')->id(),
                    ]);

                    $updatedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to update course {$course->course_code}: " . $e->getMessage();
                }
            }

            // Log activity
            Log::channel('activity')->info('Courses bulk updated', [
                'course_count' => $updatedCount,
                'total_requested' => count($courseIds),
                'user_id' => auth('sanctum')->id(),
            ]);

            return [
                'updated_count' => $updatedCount,
                'total_requested' => count($courseIds),
                'errors' => $errors
            ];
        });
    }

    /**
     * Bulk delete courses
     *
     * @param array $courseIds
     * @return array
     */
    public function bulkDeleteCourses(array $courseIds): array
    {
        return DB::transaction(function () use ($courseIds) {
            $courses = Course::whereIn('id', $courseIds)->get();
            $deletedCount = 0;
            $errors = [];

            foreach ($courses as $course) {
                try {
                    // Check if course has enrollments
                    if ($course->enrollments()->count() > 0) {
                        $errors[] = "Cannot delete course {$course->course_code}: Has existing enrollments";
                        continue;
                    }

                    // Check if course has schedules
                    if ($course->schedules()->count() > 0) {
                        $errors[] = "Cannot delete course {$course->course_code}: Has existing schedules";
                        continue;
                    }

                    $course->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to delete course {$course->course_code}: " . $e->getMessage();
                }
            }

            // Log activity
            Log::channel('activity')->warning('Courses bulk deleted', [
                'deleted_count' => $deletedCount,
                'total_requested' => count($courseIds),
                'user_id' => auth('sanctum')->id(),
            ]);

            return [
                'deleted_count' => $deletedCount,
                'total_requested' => count($courseIds),
                'errors' => $errors
            ];
        });
    }

    /**
     * Bulk toggle course status
     *
     * @param array $courseIds
     * @param bool $isActive
     * @return array
     */
    public function bulkToggleCourseStatus(array $courseIds, bool $isActive): array
    {
        return DB::transaction(function () use ($courseIds, $isActive) {
            $updated = Course::whereIn('id', $courseIds)
                ->update([
                    'is_active' => $isActive,
                    'updated_by' => auth('sanctum')->id(),
                ]);

            // Log activity
            Log::channel('activity')->info('Courses bulk status updated', [
                'updated_count' => $updated,
                'status' => $isActive ? 'activated' : 'deactivated',
                'total_requested' => count($courseIds),
                'user_id' => auth('sanctum')->id(),
            ]);

            return [
                'updated_count' => $updated,
                'total_requested' => count($courseIds),
                'status' => $isActive ? 'activated' : 'deactivated'
            ];
        });
    }

    /**
     * Import courses from file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    public function importCourses($file): array
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
                    $this->createCourse($row);
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$importedCount + 1}: " . $e->getMessage();
                }
            }

            // Log activity
            Log::channel('activity')->info('Courses imported from file', [
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
            Log::channel('activity')->error('Course import failed', [
                'error' => $e->getMessage(),
                'file_name' => $file->getClientOriginalName(),
                'user_id' => auth('sanctum')->id(),
            ]);

            throw $e;
        }
    }

    /**
     * Export courses to file
     *
     * @param array $filters
     * @return string
     */
    public function exportCourses(array $filters = []): string
    {
        $courses = $this->getAllCourses(10000, $filters)->items();
        $format = $filters['format'] ?? 'xlsx';
        $filename = 'courses_' . date('Y-m-d_H-i-s') . '.' . $format;

        // Log activity
        Log::channel('activity')->info('Courses exported', [
            'count' => count($courses),
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
                    'course_code' => $row[0] ?? '',
                    'course_name' => $row[1] ?? '',
                    'description' => $row[2] ?? '',
                    'credits' => (int) ($row[3] ?? 3),
                    'semester' => $row[4] ?? 'ganjil',
                    'academic_year' => $row[5] ?? '2023/2024',
                    'course_type' => $row[6] ?? 'mandatory',
                    'level' => $row[7] ?? 'undergraduate',
                    'capacity' => (int) ($row[8] ?? 50),
                    'program_study_id' => (int) ($row[9] ?? 1),
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
}