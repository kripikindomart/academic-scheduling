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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Set default degree if not provided, use level to determine
        $degree = $this->degree ?: $this->level ?: 'S1';

        $this->merge([
            'faculty' => $this->faculty ?: 'Sekolah Pascasarjana',
            'level' => $this->mapLevel($this->level),
            'degree' => $degree,
            'duration_years' => $this->duration_years ?: $this->getDefaultDuration($degree),
            'minimum_credits' => $this->minimum_credits ?: $this->getDefaultCredits($degree),
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
        return [
            'code' => ['required', 'string', 'max:20', 'unique:program_studies,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'faculty' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'in:undergraduate,graduate,doctoral,S1,S2,S3,D3,D4'],
            'degree' => ['required', 'string', 'in:S1,S2,S3,D3,D4'],
            'duration_years' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'minimum_credits' => ['sometimes', 'integer', 'min:1', 'max:500'],
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
            'code.required' => 'Kode program studi wajib diisi',
            'code.unique' => 'Kode program studi sudah ada',
            'code.max' => 'Kode program studi maksimal 20 karakter',
            'name.required' => 'Nama program studi wajib diisi',
            'name.max' => 'Nama program studi maksimal 255 karakter',
            'faculty.required' => 'Fakultas wajib diisi',
            'faculty.max' => 'Nama fakultas maksimal 255 karakter',
            'level.required' => 'Jenjang pendidikan wajib diisi',
            'level.in' => 'Jenjang pendidikan harus S1, S2, S3, D3, atau D4',
            'degree.required' => 'Gelar akademik wajib diisi',
            'degree.in' => 'Gelar akademik harus S1, S2, S3, D3, atau D4',
            'duration_years.integer' => 'Durasi tahun harus berupa angka',
            'duration_years.min' => 'Durasi tahun minimal 1 tahun',
            'duration_years.max' => 'Durasi tahun maksimal 10 tahun',
            'minimum_credits.integer' => 'Jumlah minimal SKS harus berupa angka',
            'minimum_credits.min' => 'Jumlah minimal SKS minimal 1',
            'minimum_credits.max' => 'Jumlah minimal SKS maksimal 500',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'phone.max' => 'Nomor telepon maksimal 50 karakter',
            'office_location.max' => 'Lokasi kantor maksimal 255 karakter',
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