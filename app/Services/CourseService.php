<?php

namespace App\Services;

use App\Models\Course;
use App\Models\ProgramStudy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class CourseService
{
    /**
     * Get paginated list of courses with filtering.
     */
    public function getAllCourses(int $perPage = 20, array $filters = [])
    {
        $query = Course::with(['programStudy', 'creator']);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        
        if (!empty($filters['course_type'])) {
            $query->where('course_type', $filters['course_type']);
        }

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Create a new course.
     */
    public function createCourse(array $data): Course
    {
        return Course::create($data);
    }

    /**
     * Update a course.
     */
    public function updateCourse(int $id, array $data): Course
    {
        $course = Course::findOrFail($id);
        $course->update($data);
        return $course;
    }

    /**
     * Delete a course.
     */
    public function deleteCourse(int $id): void
    {
        $course = Course::findOrFail($id);
        $course->delete();
    }

    /**
     * Get available courses for enrollment.
     */
    public function getAvailableCourses(array $filters = []): array
    {
        $query = Course::where('is_active', true);

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        
        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        return $query->with(['programStudy'])->get()->toArray();
    }

    /**
     * Get course statistics.
     */
    public function getCourseStatistics(): array
    {
        $totalCourses = Course::count();
        $activeCourses = Course::where('is_active', true)->count();
        $inactiveCourses = Course::where('is_active', false)->count();

        return [
            'total_courses' => $totalCourses,
            'active_courses' => $activeCourses,
            'inactive_courses' => $inactiveCourses,
        ];
    }

    /**
     * Bulk update courses.
     */
    public function bulkUpdateCourses(array $courseIds, array $updates): int
    {
        return Course::whereIn('id', $courseIds)->update($updates);
    }

    /**
     * Bulk delete courses.
     */
    public function bulkDeleteCourses(array $courseIds): int
    {
        return Course::whereIn('id', $courseIds)->delete();
    }

    /**
     * Bulk toggle course status.
     */
    public function bulkToggleCourseStatus(array $courseIds, bool $isActive): int
    {
        return Course::whereIn('id', $courseIds)->update(['is_active' => $isActive]);
    }

    /**
     * Duplicate a course.
     */
    public function duplicateCourse(int $id, int $userId): Course
    {
        $originalCourse = Course::findOrFail($id);
        $newCourseData = $originalCourse->toArray();

        unset($newCourseData['id'], $newCourseData['created_at'], $newCourseData['updated_at']);

        // Generate unique course code
        $baseCode = $originalCourse->course_code;
        $newCourseData['course_code'] = $this->generateUniqueCourseCode($baseCode);

        // Generate unique course name
        $baseName = $originalCourse->course_name;
        $newCourseData['course_name'] = $this->generateUniqueCourseName($baseName);

        $newCourseData['created_by'] = $userId;
        // Reset enrollment for new course
        $newCourseData['current_enrollment'] = 0;

        return Course::create($newCourseData);
    }

    /**
     * Generate a unique course code for duplication.
     */
    private function generateUniqueCourseCode(string $baseCode): string
    {
        // Remove existing _COPY suffix if present
        $cleanCode = preg_replace('/_COPY(\d*)$/', '', $baseCode);

        $counter = 1;
        $newCode = $cleanCode . '_COPY';

        // Keep incrementing until we find a unique code
        while (Course::where('course_code', $newCode)->exists()) {
            $counter++;
            $newCode = $cleanCode . '_COPY' . $counter;
        }

        return $newCode;
    }

    /**
     * Generate a unique course name for duplication.
     */
    private function generateUniqueCourseName(string $baseName): string
    {
        // Remove existing (Copy) suffix if present
        $cleanName = preg_replace('/\s*\(Copy(\d*)\)$/', '', $baseName);

        $counter = 1;
        $newName = $cleanName . ' (Copy)';

        // Keep incrementing until we find a unique name
        while (Course::where('course_name', $newName)->exists()) {
            $counter++;
            $newName = $cleanName . ' (Copy ' . $counter . ')';
        }

        return $newName;
    }

    /**
     * Toggle active status for a course.
     */
    public function toggleCourseStatus(int $id, int $userId): Course
    {
        $course = Course::findOrFail($id);
        $course->update([
            'is_active' => !$course->is_active,
            'updated_by' => $userId
        ]);
        return $course;
    }

    /**
     * Import courses from file.
     */
    public function importCourses($file): array
    {
        // Placeholder implementation
        return [
            'imported' => 0,
            'errors' => []
        ];
    }

    /**
     * Export courses to file.
     */
    public function exportCourses(array $filters = []): string
    {
        // Placeholder implementation
        return 'courses.xlsx';
    }

    /**
     * Add prerequisite to course.
     */
    public function addPrerequisite(int $courseId, int $prerequisiteCourseId): void
    {
        $course = Course::findOrFail($courseId);
        $prerequisiteCourse = Course::findOrFail($prerequisiteCourseId);

        // Avoid duplicate prerequisites
        if (!$course->prerequisites()->where('prerequisite_course_id', $prerequisiteCourseId)->exists()) {
            $course->prerequisites()->attach($prerequisiteCourseId);
        }
    }

    /**
     * Remove prerequisite from course.
     */
    public function removePrerequisite(int $courseId, int $prerequisiteCourseId): void
    {
        $course = Course::findOrFail($courseId);
        $course->prerequisites()->detach($prerequisiteCourseId);
    }

    /**
     * Get paginated list of trashed courses with filtering.
     */
    public function getTrashedCourses(int $perPage = 20, array $filters = [])
    {
        $query = Course::onlyTrashed()->with(['programStudy', 'creator']);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['program_study_id'])) {
            $query->where('program_study_id', $filters['program_study_id']);
        }

        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        if (!empty($filters['course_type'])) {
            $query->where('course_type', $filters['course_type']);
        }

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        return $query->orderBy('deleted_at', 'desc')->paginate($perPage);
    }
}