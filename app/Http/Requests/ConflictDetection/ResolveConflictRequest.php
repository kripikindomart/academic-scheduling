<?php

namespace App\Http\Requests\ConflictDetection;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResolveConflictRequest extends FormRequest
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
            'resolution_strategy' => [
                'required',
                Rule::in([
                    'none',
                    'reschedule_primary',
                    'reschedule_conflicting',
                    'change_room',
                    'change_lecturer',
                    'change_class',
                    'adjust_time',
                    'override',
                    'manual_resolution'
                ])
            ],
            'resolution_notes' => 'nullable|string|max:1000',
            'is_resolution_permanent' => 'nullable|boolean',
            'resolution_data' => 'nullable|array',
            'resolution_data.new_room_id' => 'nullable|exists:rooms,id',
            'resolution_data.new_lecturer_id' => 'nullable|exists:lecturers,id',
            'resolution_data.new_class_id' => 'nullable|exists:school_classes,id',
            'resolution_data.new_start_time' => 'nullable|date_format:H:i',
            'resolution_data.new_end_time' => 'nullable|date_format:H:i|after:resolution_data.new_start_time',
            'resolution_data.new_date' => 'nullable|date|after_or_equal:today',
            'resolution_data.override_reason' => 'required_if:resolution_strategy,override|string|max:1000',
            'resolution_data.notification_recipients' => 'nullable|array',
            'resolution_data.notification_recipients.*' => 'exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'resolution_strategy.required' => 'Resolution strategy is required',
            'resolution_strategy.in' => 'Invalid resolution strategy',
            'resolution_data.new_room_id.exists' => 'Selected room does not exist',
            'resolution_data.new_lecturer_id.exists' => 'Selected lecturer does not exist',
            'resolution_data.new_class_id.exists' => 'Selected class does not exist',
            'resolution_data.new_start_time.date_format' => 'Start time must be in HH:MM format',
            'resolution_data.new_end_time.date_format' => 'End time must be in HH:MM format',
            'resolution_data.new_end_time.after' => 'End time must be after start time',
            'resolution_data.new_date.after_or_equal' => 'New date must be today or in the future',
            'resolution_data.override_reason.required_if' => 'Override reason is required when using override strategy',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert string boolean values to actual booleans
        if ($this->has('is_resolution_permanent')) {
            $this->merge([
                'is_resolution_permanent' => filter_var($this->input('is_resolution_permanent'), FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        // Sanitize text inputs
        $textFields = ['resolution_notes', 'resolution_data.override_reason'];
        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => trim(strip_tags($this->input($field)))
                ]);
            }
        }
    }

    /**
     * Get the validated data with proper defaults.
     */
    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        // Set default values
        $validated['is_resolution_permanent'] = $validated['is_resolution_permanent'] ?? false;
        $validated['resolution_data'] = $validated['resolution_data'] ?? [];

        return $validated;
    }

    /**
     * Validate resolution strategy compatibility.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $strategy = $this->input('resolution_strategy');
            $resolutionData = $this->input('resolution_data', []);

            // Validate required fields based on resolution strategy
            switch ($strategy) {
                case 'change_room':
                    if (empty($resolutionData['new_room_id'])) {
                        $validator->errors()->add('resolution_data.new_room_id', 'New room ID is required for room change strategy');
                    }
                    break;

                case 'change_lecturer':
                    if (empty($resolutionData['new_lecturer_id'])) {
                        $validator->errors()->add('resolution_data.new_lecturer_id', 'New lecturer ID is required for lecturer change strategy');
                    }
                    break;

                case 'change_class':
                    if (empty($resolutionData['new_class_id'])) {
                        $validator->errors()->add('resolution_data.new_class_id', 'New class ID is required for class change strategy');
                    }
                    break;

                case 'adjust_time':
                    if (empty($resolutionData['new_start_time']) || empty($resolutionData['new_end_time'])) {
                        $validator->errors()->add('resolution_data.time', 'Both start and end times are required for time adjustment strategy');
                    }
                    break;

                case 'reschedule_primary':
                case 'reschedule_conflicting':
                    if (empty($resolutionData['new_date'])) {
                        $validator->errors()->add('resolution_data.new_date', 'New date is required for rescheduling strategies');
                    }
                    break;
            }

            // Validate notification recipients if provided
            if (isset($resolutionData['notification_recipients'])) {
                if (!is_array($resolutionData['notification_recipients'])) {
                    $validator->errors()->add('resolution_data.notification_recipients', 'Notification recipients must be an array');
                }
            }
        });
    }

    /**
     * Get the sanitized resolution data.
     */
    public function getSanitizedResolutionData(): array
    {
        $resolutionData = $this->input('resolution_data', []);

        // Remove any potentially harmful data
        unset($resolutionData['csrf_token'], $resolutionData['_token']);

        // Sanitize text fields
        $textFields = ['override_reason', 'notes'];
        foreach ($textFields as $field) {
            if (isset($resolutionData[$field])) {
                $resolutionData[$field] = trim(strip_tags($resolutionData[$field]));
            }
        }

        return $resolutionData;
    }
}