<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic information
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',

            // Schedule details
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'schedule_type' => 'required|in:single,recurring,exam,extra',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|array',
            'recurrence_pattern.frequency' => 'required_if:is_recurring,true|in:daily,weekly,monthly',
            'recurrence_pattern.interval' => 'required_if:is_recurring,true|integer|min:1',
            'recurrence_pattern.days' => 'nullable|array',
            'recurrence_pattern.days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'recurrence_end_date' => 'required_if:is_recurring,true|date|after:date',

            // Related entities
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'room_id' => 'required|exists:rooms,id',
            'program_study_id' => 'required|exists:program_studies,id',
            'class_id' => 'nullable|exists:school_classes,id',

            // Academic information
            'semester' => 'required|in:ganjil,genap',
            'academic_year' => 'required|string|max:20',
            'week_number' => 'nullable|integer|min:1|max:30',

            // Session details
            'session_type' => 'required|in:lecture,lab,seminar,tutorial,exam,meeting',
            'is_mandatory' => 'boolean',
            'is_online' => 'boolean',
            'meeting_link' => 'required_if:is_online,true|url|max:500',
            'expected_attendees' => 'required|integer|min:1|max:1000',

            // Additional information
            'notes' => 'nullable|string|max:2000',
            'materials' => 'nullable|array',
            'materials.*' => 'string|max:255',

            // Optional metadata
            'is_published' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Schedule title is required',
            'date.required' => 'Schedule date is required',
            'date.after_or_equal' => 'Schedule date cannot be in the past',
            'start_time.required' => 'Start time is required',
            'start_time.date_format' => 'Start time must be in HH:MM format',
            'end_time.required' => 'End time is required',
            'end_time.date_format' => 'End time must be in HH:MM format',
            'end_time.after' => 'End time must be after start time',
            'course_id.required' => 'Course selection is required',
            'lecturer_id.required' => 'Lecturer selection is required',
            'room_id.required' => 'Room selection is required',
            'program_study_id.required' => 'Program study selection is required',
            'semester.required' => 'Semester selection is required',
            'academic_year.required' => 'Academic year is required',
            'session_type.required' => 'Session type is required',
            'meeting_link.required_if' => 'Meeting link is required for online sessions',
            'meeting_link.url' => 'Meeting link must be a valid URL',
            'expected_attendees.required' => 'Expected attendees is required',
            'expected_attendees.min' => 'Expected attendees must be at least 1',
            'expected_attendees.max' => 'Expected attendees cannot exceed 1000',
            'recurrence_pattern.frequency.required_if' => 'Recurrence frequency is required for recurring schedules',
            'recurrence_pattern.interval.required_if' => 'Recurrence interval is required for recurring schedules',
            'recurrence_end_date.required_if' => 'Recurrence end date is required for recurring schedules',
            'recurrence_end_date.after' => 'Recurrence end date must be after start date',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'Schedule Title',
            'description' => 'Description',
            'date' => 'Schedule Date',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'schedule_type' => 'Schedule Type',
            'is_recurring' => 'Recurring Schedule',
            'recurrence_pattern' => 'Recurrence Pattern',
            'recurrence_pattern.frequency' => 'Recurrence Frequency',
            'recurrence_pattern.interval' => 'Recurrence Interval',
            'recurrence_pattern.days' => 'Recurrence Days',
            'recurrence_end_date' => 'Recurrence End Date',
            'course_id' => 'Course',
            'lecturer_id' => 'Lecturer',
            'room_id' => 'Room',
            'program_study_id' => 'Program Study',
            'class_id' => 'Class',
            'semester' => 'Semester',
            'academic_year' => 'Academic Year',
            'week_number' => 'Week Number',
            'session_type' => 'Session Type',
            'is_mandatory' => 'Mandatory Session',
            'is_online' => 'Online Session',
            'meeting_link' => 'Meeting Link',
            'expected_attendees' => 'Expected Attendees',
            'notes' => 'Notes',
            'materials' => 'Materials',
            'is_published' => 'Published',
            'is_locked' => 'Locked',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate time duration doesn't exceed reasonable limits
            if ($this->start_time && $this->end_time) {
                $startTime = \Carbon\Carbon::createFromFormat('H:i', $this->start_time);
                $endTime = \Carbon\Carbon::createFromFormat('H:i', $this->end_time);
                $durationInMinutes = $startTime->diffInMinutes($endTime);

                if ($durationInMinutes > 480) { // 8 hours max
                    $validator->errors()->add('end_time', 'Schedule duration cannot exceed 8 hours');
                }

                if ($durationInMinutes < 15) { // 15 minutes min
                    $validator->errors()->add('end_time', 'Schedule duration must be at least 15 minutes');
                }
            }

            // Validate room capacity
            if ($this->room_id && $this->expected_attendees) {
                $room = \App\Models\Room::find($this->room_id);
                if ($room && $this->expected_attendees > $room->capacity) {
                    $validator->errors()->add('expected_attendees', 'Expected attendees cannot exceed room capacity (' . $room->capacity . ')');
                }
            }

            // Validate room availability
            if ($this->date && $this->start_time && $this->end_time && $this->room_id) {
                $conflict = \App\Models\Schedule::where('date', $this->date)
                    ->where('room_id', $this->room_id)
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            $q->where('start_time', '<=', $this->start_time)
                              ->where('end_time', '>', $this->start_time);
                        })
                        ->orWhere(function ($q) {
                            $q->where('start_time', '<', $this->end_time)
                              ->where('end_time', '>=', $this->end_time);
                        });
                    })
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if ($conflict) {
                    $validator->errors()->add('room_id', 'Room is already booked for this time slot');
                }
            }

            // Validate lecturer availability
            if ($this->date && $this->start_time && $this->end_time && $this->lecturer_id) {
                $conflict = \App\Models\Schedule::where('date', $this->date)
                    ->where('lecturer_id', $this->lecturer_id)
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            $q->where('start_time', '<=', $this->start_time)
                              ->where('end_time', '>', $this->start_time);
                        })
                        ->orWhere(function ($q) {
                            $q->where('start_time', '<', $this->end_time)
                              ->where('end_time', '>=', $this->end_time);
                        });
                    })
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if ($conflict) {
                    $validator->errors()->add('lecturer_id', 'Lecturer is already scheduled for this time slot');
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Calculate duration in minutes
        if ($this->start_time && $this->end_time) {
            $startTime = \Carbon\Carbon::createFromFormat('H:i', $this->start_time);
            $endTime = \Carbon\Carbon::createFromFormat('H:i', $this->end_time);
            $this->merge([
                'duration_minutes' => $startTime->diffInMinutes($endTime),
            ]);
        }

        // Set day of week from date
        if ($this->date) {
            $this->merge([
                'day_of_week' => strtolower(\Carbon\Carbon::parse($this->date)->format('l')),
            ]);
        }
    }
}
