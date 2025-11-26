<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateScheduleRequest extends FormRequest
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
        $schedule = $this->route('schedule');

        return [
            // Basic information
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',

            // Schedule details
            'date' => 'sometimes|date|after_or_equal:today',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'schedule_type' => 'sometimes|in:single,recurring,exam,extra',
            'is_recurring' => 'sometimes|boolean',
            'recurrence_pattern' => 'sometimes|nullable|array',
            'recurrence_pattern.frequency' => 'required_if:is_recurring,true|in:daily,weekly,monthly',
            'recurrence_pattern.interval' => 'required_if:is_recurring,true|integer|min:1',
            'recurrence_pattern.days' => 'sometimes|nullable|array',
            'recurrence_pattern.days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'recurrence_end_date' => 'required_if:is_recurring,true|date|after:date',

            // Related entities
            'course_id' => 'sometimes|exists:courses,id',
            'lecturer_id' => 'sometimes|exists:lecturers,id',
            'room_id' => 'sometimes|exists:rooms,id',
            'program_study_id' => 'sometimes|exists:program_studies,id',
            'class_id' => 'sometimes|nullable|exists:school_classes,id',

            // Academic information
            'semester' => 'sometimes|in:ganjil,genap',
            'academic_year' => 'sometimes|string|max:20',
            'week_number' => 'sometimes|nullable|integer|min:1|max:30',

            // Session details
            'session_type' => 'sometimes|in:lecture,lab,seminar,tutorial,exam,meeting',
            'is_mandatory' => 'sometimes|boolean',
            'is_online' => 'sometimes|boolean',
            'meeting_link' => 'required_if:is_online,true|url|max:500',
            'expected_attendees' => 'sometimes|integer|min:1|max:1000',
            'actual_attendees' => 'sometimes|integer|min:0',

            // Status and workflow
            'status' => 'sometimes|in:draft,submitted,approved,rejected,cancelled,completed',
            'conflict_status' => 'sometimes|in:none,detected,resolved',
            'conflict_details' => 'sometimes|nullable|string',
            'rejection_reason' => 'required_if:status,rejected|string|max:1000',

            // Additional information
            'notes' => 'sometimes|nullable|string|max:2000',
            'materials' => 'sometimes|nullable|array',
            'materials.*' => 'string|max:255',

            // Metadata
            'is_published' => 'sometimes|boolean',
            'is_locked' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.string' => 'Title must be a string',
            'title.max' => 'Title cannot exceed 255 characters',
            'date.after_or_equal' => 'Schedule date cannot be in the past',
            'start_time.date_format' => 'Start time must be in HH:MM format',
            'end_time.date_format' => 'End time must be in HH:MM format',
            'end_time.after' => 'End time must be after start time',
            'course_id.exists' => 'Selected course does not exist',
            'lecturer_id.exists' => 'Selected lecturer does not exist',
            'room_id.exists' => 'Selected room does not exist',
            'program_study_id.exists' => 'Selected program study does not exist',
            'class_id.exists' => 'Selected class does not exist',
            'session_type.in' => 'Invalid session type selected',
            'meeting_link.required_if' => 'Meeting link is required for online sessions',
            'meeting_link.url' => 'Meeting link must be a valid URL',
            'expected_attendees.min' => 'Expected attendees must be at least 1',
            'expected_attendees.max' => 'Expected attendees cannot exceed 1000',
            'actual_attendees.min' => 'Actual attendees cannot be negative',
            'status.in' => 'Invalid status selected',
            'rejection_reason.required_if' => 'Rejection reason is required when rejecting',
            'rejection_reason.max' => 'Rejection reason cannot exceed 1000 characters',
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
            'actual_attendees' => 'Actual Attendees',
            'status' => 'Status',
            'conflict_status' => 'Conflict Status',
            'conflict_details' => 'Conflict Details',
            'rejection_reason' => 'Rejection Reason',
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
            $schedule = $this->route('schedule');

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

            // Validate room availability (excluding current schedule)
            if ($this->date && $this->start_time && $this->end_time && $this->room_id) {
                $conflict = \App\Models\Schedule::where('date', $this->date)
                    ->where('room_id', $this->room_id)
                    ->where('id', '!=', $schedule->id)
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

            // Validate lecturer availability (excluding current schedule)
            if ($this->date && $this->start_time && $this->end_time && $this->lecturer_id) {
                $conflict = \App\Models\Schedule::where('date', $this->date)
                    ->where('lecturer_id', $this->lecturer_id)
                    ->where('id', '!=', $schedule->id)
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

            // Validate status transitions
            if ($this->status && $schedule->status) {
                $allowedTransitions = [
                    'draft' => ['submitted', 'cancelled'],
                    'submitted' => ['draft', 'approved', 'rejected', 'cancelled'],
                    'approved' => ['cancelled', 'completed'],
                    'rejected' => ['draft', 'submitted'],
                    'cancelled' => ['draft', 'submitted'],
                    'completed' => [], // Terminal state
                ];

                if (!in_array($this->status, $allowedTransitions[$schedule->status] ?? [])) {
                    $validator->errors()->add('status', 'Invalid status transition from ' . $schedule->status . ' to ' . $this->status);
                }
            }

            // Validate that locked schedules cannot be edited
            if ($schedule->is_locked && $this->any()) {
                $lockedFields = ['title', 'date', 'start_time', 'end_time', 'room_id', 'lecturer_id', 'course_id'];
                foreach ($lockedFields as $field) {
                    if ($this->has($field)) {
                        $validator->errors()->add($field, 'Cannot modify locked schedule');
                        break;
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Calculate duration in minutes if times are provided
        if ($this->start_time && $this->end_time) {
            $startTime = \Carbon\Carbon::createFromFormat('H:i', $this->start_time);
            $endTime = \Carbon\Carbon::createFromFormat('H:i', $this->end_time);
            $this->merge([
                'duration_minutes' => $startTime->diffInMinutes($endTime),
            ]);
        }

        // Update day of week from date if date is provided
        if ($this->date) {
            $this->merge([
                'day_of_week' => strtolower(\Carbon\Carbon::parse($this->date)->format('l')),
            ]);
        }

        // Update attendance rate if both values are provided
        if ($this->expected_attendees && $this->actual_attendees) {
            $this->merge([
                'attendance_rate' => round(($this->actual_attendees / $this->expected_attendees) * 100, 2),
            ]);
        }
    }
}
