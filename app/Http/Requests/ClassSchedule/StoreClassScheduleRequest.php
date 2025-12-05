<?php

namespace App\Http\Requests\ClassSchedule;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassScheduleRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'program_study_id' => 'required|exists:program_studies,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester' => 'required|string|max:255',
            'online_percentage' => 'nullable|numeric|min:0|max:100',
            'offline_percentage' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'status' => 'sometimes|string|in:draft,active,completed,cancelled',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul jadwal wajib diisi',
            'title.max' => 'Judul jadwal maksimal 255 karakter',
            'program_study_id.required' => 'Program studi wajib dipilih',
            'program_study_id.exists' => 'Program studi tidak valid',
            'class_id.required' => 'Kelas wajib dipilih',
            'class_id.exists' => 'Kelas tidak valid',
            'academic_year_id.exists' => 'Tahun akademik tidak valid',
            'semester.required' => 'Semester wajib diisi',
            'online_percentage.numeric' => 'Persentase online harus berupa angka',
            'online_percentage.min' => 'Persentase online minimal 0',
            'online_percentage.max' => 'Persentase online maksimal 100',
            'offline_percentage.numeric' => 'Persentase offline harus berupa angka',
            'offline_percentage.min' => 'Persentase offline minimal 0',
            'offline_percentage.max' => 'Persentase offline maksimal 100',
            'status.in' => 'Status tidak valid',
        ];
    }
}
