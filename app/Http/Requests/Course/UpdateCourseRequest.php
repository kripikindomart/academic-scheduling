<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseService;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check() && auth('sanctum')->user()->hasPermissionTo('courses.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $courseId = $this->route('course');

        return [
            'course_code' => ['sometimes', 'string', 'max:20', 'unique:courses,course_code,' . $courseId],
            'course_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'credits' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'semester' => ['sometimes', 'string', 'max:20'],
            'academic_year' => ['sometimes', 'string', 'max:10', 'regex:/^\d{4}\/\d{4}$/'],
            'course_type' => ['sometimes', 'string', 'in:mandatory,elective'],
            'level' => ['sometimes', 'string', 'in:undergraduate,graduate,doctoral'],
            'capacity' => ['sometimes', 'integer', 'min:1', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'program_study_id' => ['sometimes', 'exists:program_studies,id'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'course_code.unique' => 'Course code already exists',
            'course_name.max' => 'Course name may not be greater than 255 characters',
            'credits.integer' => 'Credits must be an integer',
            'credits.min' => 'Credits must be at least 1',
            'credits.max' => 'Credits may not be greater than 12',
            'academic_year.regex' => 'Academic year must be in format YYYY/YYYY (e.g., 2024/2025)',
            'course_type.in' => 'Course type must be either mandatory or elective',
            'level.in' => 'Level must be undergraduate, graduate, or doctoral',
            'capacity.integer' => 'Capacity must be an integer',
            'capacity.min' => 'Capacity must be at least 1',
            'capacity.max' => 'Capacity may not be greater than 500',
            'program_study_id.exists' => 'Selected program study is invalid',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'course_code' => 'Course Code',
            'course_name' => 'Course Name',
            'description' => 'Description',
            'credits' => 'Credits',
            'semester' => 'Semester',
            'academic_year' => 'Academic Year',
            'course_type' => 'Course Type',
            'level' => 'Level',
            'capacity' => 'Capacity',
            'is_active' => 'Active Status',
            'program_study_id' => 'Program Study',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $errors = $validator->errors()->toArray();

        ResponseService::validationError($errors, 'Course validation failed')->throwResponse();
    }
}