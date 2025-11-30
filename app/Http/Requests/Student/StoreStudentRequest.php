<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
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
            // Field wajib dasar
            'student_number' => 'required|string|max:20|unique:students,student_number',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:students,email',
            'program_study_id' => 'required|exists:program_studies,id',

            // Field informasi pribadi (optional)
            'phone' => 'nullable|string|max:50',
            'gender' => 'nullable|in:L,P',
            'birth_date' => 'nullable|date|before:today',
            'birth_place' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',
            'blood_type' => 'nullable|string|max:10',
            'id_card_number' => 'nullable|string|max:50|unique:students,id_card_number',
            'passport_number' => 'nullable|string|max:50|unique:students,passport_number',

            // Field akademik (optional)
            'status' => 'nullable|in:active,inactive,graduated,dropped_out,on_leave',
            'enrollment_date' => 'nullable|date',
            'graduation_date' => 'nullable|date|after:enrollment_date',
            'current_semester' => 'nullable|integer|min:1|max:12',
            'current_year' => 'nullable|integer|min:1|max:10',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'class' => 'nullable|string|max:50',
            'batch_year' => 'nullable|string|size:4',
            'is_regular' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',

            // Field orang tua (optional)
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:50',
            'parent_email' => 'nullable|email|max:255',
            'parent_address' => 'nullable|string',

            // Field tambahan (optional)
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'student_number.unique' => 'Student number already exists',
            'email.unique' => 'Email already registered',
            'id_card_number.unique' => 'ID card number already exists',
            'passport_number.unique' => 'Passport number already exists',
            'birth_date.before' => 'Birth date must be before today',
            'graduation_date.after' => 'Graduation date must be after enrollment date',
            'gpa.max' => 'GPA cannot be more than 4.0',
            'program_study_id.exists' => 'Selected program study does not exist',
            'phone.max' => 'Phone number cannot exceed 50 characters',
            'parent_email.email' => 'Parent email must be a valid email address',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'student_number' => 'Student Number',
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
            'enrollment_date' => 'Enrollment Date',
            'graduation_date' => 'Graduation Date',
            'current_semester' => 'Current Semester',
            'current_year' => 'Current Year',
            'gpa' => 'GPA',
            'class' => 'Class',
            'batch_year' => 'Batch Year',
            'is_regular' => 'Regular Student',
            'is_active' => 'Active Status',
            'father_name' => "Father's Name",
            'mother_name' => "Mother's Name",
            'parent_phone' => "Parent's Phone",
            'parent_email' => "Parent's Email",
            'parent_address' => "Parent's Address",
            'photo' => 'Photo',
            'notes' => 'Notes',
            'program_study_id' => 'Program Study',
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
            'current_semester' => $this->current_semester ?? 1,
            'current_year' => $this->current_year ?? 1,
            'gpa' => $this->gpa ?? 0.00,
            'is_regular' => $this->is_regular ?? true,
            'is_active' => $this->is_active ?? true,
            'nationality' => $this->nationality ?? 'Indonesia',
        ]);
    }
}