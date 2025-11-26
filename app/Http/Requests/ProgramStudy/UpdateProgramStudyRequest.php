<?php

namespace App\Http\Requests\ProgramStudy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseService;

class UpdateProgramStudyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check() && auth('sanctum')->user()->hasPermissionTo('program_studies.edit');
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Get original request data
        $originalData = $this->all();

        // Log the incoming data for debugging
        \Log::info('UpdateProgramStudyRequest incoming data:', [
            'original' => $originalData,
            'code' => $this->code,
            'level' => $this->level,
            'degree' => $this->degree
        ]);

        // Clean and process the code field
        $code = $this->code;
        if (is_string($code)) {
            $code = trim($code);
            $code = preg_replace('/[^a-zA-Z0-9\-_]/', '', $code);
        }

        // Set default degree if not provided, use level to determine
        $degree = $this->degree ?: $this->level ?: 'S1';

        $this->merge([
            'code' => $code,
            'faculty' => $this->faculty ?: 'Sekolah Pascasarjana',
            'level' => $this->mapLevel($this->level),
            'degree' => $degree,
            'duration_years' => $this->duration_years ?: $this->getDefaultDuration($degree),
            'minimum_credits' => $this->minimum_credits ?: $this->getDefaultCredits($degree),
        ]);

        // Log the processed data
        \Log::info('UpdateProgramStudyRequest processed data:', [
            'processed' => $this->all(),
            'cleaned_code' => $code
        ]);
    }

    /**
     * Map level from frontend format to database format
     *
     * @param string|null $level
     * @return string
     */
    private function mapLevel(?string $level): string
    {
        $mapping = [
            'S1' => 'undergraduate',
            'S2' => 'graduate',
            'S3' => 'doctoral',
            'D3' => 'undergraduate',
            'D4' => 'undergraduate',
        ];

        return $mapping[$level] ?? $level ?? 'undergraduate';
    }

    /**
     * Get default duration based on degree
     *
     * @param string|null $degree
     * @return int
     */
    private function getDefaultDuration(?string $degree): int
    {
        $defaults = [
            'D3' => 3,
            'D4' => 4,
            'S1' => 4,
            'S2' => 2,
            'S3' => 3,
        ];

        return $defaults[$degree] ?? 4;
    }

    /**
     * Get default minimum credits based on degree
     *
     * @param string|null $degree
     * @return int
     */
    private function getDefaultCredits(?string $degree): int
    {
        $defaults = [
            'D3' => 110,
            'D4' => 144,
            'S1' => 144,
            'S2' => 36,
            'S3' => 48,
        ];

        return $defaults[$degree] ?? 144;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $programId = $this->route('program_study');

        // Only validate code if it's provided and not empty
        $rules = [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'faculty' => ['sometimes', 'string', 'max:255'],
            'level' => ['sometimes', 'string', 'in:undergraduate,graduate,doctoral,S1,S2,S3,D3,D4'],
            'degree' => ['sometimes', 'string', 'in:S1,S2,S3,D3,D4'],
            'duration_years' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'minimum_credits' => ['sometimes', 'integer', 'min:1', 'max:500'],
            'head_of_program' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'office_location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];

        // Temporarily disable code validation to debug the SQL issue
        // if ($this->has('code') && is_string($this->input('code'))) {
        //     $cleanCode = trim($this->input('code'));
        //     if (!empty($cleanCode)) {
        //         $rules['code'] = [
        //             'string',
        //             'max:20',
        //             function ($attribute, $value, $fail) use ($programId) {
        //                 // Manual unique validation to avoid SQL injection
        //                 $exists = \DB::table('program_studies')
        //                     ->where('code', $value)
        //                     ->where('id', '!=', $programId)
        //                     ->exists();

        //                 if ($exists) {
        //                     $fail('Kode program studi sudah digunakan.');
        //                 }
        //             }
        //         ];
        //     }
        // }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'Program code already exists',
            'name.max' => 'Program name may not be greater than 255 characters',
            'faculty.max' => 'Faculty name may not be greater than 255 characters',
            'level.in' => 'Level must be undergraduate, graduate, or doctoral',
            'degree.in' => 'Degree must be S1, S2, S3, D3, or D4',
            'duration_years.integer' => 'Duration years must be an integer',
            'duration_years.min' => 'Duration years must be at least 1',
            'duration_years.max' => 'Duration years may not be greater than 10',
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