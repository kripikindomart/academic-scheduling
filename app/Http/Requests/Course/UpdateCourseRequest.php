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
        $courseId = $this->route('course')?->id ?? $this->route('course');

        return [
            'course_code' => ['sometimes', 'string', 'max:20', 'unique:courses,course_code,' . $courseId . ',id'],
            'course_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'credits' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'semester' => ['sometimes', 'string', 'in:ganjil,genap'],
            'course_type' => ['sometimes', 'string', 'in:mandatory,elective'],
            'level' => ['nullable', 'string', 'in:undergraduate,graduate,doctoral'],
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
            'course_code.unique' => 'Kode matakuliah sudah ada',
            'course_name.max' => 'Nama matakuliah maksimal 255 karakter',
            'credits.integer' => 'SKS harus berupa angka',
            'credits.min' => 'SKS minimal 1',
            'credits.max' => 'SKS maksimal 12',
            'semester.in' => 'Semester harus ganjil atau genap',
            'course_type.in' => 'Tipe matakuliah harus wajib atau pilihan',
            'level.in' => 'Jenjang harus S1, S2, atau S3',
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