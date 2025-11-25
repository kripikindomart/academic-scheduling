<?php

namespace App\Http\Requests\Lecturer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLecturerRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'employee_number' => 'nullable|string|max:20|unique:lecturers,employee_number',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:lecturers,email',
            'phone' => 'nullable|string|max:50',
            'gender' => 'required|in:male,female,other',
            'birth_date' => 'required|date|before:today',
            'birth_place' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'nationality' => 'nullable|string|max:100|default:Indonesia',
            'religion' => 'required|string|max:100',
            'blood_type' => 'nullable|string|max:10',
            'id_card_number' => 'required|string|max:50|unique:lecturers,id_card_number',
            'passport_number' => 'nullable|string|max:50|unique:lecturers,passport_number',
            'status' => 'nullable|in:active,inactive,on_leave,terminated,retired|default:active',
            'employment_status' => 'required|string|max:100',
            'employment_type' => 'nullable|in:permanent,contract,part_time,guest|default:permanent',
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date|after:hire_date',
            'position' => 'required|string|max:255',
            'rank' => 'nullable|string|max:100',
            'specialization' => 'nullable|array',
            'specialization.*' => 'string|max:255',
            'department' => 'required|string|max:255',
            'faculty' => 'required|string|max:255',
            'highest_education' => 'nullable|in:S1,S2,S3',
            'education_institution' => 'nullable|string|max:255',
            'education_major' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:255',
            'research_interests' => 'nullable|array',
            'research_interests.*' => 'string|max:255',
            'publications' => 'nullable|array',
            'publications.*' => 'string|max:500',
            'academic_load' => 'nullable|integer|min:1|max:24|default:12',
            'office_room' => 'nullable|string|max:100',
            'office_hours' => 'nullable|array',
            'office_hours.*' => 'array',
            'office_hours.*.start' => 'required_with:office_hours.*|date_format:H:i',
            'office_hours.*.end' => 'required_with:office_hours.*|date_format:H:i|after:office_hours.*.start',
            'is_active' => 'nullable|boolean|default:true',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:1000',
            'program_study_id' => 'nullable|exists:program_studies,id',
            'user_id' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'employee_number.unique' => 'Employee number already exists',
            'email.unique' => 'Email already registered',
            'id_card_number.unique' => 'ID card number already exists',
            'passport_number.unique' => 'Passport number already exists',
            'birth_date.before' => 'Birth date must be before today',
            'termination_date.after' => 'Termination date must be after hire date',
            'graduation_year.max' => 'Graduation year cannot be in the future',
            'academic_load.max' => 'Academic load cannot exceed 24 credit hours',
            'program_study_id.exists' => 'Selected program study does not exist',
            'user_id.exists' => 'Selected user does not exist',
            'office_hours.*.start.date_format' => 'Office hours start time must be in HH:MM format',
            'office_hours.*.end.date_format' => 'Office hours end time must be in HH:MM format',
            'office_hours.*.end.after' => 'Office hours end time must be after start time',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'employee_number' => 'Employee Number',
            'name' => 'Full Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'gender' => 'Gender',
            'birth_date' => 'Birth Date',
            'birth_place' => 'Birth Place',
            'address' => 'Address',
            'city' => 'City',
            'province' => 'Province',
            'postal_code' => 'Postal Code',
            'nationality' => 'Nationality',
            'religion' => 'Religion',
            'blood_type' => 'Blood Type',
            'id_card_number' => 'ID Card Number',
            'passport_number' => 'Passport Number',
            'status' => 'Status',
            'employment_status' => 'Employment Status',
            'employment_type' => 'Employment Type',
            'hire_date' => 'Hire Date',
            'termination_date' => 'Termination Date',
            'position' => 'Position',
            'rank' => 'Academic Rank',
            'specialization' => 'Specialization',
            'department' => 'Department',
            'faculty' => 'Faculty',
            'highest_education' => 'Highest Education',
            'education_institution' => 'Education Institution',
            'education_major' => 'Education Major',
            'graduation_year' => 'Graduation Year',
            'certifications' => 'Certifications',
            'research_interests' => 'Research Interests',
            'publications' => 'Publications',
            'academic_load' => 'Academic Load',
            'office_room' => 'Office Room',
            'office_hours' => 'Office Hours',
            'is_active' => 'Active Status',
            'photo' => 'Photo',
            'notes' => 'Notes',
            'program_study_id' => 'Program Study',
            'user_id' => 'User Account',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'status' => $this->status ?? 'active',
            'employment_type' => $this->employment_type ?? 'permanent',
            'is_active' => $this->is_active ?? true,
            'nationality' => $this->nationality ?? 'Indonesia',
            'academic_load' => $this->academic_load ?? 12,
        ]);
    }
}