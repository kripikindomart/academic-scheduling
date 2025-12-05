<?php

namespace App\Http\Requests\Class;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:classes,code',
            'program_study_id' => 'required|exists:program_studies,id',
            'batch_year' => 'required|integer|min:2000|max:' . (date('Y') + 10),
            'academic_year_id' => 'required|exists:academic_years,id',
            'capacity' => 'required|integer|min:1|max:200',
            'room_number' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kelas wajib diisi.',
            'name.max' => 'Nama kelas maksimal 100 karakter.',
            'code.unique' => 'Kode kelas sudah digunakan.',
            'code.max' => 'Kode kelas maksimal 50 karakter.',
            'program_study_id.required' => 'Program studi wajib dipilih.',
            'program_study_id.exists' => 'Program studi tidak valid.',
            'batch_year.required' => 'Tahun angkatan wajib diisi.',
            'batch_year.integer' => 'Tahun angkatan harus berupa angka.',
            'batch_year.min' => 'Tahun angkatan minimal 2000.',
            'batch_year.max' => 'Tahun angkatan tidak valid.',
            'academic_year_id.required' => 'Tahun akademik wajib dipilih.',
            'academic_year_id.exists' => 'Tahun akademik tidak valid.',
            'capacity.required' => 'Kapasitas kelas wajib diisi.',
            'capacity.min' => 'Kapasitas kelas minimal 1 mahasiswa.',
            'capacity.max' => 'Kapasitas kelas maksimal 200 mahasiswa.',
            'room_number.max' => 'Nomor ruangan maksimal 50 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nama Kelas',
            'code' => 'Kode Kelas',
            'program_study_id' => 'Program Studi',
            'batch_year' => 'Tahun Angkatan',
            'academic_year_id' => 'Tahun Akademik',
            'capacity' => 'Kapasitas',
            'room_number' => 'Nomor Ruangan',
            'description' => 'Deskripsi',
            'is_active' => 'Status Aktif',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->capacity && $this->capacity < 1) {
                $validator->errors()->add('capacity', 'Kapasitas kelas minimal 1 mahasiswa.');
            }
        });
    }
}