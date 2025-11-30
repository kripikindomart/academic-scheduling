<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AcademicYearService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    /**
     * Get all academic years.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [];

            if ($request->has('status')) {
                $filters['status'] = $request->get('status');
            }

            if ($request->has('visible')) {
                $filters['visible'] = $request->boolean('visible');
            }

            $academicYears = AcademicYearService::getAllAcademicYears($filters);

            return ResponseService::success(
                $academicYears,
                'Academic years retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve academic years: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Store a new academic year.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Only admin can create academic years
            if (!$user || !$user->isAdmin()) {
                return ResponseService::error(
                    'Unauthorized. Only admin can create academic years.',
                    null,
                    403
                );
            }

            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:academic_years,name',
                'code' => 'required|string|max:20|unique:academic_years,code',
                'academic_calendar_year' => 'required|string|max:9',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => ['required', Rule::in(['upcoming', 'active', 'completed'])],
                'admission_period' => ['required', Rule::in(['ganjil', 'genap'])],
                'admission_start_date' => 'nullable|date',
                'admission_end_date' => 'nullable|date|after_or_equal:admission_start_date',
                'registration_start_date' => 'nullable|date',
                'registration_end_date' => 'nullable|date|after_or_equal:registration_start_date',
                'course_registration_start_date' => 'nullable|date',
                'course_registration_end_date' => 'nullable|date|after_or_equal:course_registration_start_date',
                'class_start_date' => 'nullable|date',
                'class_end_date' => 'nullable|date|after_or_equal:class_start_date',
                'mid_exam_start_date' => 'nullable|date',
                'mid_exam_end_date' => 'nullable|date|after_or_equal:mid_exam_start_date',
                'final_exam_start_date' => 'nullable|date',
                'final_exam_end_date' => 'nullable|date|after_or_equal:final_exam_start_date',
                'thesis_deadline' => 'nullable|date',
                'yudisium_date' => 'nullable|date',
                'max_credit_per_semester' => 'required|integer|min:1|max:50',
                'tuition_fee' => 'nullable|decimal:0,2|min:0',
                'registration_fee' => 'nullable|decimal:0,2|min:0',
                'is_visible_to_students' => 'boolean',
                'allow_course_registration' => 'boolean',
                'allow_schedule_changes' => 'boolean',
                'settings' => 'nullable|array',
            ]);

            // Set default values for checkboxes
            $validated['is_active'] = $validated['status'] === 'active';
            $validated['is_visible_to_students'] = $validated['is_visible_to_students'] ?? true;
            $validated['allow_course_registration'] = $validated['allow_course_registration'] ?? false;
            $validated['allow_schedule_changes'] = $validated['allow_schedule_changes'] ?? true;
            $validated['created_by'] = $user->id;
            $validated['updated_by'] = $user->id;

            $academicYear = AcademicYearService::createAcademicYear($validated);

            return ResponseService::success(
                $academicYear,
                'Academic year created successfully',
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::error(
                'Validation failed: ' . $e->getMessage(),
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to create academic year: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get a specific academic year.
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $academicYears = AcademicYearService::getAllAcademicYears();
            $academicYear = collect($academicYears)->firstWhere('id', $id);

            if (!$academicYear) {
                return ResponseService::error(
                    'Academic year not found',
                    null,
                    404
                );
            }

            return ResponseService::success(
                $academicYear,
                'Academic year retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve academic year: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Update an academic year.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();

            // Only admin can update academic years
            if (!$user || !$user->isAdmin()) {
                return ResponseService::error(
                    'Unauthorized. Only admin can update academic years.',
                    null,
                    403
                );
            }

            $validated = $request->validate([
                'name' => 'required|string|max:50|unique:academic_years,name,' . $id,
                'code' => 'required|string|max:20|unique:academic_years,code,' . $id,
                'academic_calendar_year' => 'required|string|max:9',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => ['required', Rule::in(['upcoming', 'active', 'completed'])],
                'admission_period' => ['required', Rule::in(['ganjil', 'genap'])],
                'admission_start_date' => 'nullable|date',
                'admission_end_date' => 'nullable|date|after_or_equal:admission_start_date',
                'registration_start_date' => 'nullable|date',
                'registration_end_date' => 'nullable|date|after_or_equal:registration_start_date',
                'course_registration_start_date' => 'nullable|date',
                'course_registration_end_date' => 'nullable|date|after_or_equal:course_registration_start_date',
                'class_start_date' => 'nullable|date',
                'class_end_date' => 'nullable|date|after_or_equal:class_start_date',
                'mid_exam_start_date' => 'nullable|date',
                'mid_exam_end_date' => 'nullable|date|after_or_equal:mid_exam_start_date',
                'final_exam_start_date' => 'nullable|date',
                'final_exam_end_date' => 'nullable|date|after_or_equal:final_exam_start_date',
                'thesis_deadline' => 'nullable|date',
                'yudisium_date' => 'nullable|date',
                'max_credit_per_semester' => 'required|integer|min:1|max:50',
                'tuition_fee' => 'nullable|decimal:0,2|min:0',
                'registration_fee' => 'nullable|decimal:0,2|min:0',
                'is_visible_to_students' => 'boolean',
                'allow_course_registration' => 'boolean',
                'allow_schedule_changes' => 'boolean',
                'settings' => 'nullable|array',
            ]);

            // Set boolean values
            $validated['is_active'] = $validated['status'] === 'active';
            $validated['updated_by'] = $user->id;

            $academicYear = AcademicYearService::updateAcademicYear($id, $validated);

            return ResponseService::success(
                $academicYear,
                'Academic year updated successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::error(
                'Validation failed: ' . $e->getMessage(),
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update academic year: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Delete an academic year.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();

            // Only admin can delete academic years
            if (!$user || !$user->isAdmin()) {
                return ResponseService::error(
                    'Unauthorized. Only admin can delete academic years.',
                    null,
                    403
                );
            }

            AcademicYearService::deleteAcademicYear($id);

            return ResponseService::success(
                null,
                'Academic year deleted successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete academic year: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get the currently active academic year.
     */
    public function active(Request $request): JsonResponse
    {
        try {
            $activeYear = AcademicYearService::getActiveAcademicYear();

            return ResponseService::success(
                $activeYear,
                'Active academic year retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve active academic year: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Set the active academic year.
     */
    public function setActive(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'academic_year_id' => 'required|integer'
            ]);

            $user = $request->user();

            // Only admin can change active academic year
            if (!$user || !$user->isAdmin()) {
                return ResponseService::error(
                    'Unauthorized. Only admin can change active academic year.',
                    null,
                    403
                );
            }

            AcademicYearService::setActiveAcademicYear($validated['academic_year_id']);

            $activeYear = AcademicYearService::getActiveAcademicYear();

            return ResponseService::success(
                $activeYear,
                'Active academic year updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to set active academic year: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get academic year statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $stats = AcademicYearService::getStatistics();

            return ResponseService::success(
                $stats,
                'Academic year statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve academic year statistics: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Duplicate an academic year.
     */
    public function duplicate(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();

            // Only admin can duplicate academic years
            if (!$user || !$user->isAdmin()) {
                return ResponseService::error(
                    'Unauthorized. Only admin can duplicate academic years.',
                    null,
                    403
                );
            }

            $duplicatedYear = AcademicYearService::duplicateAcademicYear($id, $user->id);

            return ResponseService::success(
                $duplicatedYear,
                'Academic year duplicated successfully',
                201
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to duplicate academic year: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Toggle active status for an academic year.
     */
    public function toggleActive(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();

            // Only admin can toggle active status
            if (!$user || !$user->isAdmin()) {
                return ResponseService::error(
                    'Unauthorized. Only admin can toggle academic year status.',
                    null,
                    403
                );
            }

            $academicYear = AcademicYearService::toggleActiveStatus($id, $user->id);

            return ResponseService::success(
                $academicYear,
                'Academic year status updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to toggle academic year status: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
}