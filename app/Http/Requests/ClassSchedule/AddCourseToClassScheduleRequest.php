<?php

namespace App\Http\Requests\ClassSchedule;

use Illuminate\Foundation\Http\FormRequest;

class AddCourseToClassScheduleRequest extends FormRequest
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
            'course_id' => 'required|exists:courses,id',
            'lecturer_ids' => 'required|array|min:1',
            'lecturer_ids.*' => 'required|exists:lecturers,id',
            'room_ids' => 'sometimes|array|min:0',
            'room_ids.*' => 'nullable|exists:rooms,id',
            'day' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'sessions_per_meeting' => 'sometimes|integer|min:1|max:2',
            'total_meetings' => 'sometimes|integer|min:1|max:16',
            'meeting_type' => 'sometimes|string|in:lecture,lab,seminar,workshop,online,offline',
            'is_online' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required' => 'Mata kuliah wajib dipilih',
            'course_id.exists' => 'Mata kuliah tidak valid',
            'lecturer_ids.required' => 'Setidaknya satu dosen wajib dipilih',
            'lecturer_ids.array' => 'Format dosen tidak valid',
            'lecturer_ids.min' => 'Setidaknya satu dosen wajib dipilih',
            'lecturer_ids.*.required' => 'Dosen wajib dipilih',
            'lecturer_ids.*.exists' => 'Dosen tidak valid',
            'room_ids.array' => 'Format ruangan tidak valid',
            'room_ids.*.exists' => 'Ruang tidak valid',
            'day.required' => 'Hari wajib dipilih',
            'day.in' => 'Hari tidak valid',
            'start_time.required' => 'Jam mulai wajib diisi',
            'start_time.date_format' => 'Format jam mulai tidak valid (HH:MM)',
            'end_time.required' => 'Jam selesai wajib diisi',
            'end_time.date_format' => 'Format jam selesai tidak valid (HH:MM)',
            'end_time.after' => 'Jam selesai harus setelah jam mulai',
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'start_date.date' => 'Format tanggal mulai tidak valid',
            'end_date.required' => 'Tanggal selesai wajib diisi',
            'end_date.date' => 'Format tanggal selesai tidak valid',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'sessions_per_meeting.integer' => 'Jumlah sesi per pertemuan harus berupa angka',
            'sessions_per_meeting.min' => 'Jumlah sesi per pertemuan minimal 1',
            'sessions_per_meeting.max' => 'Jumlah sesi per pertemuan maksimal 2',
            'total_meetings.integer' => 'Jumlah pertemuan total harus berupa angka',
            'total_meetings.min' => 'Jumlah pertemuan total minimal 1',
            'total_meetings.max' => 'Jumlah pertemuan total maksimal 16',
            'meeting_type.in' => 'Tipe pertemuan tidak valid (pilih: lecture, lab, seminar, workshop, online, offline)',
            'is_online.boolean' => 'Status online harus berupa boolean',
            'notes.string' => 'Notes harus berupa string',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        // Log detailed validation errors for debugging
        \Log::error('=== ADD COURSE VALIDATION ERROR ===');
        \Log::error('Request payload:', request()->all());
        \Log::error('Validation errors:', $validator->errors()->toArray());

        // Create a new ValidationException with proper format
        throw new \Illuminate\Validation\ValidationException(
            $validator,
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
