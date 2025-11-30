<?php

namespace App\Services;

use App\Models\AcademicYear;
use Illuminate\Support\Facades\Cache;

class AcademicYearService
{
    const CACHE_KEY = 'active_academic_year';
    const CACHE_TTL = 24 * 60 * 60; // 24 hours

    /**
     * Get the active academic year ID.
     *
     * @return int|null
     */
    public static function getActiveAcademicYearId()
    {
        // Try cache first
        $cachedId = Cache::get(self::CACHE_KEY);
        if ($cachedId) {
            return $cachedId;
        }

        // Get from database
        $activeYear = AcademicYear::getActive();
        if ($activeYear) {
            Cache::put(self::CACHE_KEY, $activeYear->id, self::CACHE_TTL);
            return $activeYear->id;
        }

        return null;
    }

    /**
     * Set the active academic year ID.
     *
     * @param int $academicYearId
     * @return void
     */
    public static function setActiveAcademicYearId($academicYearId)
    {
        Cache::put(self::CACHE_KEY, $academicYearId, self::CACHE_TTL);
    }

    /**
     * Get the active academic year with full details.
     *
     * @return array|null
     */
    public static function getActiveAcademicYear()
    {
        $activeYear = AcademicYear::getActive();

        if (!$activeYear) {
            return null;
        }

        return [
            'id' => $activeYear->id,
            'name' => $activeYear->name,
            'code' => $activeYear->code,
            'academic_calendar_year' => $activeYear->academic_calendar_year,
            'description' => $activeYear->description,
            'start_date' => $activeYear->start_date->format('Y-m-d'),
            'end_date' => $activeYear->end_date->format('Y-m-d'),
            'is_active' => $activeYear->is_active,
            'status' => $activeYear->status,
            'admission_period' => $activeYear->admission_period,
            'admission_start_date' => $activeYear->admission_start_date?->format('Y-m-d'),
            'admission_end_date' => $activeYear->admission_end_date?->format('Y-m-d'),
            'registration_start_date' => $activeYear->registration_start_date?->format('Y-m-d'),
            'registration_end_date' => $activeYear->registration_end_date?->format('Y-m-d'),
            'course_registration_start_date' => $activeYear->course_registration_start_date?->format('Y-m-d'),
            'course_registration_end_date' => $activeYear->course_registration_end_date?->format('Y-m-d'),
            'class_start_date' => $activeYear->class_start_date?->format('Y-m-d'),
            'class_end_date' => $activeYear->class_end_date?->format('Y-m-d'),
            'mid_exam_start_date' => $activeYear->mid_exam_start_date?->format('Y-m-d'),
            'mid_exam_end_date' => $activeYear->mid_exam_end_date?->format('Y-m-d'),
            'final_exam_start_date' => $activeYear->final_exam_start_date?->format('Y-m-d'),
            'final_exam_end_date' => $activeYear->final_exam_end_date?->format('Y-m-d'),
            'thesis_deadline' => $activeYear->thesis_deadline?->format('Y-m-d'),
            'yudisium_date' => $activeYear->yudisium_date?->format('Y-m-d'),
            'max_credit_per_semester' => $activeYear->max_credit_per_semester,
            'is_visible_to_students' => $activeYear->is_visible_to_students,
            'allow_course_registration' => $activeYear->allow_course_registration,
            'allow_schedule_changes' => $activeYear->allow_schedule_changes,
            'tuition_fee' => $activeYear->tuition_fee,
            'registration_fee' => $activeYear->registration_fee,
        ];
    }

    /**
     * Check if a specific academic year is active.
     *
     * @param int $academicYearId
     * @return bool
     */
    public static function isAcademicYearActive($academicYearId)
    {
        return self::getActiveAcademicYearId() == $academicYearId;
    }

