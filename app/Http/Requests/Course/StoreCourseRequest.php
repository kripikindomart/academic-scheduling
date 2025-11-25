<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseService;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check() && auth('sanctum')->user()->hasPermissionTo('courses.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_code' => ['required', 'string', 'max:20', 'unique:courses,course_code'],
            'course_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'credits' => ['required', 'integer', 'min:1', 'max:12'],
            'semester' => ['required', 'string', 'max:20'],
            'academic_year' => ['required', 'string', 'max:10', 'regex:/^\d{4}\/\d{4}$/'],
            'course_type' => ['required', 'string', 'in:mandatory,elective'],
            'level' => ['required', 'string', 'in:undergraduate,graduate,doctoral'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'program_study_id' => ['required', 'exists:program_studies,id'],
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
            'course_code.required' => 'Course code is required',
            'course_code.unique' => 'Course code already exists',
            'course_name.required' => 'Course name is required',
            'course_name.max' => 'Course name may not be greater than 255 characters',
            'credits.required' => 'Credits is required',
            'credits.integer' => 'Credits must be an integer',
            'credits.min' => 'Credits must be at least 1',
            'credits.max' => 'Credits may not be greater than 12',
            'semester.required' => 'Semester is required',
            'academic_year.required' => 'Academic year is required',
            'academic_year.regex' => 'Academic year must be in format YYYY/YYYY (e.g., 2024/2025)',
            'course_type.required' => 'Course type is required',
            'course_type.in' => 'Course type must be either mandatory or elective',
            'level.required' => 'Course level is required',
            'level.in' => 'Level must be undergraduate, graduate, or doctoral',
            'capacity.required' => 'Capacity is required',
            'capacity.integer' => 'Capacity must be an integer',
            'capacity.min' => 'Capacity must be at least 1',
            'capacity.max' => 'Capacity may not be greater than 500',
            'program_study_id.required' => 'Program study is required',
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