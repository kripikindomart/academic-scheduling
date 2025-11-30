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
            'semester' => ['required', 'string', 'in:ganjil,genap'],
            'course_type' => ['required', 'string', 'in:mandatory,elective'],
            'level' => ['nullable', 'string', 'in:undergraduate,graduate,doctoral'],
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
            'course_code.required' => 'Kode matakuliah wajib diisi',
            'course_code.unique' => 'Kode matakuliah sudah ada',
            'course_name.required' => 'Nama matakuliah wajib diisi',
            'course_name.max' => 'Nama matakuliah maksimal 255 karakter',
            'credits.required' => 'SKS wajib diisi',
            'credits.integer' => 'SKS harus berupa angka',
            'credits.min' => 'SKS minimal 1',
            'credits.max' => 'SKS maksimal 12',
            'semester.required' => 'Semester wajib diisi',
            'semester.in' => 'Semester harus ganjil atau genap',
            'course_type.required' => 'Tipe matakuliah wajib diisi',
            'course_type.in' => 'Tipe matakuliah harus wajib atau pilihan',
            'level.required' => 'Jenjang pendidikan wajib diisi',
            'level.in' => 'Jenjang harus S1, S2, atau S3',
            'program_study_id.required' => 'Program studi wajib dipilih',
            'program_study_id.exists' => 'Program studi tidak valid',
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
            'course_code' => 'Kode Matakuliah',
            'course_name' => 'Nama Matakuliah',
            'description' => 'Deskripsi',
            'credits' => 'SKS',
            'semester' => 'Semester',
            'course_type' => 'Tipe Matakuliah',
            'level' => 'Jenjang Pendidikan',
            'is_active' => 'Status Aktif',
            'program_study_id' => 'Program Studi',
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