    /**
     * Clear the active academic year cache.
     *
     * @return void
     */
    public static function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get all academic years from database.
     *
     * @param array $filters
     * @return array
     */
    public static function getAllAcademicYears($filters = [])
    {
        $query = AcademicYear::query();

        // Apply filters
        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['visible'])) {
            $query->visible();
        }

        $academicYears = $query->orderBy('start_date', 'desc')->get();

        return $academicYears->map(function ($year) {
            return [
                'id' => $year->id,
                'name' => $year->name,
                'code' => $year->code,
                'academic_calendar_year' => $year->academic_calendar_year,
                'description' => $year->description,
                'start_date' => $year->start_date->format('Y-m-d'),
                'end_date' => $year->end_date->format('Y-m-d'),
                'is_active' => $year->is_active,
                'status' => $year->status,
                'admission_period' => $year->admission_period,
                'admission_start_date' => $year->admission_start_date?->format('Y-m-d'),
                'admission_end_date' => $year->admission_end_date?->format('Y-m-d'),
                'registration_start_date' => $year->registration_start_date?->format('Y-m-d'),
                'registration_end_date' => $year->registration_end_date?->format('Y-m-d'),
                'course_registration_start_date' => $year->course_registration_start_date?->format('Y-m-d'),
                'course_registration_end_date' => $year->course_registration_end_date?->format('Y-m-d'),
                'class_start_date' => $year->class_start_date?->format('Y-m-d'),
                'class_end_date' => $year->class_end_date?->format('Y-m-d'),
                'mid_exam_start_date' => $year->mid_exam_start_date?->format('Y-m-d'),
                'mid_exam_end_date' => $year->mid_exam_end_date?->format('Y-m-d'),
                'final_exam_start_date' => $year->final_exam_start_date?->format('Y-m-d'),
                'final_exam_end_date' => $year->final_exam_end_date?->format('Y-m-d'),
                'thesis_deadline' => $year->thesis_deadline?->format('Y-m-d'),
                'yudisium_date' => $year->yudisium_date?->format('Y-m-d'),
                'max_credit_per_semester' => $year->max_credit_per_semester,
                'tuition_fee' => $year->tuition_fee,
                'registration_fee' => $year->registration_fee,
                'is_visible_to_students' => $year->is_visible_to_students,
                'allow_course_registration' => $year->allow_course_registration,
                'allow_schedule_changes' => $year->allow_schedule_changes,
                'created_at' => $year->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $year->updated_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    /**
     * Create a new academic year.
     *
     * @param array $data
     * @return array
     */
    public static function createAcademicYear($data)
    {
        $academicYear = AcademicYear::create($data);

        return [
            'id' => $academicYear->id,
            'name' => $academicYear->name,
            'code' => $academicYear->code,
            'academic_calendar_year' => $academicYear->academic_calendar_year,
            'description' => $academicYear->description,
            'start_date' => $academicYear->start_date->format('Y-m-d'),
            'end_date' => $academicYear->end_date->format('Y-m-d'),
            'is_active' => $academicYear->is_active,
            'status' => $academicYear->status,
            'admission_period' => $academicYear->admission_period,
            'admission_start_date' => $academicYear->admission_start_date?->format('Y-m-d'),
            'admission_end_date' => $academicYear->admission_end_date?->format('Y-m-d'),
            'registration_start_date' => $academicYear->registration_start_date?->format('Y-m-d'),
            'registration_end_date' => $academicYear->registration_end_date?->format('Y-m-d'),
            'course_registration_start_date' => $academicYear->course_registration_start_date?->format('Y-m-d'),
            'course_registration_end_date' => $academicYear->course_registration_end_date?->format('Y-m-d'),
            'class_start_date' => $academicYear->class_start_date?->format('Y-m-d'),
            'class_end_date' => $academicYear->class_end_date?->format('Y-m-d'),
            'mid_exam_start_date' => $academicYear->mid_exam_start_date?->format('Y-m-d'),
            'mid_exam_end_date' => $academicYear->mid_exam_end_date?->format('Y-m-d'),
            'final_exam_start_date' => $academicYear->final_exam_start_date?->format('Y-m-d'),
            'final_exam_end_date' => $academicYear->final_exam_end_date?->format('Y-m-d'),
            'thesis_deadline' => $academicYear->thesis_deadline?->format('Y-m-d'),
            'yudisium_date' => $academicYear->yudisium_date?->format('Y-m-d'),
            'max_credit_per_semester' => $academicYear->max_credit_per_semester,
            'is_visible_to_students' => $academicYear->is_visible_to_students,
            'allow_course_registration' => $academicYear->allow_course_registration,
            'allow_schedule_changes' => $academicYear->allow_schedule_changes,
            'tuition_fee' => $academicYear->tuition_fee,
            'registration_fee' => $academicYear->registration_fee,
            'created_at' => $academicYear->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $academicYear->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Update an academic year.
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public static function updateAcademicYear($id, $data)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->update($data);

        return [
            'id' => $academicYear->id,
            'name' => $academicYear->name,
            'code' => $academicYear->code,
            'description' => $academicYear->description,
            'start_date' => $academicYear->start_date->format('Y-m-d'),
            'end_date' => $academicYear->end_date->format('Y-m-d'),
            'is_active' => $academicYear->is_active,
            'status' => $academicYear->status,
            'current_semester' => $academicYear->current_semester,
            'registration_start_date' => $academicYear->registration_start_date?->format('Y-m-d'),
            'registration_end_date' => $academicYear->registration_end_date?->format('Y-m-d'),
            'course_registration_start_date' => $academicYear->course_registration_start_date?->format('Y-m-d'),
            'course_registration_end_date' => $academicYear->course_registration_end_date?->format('Y-m-d'),
            'is_visible_to_students' => $academicYear->is_visible_to_students,
            'allow_course_registration' => $academicYear->allow_course_registration,
            'allow_schedule_changes' => $academicYear->allow_schedule_changes,
            'tuition_fee' => $academicYear->tuition_fee,
            'registration_fee' => $academicYear->registration_fee,
            'updated_at' => $academicYear->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Delete an academic year.
     *
     * @param int $id
     * @return bool
     */
    public static function deleteAcademicYear($id)
    {
        $academicYear = AcademicYear::findOrFail($id);

        // Cannot delete active academic year
        if ($academicYear->is_active) {
            throw new \Exception('Cannot delete active academic year');
        }

        return $academicYear->delete();
    }

    /**
     * Set an academic year as active.
     *
     * @param int $id
     * @return void
     */
    public static function setActiveAcademicYear($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->setActive();
        self::clearCache();
    }

    /**
     * Get academic year statistics.
     *
     * @return array
     */
    public static function getStatistics()
    {
        $total = AcademicYear::count();
        $active = AcademicYear::active()->count();
        $upcoming = AcademicYear::byStatus('upcoming')->count();
        $completed = AcademicYear::byStatus('completed')->count();
        $visible = AcademicYear::visible()->count();

        return [
            'total_academic_years' => $total,
            'active_academic_years' => $active,
            'upcoming_academic_years' => $upcoming,
            'completed_academic_years' => $completed,
            'visible_academic_years' => $visible,
            'current_active_year' => self::getActiveAcademicYear(),
        ];
    }

    /**
     * Set active academic year for the current session/request.
     * This can be used to temporarily override the active academic year.
     *
     * @param int $academicYearId
     * @return void
     */
    public static function setActiveAcademicYearForSession($academicYearId)
    {
        session(['active_academic_year' => $academicYearId]);
    }

    /**
     * Get the currently active academic year considering session override.
     *
     * @return int
     */
    public static function getCurrentActiveAcademicYearId()
    {
        // Check session first (temporary override)
        if (session()->has('active_academic_year')) {
            return session('active_academic_year');
        }

        // Fall back to cached/saved active academic year
        return self::getActiveAcademicYearId() ?: (int)date('Y');
    }

    /**
     * Duplicate an academic year.
     *
     * @param int $id
     * @param int $createdBy
     * @return array
     */
    public static function duplicateAcademicYear($id, $createdBy)
    {
        $originalYear = AcademicYear::findOrFail($id);

        $newData = $originalYear->toArray();

        // Remove fields that should not be duplicated
        unset($newData['id'], $newData['created_at'], $newData['updated_at'], $newData['deleted_at']);
        unset($newData['activated_by'], $newData['activated_at']);

        // Modify fields for the duplicate
        $newData['name'] = $originalYear->name . ' (Copy)';
        $newData['code'] = $originalYear->code . '_copy';
        $newData['is_active'] = false;
        $newData['status'] = 'upcoming';
        $newData['created_by'] = $createdBy;
        $newData['updated_by'] = $createdBy;

        $duplicatedYear = AcademicYear::create($newData);

        return [
            'id' => $duplicatedYear->id,
            'name' => $duplicatedYear->name,
            'code' => $duplicatedYear->code,
            'description' => $duplicatedYear->description,
            'start_date' => $duplicatedYear->start_date->format('Y-m-d'),
            'end_date' => $duplicatedYear->end_date->format('Y-m-d'),
            'is_active' => $duplicatedYear->is_active,
            'status' => $duplicatedYear->status,
            'current_semester' => $duplicatedYear->current_semester,
            'registration_start_date' => $duplicatedYear->registration_start_date?->format('Y-m-d'),
            'registration_end_date' => $duplicatedYear->registration_end_date?->format('Y-m-d'),
            'course_registration_start_date' => $duplicatedYear->course_registration_start_date?->format('Y-m-d'),
            'course_registration_end_date' => $duplicatedYear->course_registration_end_date?->format('Y-m-d'),
            'is_visible_to_students' => $duplicatedYear->is_visible_to_students,
            'allow_course_registration' => $duplicatedYear->allow_course_registration,
            'allow_schedule_changes' => $duplicatedYear->allow_schedule_changes,
            'tuition_fee' => $duplicatedYear->tuition_fee,
            'registration_fee' => $duplicatedYear->registration_fee,
            'created_at' => $duplicatedYear->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $duplicatedYear->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Toggle active status for an academic year.
     *
     * @param int $id
     * @param int $updatedBy
     * @return array
     */
    public static function toggleActiveStatus($id, $updatedBy)
    {
        $academicYear = AcademicYear::findOrFail($id);

        if ($academicYear->is_active) {
            // Deactivating - set to completed status
            $academicYear->update([
                'is_active' => false,
                'status' => 'completed',
                'updated_by' => $updatedBy
            ]);
        } else {
            // Activating - set as the only active year
            $academicYear->setActive();
        }

        self::clearCache();

        // Reload fresh data
        $academicYear->refresh();

        return [
            'id' => $academicYear->id,
            'name' => $academicYear->name,
            'code' => $academicYear->code,
            'description' => $academicYear->description,
            'start_date' => $academicYear->start_date->format('Y-m-d'),
            'end_date' => $academicYear->end_date->format('Y-m-d'),
            'is_active' => $academicYear->is_active,
            'status' => $academicYear->status,
            'current_semester' => $academicYear->current_semester,
            'registration_start_date' => $academicYear->registration_start_date?->format('Y-m-d'),
            'registration_end_date' => $academicYear->registration_end_date?->format('Y-m-d'),
            'course_registration_start_date' => $academicYear->course_registration_start_date?->format('Y-m-d'),
            'course_registration_end_date' => $academicYear->course_registration_end_date?->format('Y-m-d'),
            'is_visible_to_students' => $academicYear->is_visible_to_students,
            'allow_course_registration' => $academicYear->allow_course_registration,
            'allow_schedule_changes' => $academicYear->allow_schedule_changes,
            'tuition_fee' => $academicYear->tuition_fee,
            'registration_fee' => $academicYear->registration_fee,
            'updated_at' => $academicYear->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}