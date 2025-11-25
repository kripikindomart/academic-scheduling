<?php

namespace App\Http\Requests\ProgramStudy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseService;

class StoreProgramStudyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check() && auth('sanctum')->user()->hasPermissionTo('program_studies.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', 'unique:program_studies,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'faculty' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'in:undergraduate,graduate,doctoral'],
            'degree' => ['required', 'string', 'in:S1,S2,S3,D3,D4'],
            'duration_years' => ['required', 'integer', 'min:1', 'max:10'],
            'minimum_credits' => ['required', 'integer', 'min:1', 'max:500'],
            'head_of_program' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'office_location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
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
            'code.required' => 'Program code is required',
            'code.unique' => 'Program code already exists',
            'name.required' => 'Program name is required',
            'name.max' => 'Program name may not be greater than 255 characters',
            'faculty.required' => 'Faculty is required',
            'faculty.max' => 'Faculty name may not be greater than 255 characters',
            'level.required' => 'Level is required',
            'level.in' => 'Level must be undergraduate, graduate, or doctoral',
            'degree.required' => 'Degree is required',
            'degree.in' => 'Degree must be S1, S2, S3, D3, or D4',
            'duration_years.required' => 'Duration years is required',
            'duration_years.integer' => 'Duration years must be an integer',
            'duration_years.min' => 'Duration years must be at least 1',
            'duration_years.max' => 'Duration years may not be greater than 10',
            'minimum_credits.required' => 'Minimum credits is required',
            'minimum_credits.integer' => 'Minimum credits must be an integer',
            'minimum_credits.min' => 'Minimum credits must be at least 1',
            'minimum_credits.max' => 'Minimum credits may not be greater than 500',
            'email.email' => 'Please provide a valid email address',
            'email.max' => 'Email may not be greater than 255 characters',
            'phone.max' => 'Phone number may not be greater than 50 characters',
            'office_location.max' => 'Office location may not be greater than 255 characters',
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
            'code' => 'Program Code',
            'name' => 'Program Name',
            'description' => 'Description',
            'faculty' => 'Faculty',
            'level' => 'Level',
            'degree' => 'Degree',
            'duration_years' => 'Duration (Years)',
            'minimum_credits' => 'Minimum Credits',
            'head_of_program' => 'Head of Program',
            'email' => 'Email',
            'phone' => 'Phone',
            'office_location' => 'Office Location',
            'is_active' => 'Active Status',
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

        ResponseService::validationError($errors, 'Program study validation failed')->throwResponse();
    }
}