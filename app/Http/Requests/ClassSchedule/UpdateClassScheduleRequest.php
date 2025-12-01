<?php

namespace App\Http\Requests\ClassSchedule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassScheduleRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'program_study_id' => 'sometimes|exists:program_studies,id',
            'class_id' => 'sometimes|exists:school_classes,id',
            'academic_year_id' => 'sometimes|nullable|exists:academic_years,id',
            'semester' => 'sometimes|string|max:255',
            'online_percentage' => 'sometimes|nullable|numeric|min:0|max:100',
            'offline_percentage' => 'sometimes|nullable|numeric|min:0|max:100',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|string|in:draft,active,completed,cancelled',
        ];
    }
}
