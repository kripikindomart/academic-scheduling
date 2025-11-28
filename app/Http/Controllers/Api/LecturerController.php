<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lecturer\StoreLecturerRequest;
use App\Http\Requests\Lecturer\UpdateLecturerRequest;
use App\Services\LecturerService;
use App\Models\Lecturer;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LecturerController extends Controller
{
    protected LecturerService $lecturerService;

    public function __construct(LecturerService $lecturerService)
    {
        $this->lecturerService = $lecturerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'status' => $request->input('status'),
            'employment_type' => $request->input('employment_type'),
            'faculty' => $request->input('faculty'),
            'department' => $request->input('department'),
            'rank' => $request->input('rank'),
            'highest_education' => $request->input('highest_education'),
            'is_active' => $request->input('is_active'),
            'specialization' => $request->input('specialization'),
        ];

        $result = $this->lecturerService->getLecturers(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'name'),
            $request->input('sort_direction', 'asc')
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['data'],
            'meta' => $result['meta'] ?? []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Define validation rules with XSS protection
        $validationRules = [
            'employee_number' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\-\.\/]+$/',
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s\.\,\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\+\-\s\(\)]+$/',
            'gender' => 'nullable|in:L,P',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'birth_place' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'province' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'postal_code' => 'nullable|string|max:10|regex:/^[0-9\-\s]+$/',
            'nationality' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'religion' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'id_card_number' => 'nullable|string|max:30|regex:/^[0-9\-\s]+$/',
            'passport_number' => 'nullable|string|max:20|regex:/^[A-Za-z0-9\-\s]+$/',
            'status' => 'nullable|in:Aktif,Cuti,Tidak',
            'employment_status' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\.\-]+$/',
            'employment_type' => 'nullable|in:Tetap,Kontrak,Paruh,Tamu',
            'hire_date' => 'nullable|date|before_or_equal:today',
            'termination_date' => 'nullable|date|after_or_equal:hire_date',
            'position' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'rank' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\.\-\'IVX]+$/',
            'specialization' => 'nullable|string|max:200',
            'department' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'faculty' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'highest_education' => 'nullable|in:S1,S2,S3',
            'education_institution' => 'nullable|string|max:200|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'education_major' => 'nullable|string|max:200|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'graduation_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:200',
            'research_interests' => 'nullable|array',
            'research_interests.*' => 'string|max:200',
            'publications' => 'nullable|array',
            'publications.*' => 'string|max:500',
            'office_room' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\s\.\-\/]+$/',
            'office_hours' => 'nullable|array',
            'office_hours.*' => 'string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string|max:1000',
            'program_study_id' => 'nullable|exists:program_studies,id'
        ];

        // Validate and sanitize input
        $validatedData = $request->validate($validationRules);

        // Sanitize text fields to prevent XSS
        $textFields = [
            'employee_number', 'name', 'birth_place', 'address', 'city', 'province',
            'nationality', 'religion', 'passport_number', 'employment_status',
            'employment_type', 'position', 'rank', 'specialization', 'department',
            'faculty', 'highest_education', 'education_institution', 'education_major',
            'office_room', 'notes'
        ];

        foreach ($textFields as $field) {
            if (isset($validatedData[$field])) {
                $validatedData[$field] = $this->sanitizeInput($validatedData[$field]);
            }
        }

        // Sanitize array fields
        $arrayFields = ['certifications', 'research_interests', 'publications', 'office_hours'];
        foreach ($arrayFields as $field) {
            if (isset($validatedData[$field]) && is_array($validatedData[$field])) {
                $validatedData[$field] = array_map([$this, 'sanitizeInput'], $validatedData[$field]);
                $validatedData[$field] = array_filter($validatedData[$field], function($value) {
                    return !empty(trim($value));
                });
            }
        }

        // Use validated data
        $data = $validatedData;

        // Remove empty arrays from JSON fields to prevent Array to string conversion
        $arrayFields = ['office_hours', 'certifications', 'research_interests', 'publications'];
        foreach ($arrayFields as $field) {
            if (isset($data[$field]) && (is_array($data[$field]) && empty($data[$field]))) {
                unset($data[$field]);
            }
        }

        // Remove empty string values for unique fields to prevent duplicate entry errors
        $uniqueFields = ['employee_number', 'id_card_number', 'email'];
        foreach ($uniqueFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                unset($data[$field]);
            }
        }

        // Debug: Log what data is being processed
        \Log::info('Lecturer form data received', [
            'data' => $data,
            'original_request' => $request->all(),
            'files' => $request->allFiles()
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = $file->store('lecturers/photos', 'public');
            $data['photo'] = $photoPath;
            \Log::info('Photo uploaded successfully', ['path' => $photoPath, 'original_name' => $file->getClientOriginalName()]);
        } elseif (isset($data['photo']) && (is_array($data['photo']) || is_object($data['photo']))) {
            // Remove photo field if it's an array/object (not a valid file)
            \Log::warning('Invalid photo data received', ['photo_data' => $data['photo']]);
            unset($data['photo']);
        } else {
            \Log::info('No photo in request', [
                'hasFile' => $request->hasFile('photo'),
                'request_keys' => array_keys($request->all()),
                'files_keys' => array_keys($request->allFiles()),
                'photo_in_data' => isset($data['photo']) ? 'yes' : 'no'
            ]);
        }

        $lecturer = $this->lecturerService->createLecturer($data);

        return response()->json([
            'success' => true,
            'message' => 'Lecturer created successfully',
            'data' => $lecturer
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lecturer $lecturer): JsonResponse
    {
        $lecturer->load([
            'programStudy',
            'user',
            'courses',
            'creator',
            'updater'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lecturer retrieved successfully',
            'data' => $lecturer
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lecturer $lecturer): JsonResponse
    {
        // Debug: Log incoming request data
        \Log::info('Lecturer update request', [
            'lecturer_id' => $lecturer->id,
            'original_data' => $lecturer->toArray(),
            'request_all' => $request->all(),
            'request_files' => $request->allFiles(),
            'request_content_type' => $request->header('Content-Type'),
            'request_method' => $request->method(),
            'request_input_all' => $request->input(),
            'request_post_data' => $_POST,
            'request_get_data' => $_GET,
            'request_server_content_type' => $_SERVER['CONTENT_TYPE'] ?? 'none',
            'request_request_data' => $_REQUEST,
        ]);

        // Define valid fields that exist in database
        $validFields = [
            'employee_number', 'name', 'email', 'phone', 'gender', 'birth_date', 'birth_place',
            'address', 'city', 'province', 'postal_code', 'nationality', 'religion', 'blood_type',
            'id_card_number', 'passport_number', 'status', 'employment_status', 'employment_type',
            'hire_date', 'termination_date', 'position', 'rank', 'specialization', 'department',
            'faculty', 'highest_education', 'education_institution', 'education_major',
            'graduation_year', 'certifications', 'research_interests', 'publications',
            'office_room', 'office_hours', 'photo', 'notes', 'program_study_id'
        ];

        // Define validation rules with XSS protection (same as store method)
        $validationRules = [
            'employee_number' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\-\.\/]+$/',
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s\.\,\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\+\-\s\(\)]+$/',
            'gender' => 'nullable|in:L,P',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'birth_place' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'province' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'postal_code' => 'nullable|string|max:10|regex:/^[0-9\-\s]+$/',
            'nationality' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'religion' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'id_card_number' => 'nullable|string|max:30|regex:/^[0-9\-\s]+$/',
            'passport_number' => 'nullable|string|max:20|regex:/^[A-Za-z0-9\-\s]+$/',
            'status' => 'nullable|in:Aktif,Cuti,Tidak',
            'employment_status' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\.\-]+$/',
            'employment_type' => 'nullable|in:Tetap,Kontrak,Paruh,Tamu',
            'hire_date' => 'nullable|date|before_or_equal:today',
            'termination_date' => 'nullable|date|after_or_equal:hire_date',
            'position' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'rank' => 'nullable|string|max:50|regex:/^[a-zA-Z\s\.\-\'IVX]+$/',
            'specialization' => 'nullable|string|max:200',
            'department' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'faculty' => 'nullable|string|max:100|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'highest_education' => 'nullable|in:S1,S2,S3',
            'education_institution' => 'nullable|string|max:200|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'education_major' => 'nullable|string|max:200|regex:/^[a-zA-Z\s\.\-\'àáâãäåæçèéêëìíîïðñòóôõö÷ùúûüýþÿ]+$/',
            'graduation_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:200',
            'research_interests' => 'nullable|array',
            'research_interests.*' => 'string|max:200',
            'publications' => 'nullable|array',
            'publications.*' => 'string|max:500',
            'office_room' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\s\.\-\/]+$/',
            'office_hours' => 'nullable|array',
            'office_hours.*' => 'string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string|max:1000',
            'program_study_id' => 'nullable|exists:program_studies,id'
        ];

        // For update, make email unique by excluding current lecturer
        $validationRules['email'] = 'required|email|max:255|unique:lecturers,email,' . $lecturer->id;

        // Validate and sanitize input
        $validatedData = $request->validate($validationRules);

        // Sanitize text fields to prevent XSS
        $textFields = [
            'employee_number', 'name', 'birth_place', 'address', 'city', 'province',
            'nationality', 'religion', 'passport_number', 'employment_status',
            'employment_type', 'position', 'rank', 'specialization', 'department',
            'faculty', 'highest_education', 'education_institution', 'education_major',
            'office_room', 'notes'
        ];

        foreach ($textFields as $field) {
            if (isset($validatedData[$field])) {
                $validatedData[$field] = $this->sanitizeInput($validatedData[$field]);
            }
        }

        // Sanitize array fields
        $arrayFields = ['certifications', 'research_interests', 'publications', 'office_hours'];
        foreach ($arrayFields as $field) {
            if (isset($validatedData[$field]) && is_array($validatedData[$field])) {
                $validatedData[$field] = array_map([$this, 'sanitizeInput'], $validatedData[$field]);
                $validatedData[$field] = array_filter($validatedData[$field], function($value) {
                    return !empty(trim($value));
                });
            }
        }

        // Use validated data
        $data = $validatedData;

        \Log::info('Processed data for update', ['data' => $data]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = $file->store('lecturers/photos', 'public');
            $data['photo'] = $photoPath;
        }

        $updatedLecturer = $this->lecturerService->updateLecturer($lecturer, $data);

        \Log::info('Lecturer updated successfully', [
            'lecturer_id' => $updatedLecturer->id,
            'updated_data' => $updatedLecturer->toArray()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lecturer updated successfully',
            'data' => $updatedLecturer
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lecturer $lecturer): JsonResponse
    {
        $this->lecturerService->deleteLecturer($lecturer);

        return response()->json([
            'success' => true,
            'message' => 'Lecturer deleted successfully'
        ]);
    }

    /**
     * Get lecturer statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $statistics = $this->lecturerService->getLecturerStatistics(
            $request->input('program_study_id'),
            $request->boolean('include_trash', false)
        );

        return response()->json([
            'success' => true,
            'message' => 'Lecturer statistics retrieved successfully',
            'data' => $statistics
        ]);
    }

    /**
     * Get lecturer teaching load.
     */
    public function teachingLoad(Lecturer $lecturer): JsonResponse
    {
        $teachingLoad = $this->lecturerService->getLecturerTeachingLoad($lecturer);

        return response()->json(["success" => true, "message" => 'Lecturer teaching load retrieved successfully', "data" => $teachingLoad]);
    }

    /**
     * Get available lecturers for course assignment.
     */
    public function availableForCourse(Course $course): JsonResponse
    {
        $availableLecturers = $this->lecturerService->getAvailableLecturers($course);

        return response()->json(["success" => true, "message" => 'Available lecturers retrieved successfully', "data" => $availableLecturers]);
    }

    /**
     * Assign course to lecturer.
     */
    public function assignCourse(Request $request, Lecturer $lecturer, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'nullable|in:lecturer,assistant,coordinator',
            'academic_year' => 'nullable|integer',
            'semester' => 'nullable|in:ganjil,genap',
        ]);

        $assignment = $this->lecturerService->assignCourseToLecturer($lecturer, $course, $validated);

        return response()->json(["success" => true, "message" => 'Course assigned to lecturer successfully', "data" => $assignment]);
    }

    /**
     * Get lecturers by program study.
     */
    public function getByProgramStudy(Request $request, $programStudyId): JsonResponse
    {
        $filters = [
            'program_study_id' => $programStudyId,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'employment_type' => $request->input('employment_type'),
        ];

        $lecturers = $this->lecturerService->getLecturers($filters);

        return response()->json([
            'success' => true,
            'message' => $lecturers['message'],
            'data' => $lecturers['data'],
            'meta' => $lecturers['meta'] ?? []
        ]);
    }

    /**
     * Get active lecturers only.
     */
    public function getActive(Request $request): JsonResponse
    {
        $filters = [
            'is_active' => true,
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'status' => 'active',
        ];

        $lecturers = $this->lecturerService->getLecturers($filters);

        return response()->json([
            'success' => true,
            'message' => $lecturers['message'],
            'data' => $lecturers['data'],
            'meta' => $lecturers['meta'] ?? []
        ]);
    }

    /**
     * Get lecturers by faculty.
     */
    public function getByFaculty(Request $request, $faculty): JsonResponse
    {
        $filters = [
            'faculty' => $faculty,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'employment_type' => $request->input('employment_type'),
        ];

        $lecturers = $this->lecturerService->getLecturers($filters);

        return response()->json([
            'success' => true,
            'message' => $lecturers['message'],
            'data' => $lecturers['data'],
            'meta' => $lecturers['meta'] ?? []
        ]);
    }

    /**
     * Get lecturers for scheduling.
     */
    public function getForScheduling(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'day' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $lecturers = $this->lecturerService->getLecturersForScheduling(
            $validated['day'],
            $validated['time']
        );

        return response()->json(["success" => true, "message" => 'Available lecturers for scheduling retrieved successfully', "data" => $lecturers]);
    }

    /**
     * Bulk update lecturers.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lecturer_ids' => 'required|array',
            'lecturer_ids.*' => 'exists:lecturers,id',
            'updates' => 'required|array',
            'updates.status' => 'sometimes|in:active,inactive,on_leave,terminated,retired',
            'updates.employment_type' => 'sometimes|in:permanent,contract,part_time,guest',
            'updates.is_active' => 'sometimes|boolean',
            'updates.faculty' => 'sometimes|string|max:255',
            'updates.department' => 'sometimes|string|max:255',
        ]);

        $updatedCount = $this->lecturerService->bulkUpdateLecturers(
            $validated['lecturer_ids'],
            $validated['updates']
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updatedCount} lecturers",
            'data' => ['updated_count' => $updatedCount]
        ]);
    }

    /**
     * Bulk delete lecturers.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:lecturers,id',
        ]);

        $deletedCount = $this->lecturerService->bulkDeleteLecturers($validated['ids']);

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} lecturers",
            'data' => ['deleted_count' => $deletedCount]
        ]);
    }

    /**
     * Import lecturers from CSV/Excel.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // Max 10MB
            'program_study_id' => 'nullable|exists:program_studies,id',
        ]);

        $result = $this->lecturerService->importLecturers(
            $request->file('file'),
            $request->input('program_study_id'),
            $request->user()
        );

        return response()->json(["success" => true, "message" => 'Lecturers import completed', "data" => $result]);
    }

    /**
     * Export lecturers to CSV/Excel.
     */
    public function export(Request $request): JsonResponse
    {
        $filters = $request->only([
            'program_study_id',
            'status',
            'employment_type',
            'faculty',
            'department',
            'is_active',
        ]);

        $format = $request->input('format', 'csv');
        $filePath = $this->lecturerService->exportLecturers($filters, $format);

        return response()->json([
            'success' => true,
            'message' => 'Lecturers export completed',
            'data' => ['download_url' => asset($filePath)]
        ]);
    }

    /**
     * Get lecturer search suggestions.
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->input('query');
        $limit = $request->input('limit', 10);

        $suggestions = $this->lecturerService->getLecturerSearchSuggestions($query, $limit);

        return response()->json(["success" => true, "message" => 'Search suggestions retrieved successfully', "data" => $suggestions]);
    }

    /**
     * Get lecturers with high workload.
     */
    public function getHighWorkloadLecturers(Request $request): JsonResponse
    {
        $threshold = $request->input('threshold', 90);
        $lecturers = $this->lecturerService->getLecturersWithHighWorkload($threshold);

        return response()->json(["success" => true, "message" => 'High workload lecturers retrieved successfully', "data" => $lecturers]);
    }

    /**
     * Restore deleted lecturer.
     */
    public function restore($id): JsonResponse
    {
        $lecturer = $this->lecturerService->restoreLecturer($id);

        return response()->json(["success" => true, "message" => 'Lecturer restored successfully', "data" => $lecturer]);
    }

    /**
     * Permanently delete lecturer.
     */
    public function forceDelete($id): JsonResponse
    {
        $this->lecturerService->forceDeleteLecturer($id);

        return response()->json(["success" => true, "message" => 'Lecturer permanently deleted', "data" => null]);
    }

    /**
     * Update lecturer status.
     */
    public function updateStatus(Request $request, Lecturer $lecturer): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,on_leave,terminated,retired',
            'termination_date' => 'required_if:status,terminated|required_if:status,retired|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $updatedLecturer = $this->lecturerService->updateLecturerStatus($lecturer, $validated);

        return response()->json(["success" => true, "message" => 'Lecturer status updated successfully', "data" => $updatedLecturer]);
    }

    /**
     * Get lecturer attendance summary.
     */
    public function attendanceSummary(Lecturer $lecturer, Request $request): JsonResponse
    {
        $semester = $request->input('semester');
        $academicYear = $request->input('academic_year');

        $summary = $this->lecturerService->getLecturerAttendanceSummary($lecturer, $semester, $academicYear);

        return response()->json(["success" => true, "message" => 'Lecturer attendance summary retrieved successfully', "data" => $summary]);
    }

    /**
     * Get lecturers by employment type.
     */
    public function getByEmploymentType(Request $request, $type): JsonResponse
    {
        $filters = [
            'employment_type' => $type,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'faculty' => $request->input('faculty'),
        ];

        $lecturers = $this->lecturerService->getLecturers($filters);

        return response()->json([
            'success' => true,
            'message' => $lecturers['message'],
            'data' => $lecturers['data'],
            'meta' => $lecturers['meta'] ?? []
        ]);
    }

    /**
     * Get trashed lecturers.
     */
    public function trash(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'program_study_id' => $request->input('program_study_id'),
            'faculty' => $request->input('faculty'),
            'department' => $request->input('department'),
            'only_trashed' => $request->boolean('only_trashed', true),
        ];

        $result = $this->lecturerService->getLecturers(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'name'),
            $request->input('sort_direction', 'asc')
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['data'],
            'meta' => $result['meta'] ?? []
        ]);
    }

    /**
     * Bulk restore deleted lecturers.
     */
    public function bulkRestore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:lecturers,id',
        ]);

        $restoredCount = $this->lecturerService->bulkRestoreLecturers($validated['ids']);

        return response()->json([
            'success' => true,
            'message' => "Successfully restored {$restoredCount} lecturers",
            'data' => ['restored_count' => $restoredCount]
        ]);
    }

    /**
     * Bulk permanently delete lecturers.
     */
    public function bulkForceDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:lecturers,id',
        ]);

        $deletedCount = $this->lecturerService->bulkForceDeleteLecturers($validated['ids']);

        return response()->json([
            'success' => true,
            'message' => "Successfully permanently deleted {$deletedCount} lecturers",
            'data' => ['deleted_count' => $deletedCount]
        ]);
    }

    /**
     * Duplicate a lecturer.
     */
    public function duplicate(Lecturer $lecturer, Request $request): JsonResponse
    {
        try {
            $duplicatedLecturer = $this->lecturerService->duplicateLecturer($lecturer->id, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Lecturer duplicated successfully',
                'data' => $duplicatedLecturer
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate lecturer: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Create user account for lecturer.
     */
    public function createUserAccount(Lecturer $lecturer, Request $request): JsonResponse
    {
        try {
            // Check if lecturer already has a user account
            if ($lecturer->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lecturer already has a user account',
                    'data' => null
                ], 422);
            }

            // Check if email already exists in users table
            if (User::where('email', $lecturer->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already exists in users table',
                    'data' => null
                ], 422);
            }

            // Generate default password
            if ($request->has('password')) {
                $defaultPassword = $request->input('password');
            } else {
                // More secure password generation
                $nameWithoutSpaces = preg_replace('/[^a-zA-Z]/', '', $lecturer->name);
                $defaultPassword = strtolower($nameWithoutSpaces) . '@2024!';
            }

            // Create user account
            $user = User::create([
                'name' => $lecturer->name,
                'email' => $lecturer->email,
                'password' => Hash::make($defaultPassword),
                'email_verified_at' => now(),
            ]);

            // Assign 'Dosen' role
            $dosenRole = \Spatie\Permission\Models\Role::where('name', 'Dosen')->first();
            if ($dosenRole) {
                $user->assignRole($dosenRole);
            }

            // Update lecturer with user_id
            $lecturer->update([
                'user_id' => $user->id,
                'updated_by' => $request->user()->id,
            ]);

            \Log::info('User account created for lecturer', [
                'lecturer_id' => $lecturer->id,
                'lecturer_name' => $lecturer->name,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'created_by' => $request->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User account created successfully for lecturer',
                'data' => [
                    'lecturer' => $lecturer->load('user'),
                    'user' => $user->load('roles'),
                    'default_password' => $defaultPassword,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create user account for lecturer', [
                'lecturer_id' => $lecturer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user account: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Bulk create user accounts for lecturers.
     */
    public function bulkCreateUserAccounts(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:lecturers,id'
            ]);

            $lecturerIds = $validated['ids'];
            $results = [];
            $successCount = 0;
            $errorCount = 0;

            // Get lecturers without user accounts
            $lecturers = Lecturer::whereIn('id', $lecturerIds)
                ->whereNull('user_id')
                ->get();

            // Log skipped lecturers (those with user accounts)
            $skippedLecturers = Lecturer::whereIn('id', $lecturerIds)
                ->whereNotNull('user_id')
                ->get();

            if ($skippedLecturers->count() > 0) {
                \Log::info('Bulk create user accounts - skipped lecturers with existing accounts', [
                    'skipped_count' => $skippedLecturers->count(),
                    'skipped_ids' => $skippedLecturers->pluck('id')->toArray(),
                    'total_requested' => count($lecturerIds)
                ]);
            }

            foreach ($lecturers as $lecturer) {
                try {
                    // Check if email already exists in users table
                    if (User::where('email', $lecturer->email)->exists()) {
                        $results[] = [
                            'lecturer_id' => $lecturer->id,
                            'lecturer_name' => $lecturer->name,
                            'success' => false,
                            'message' => 'Email already exists in users table'
                        ];
                        $errorCount++;
                        continue;
                    }

                    // Generate default password with enhanced logic
                    $nameWithoutSpaces = preg_replace('/[^a-zA-Z]/', '', $lecturer->name);
                    $defaultPassword = strtolower($nameWithoutSpaces) . '@2024!';

                    // Create user account
                    $user = User::create([
                        'name' => $lecturer->name,
                        'email' => $lecturer->email,
                        'password' => Hash::make($defaultPassword),
                        'email_verified_at' => now(),
                    ]);

                    // Assign 'Dosen' role
                    $dosenRole = \Spatie\Permission\Models\Role::where('name', 'Dosen')->first();
                    if ($dosenRole) {
                        $user->assignRole($dosenRole);
                    }

                    // Update lecturer with user_id
                    $lecturer->update([
                        'user_id' => $user->id,
                        'updated_by' => $request->user()->id,
                    ]);

                    $results[] = [
                        'lecturer_id' => $lecturer->id,
                        'lecturer_name' => $lecturer->name,
                        'success' => true,
                        'message' => 'User account created successfully',
                        'default_password' => $defaultPassword
                    ];
                    $successCount++;

                    \Log::info('User account created for lecturer (bulk)', [
                        'lecturer_id' => $lecturer->id,
                        'lecturer_name' => $lecturer->name,
                        'user_id' => $user->id,
                        'created_by' => $request->user()->id,
                    ]);

                } catch (\Exception $e) {
                    $results[] = [
                        'lecturer_id' => $lecturer->id,
                        'lecturer_name' => $lecturer->name,
                        'success' => false,
                        'message' => $e->getMessage()
                    ];
                    $errorCount++;

                    \Log::error('Failed to create user account for lecturer (bulk)', [
                        'lecturer_id' => $lecturer->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk operation completed. {$successCount} accounts created, {$errorCount} failed.",
                'data' => [
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'results' => $results
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to bulk create user accounts', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk create user accounts: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Sanitize input to prevent XSS attacks
     */
    private function sanitizeInput($input)
    {
        if (is_string($input)) {
            // Remove potentially dangerous HTML tags and scripts
            $input = strip_tags($input);

            // Convert special characters to HTML entities
            $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Remove any remaining potentially harmful characters
            $input = preg_replace('/[\x00-\x1F\x7F]/', '', $input);

            // Trim whitespace
            return trim($input);
        }

        return $input;
    }
}
