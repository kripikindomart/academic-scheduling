<?php

namespace App\Http\Requests\ConflictDetection;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkResolveRequest extends FormRequest
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
            'conflict_ids' => 'required|array|min:1|max:100',
            'conflict_ids.*' => 'required|exists:conflict_detections,id',
            'resolution_data' => 'required|array',
            'resolution_data.resolution_strategy' => [
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
            'resolution_data.resolution_notes' => 'nullable|string|max:1000',
            'resolution_data.is_resolution_permanent' => 'nullable|boolean',
            'resolution_data.apply_to_all' => 'nullable|boolean',
            'resolution_data.force_resolution' => 'nullable|boolean',
            'resolution_data.new_room_id' => 'nullable|exists:rooms,id',
            'resolution_data.new_lecturer_id' => 'nullable|exists:lecturers,id',
            'resolution_data.new_class_id' => 'nullable|exists:school_classes,id',
            'resolution_data.new_start_time' => 'nullable|date_format:H:i',
            'resolution_data.new_end_time' => 'nullable|date_format:H:i|after:resolution_data.new_start_time',
            'resolution_data.new_date' => 'nullable|date|after_or_equal:today',
            'resolution_data.override_reason' => 'required_if:resolution_data.resolution_strategy,override|string|max:1000',
            'resolution_data.notification_recipients' => 'nullable|array',
            'resolution_data.notification_recipients.*' => 'exists:users,id',
            'resolution_data.skip_notifications' => 'nullable|boolean',
            'resolution_data.batch_name' => 'nullable|string|max:255',
            'resolution_data.priority' => 'nullable|integer|min:1|max:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'conflict_ids.required' => 'At least one conflict ID is required',
            'conflict_ids.array' => 'Conflict IDs must be provided as an array',
            'conflict_ids.min' => 'At least one conflict must be selected',
            'conflict_ids.max' => 'Cannot process more than 100 conflicts at once',
            'conflict_ids.*.exists' => 'One or more selected conflicts do not exist',
            'resolution_data.required' => 'Resolution data is required',
            'resolution_data.resolution_strategy.required' => 'Resolution strategy is required',
            'resolution_data.resolution_strategy.in' => 'Invalid resolution strategy',
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
        $booleanFields = [
            'resolution_data.is_resolution_permanent',
            'resolution_data.apply_to_all',
            'resolution_data.force_resolution',
            'resolution_data.skip_notifications'
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN)
                ]);
            }
        }

        // Sanitize text inputs
        $textFields = [
            'resolution_data.resolution_notes',
            'resolution_data.override_reason',
            'resolution_data.batch_name'
        ];

        foreach ($textFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => trim(strip_tags($this->input($field)))
                ]);
            }
        }

        // Limit conflict IDs to prevent performance issues
        $conflictIds = $this->input('conflict_ids', []);
        if (count($conflictIds) > 100) {
            $this->merge([
                'conflict_ids' => array_slice($conflictIds, 0, 100)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $strategy = $this->input('resolution_data.resolution_strategy');
            $resolutionData = $this->input('resolution_data', []);
            $conflictIds = $this->input('conflict_ids', []);

            // Validate conflict count
            if (empty($conflictIds)) {
                $validator->errors()->add('conflict_ids', 'No conflicts selected for resolution');
            }

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

            // Validate priority if provided
            if (isset($resolutionData['priority'])) {
                $priority = (int) $resolutionData['priority'];
                if ($priority < 1 || $priority > 10) {
                    $validator->errors()->add('resolution_data.priority', 'Priority must be between 1 and 10');
                }
            }

            // Validate batch name if provided
            if (isset($resolutionData['batch_name'])) {
                $batchName = $resolutionData['batch_name'];
                if (strlen($batchName) > 255) {
                    $validator->errors()->add('resolution_data.batch_name', 'Batch name cannot exceed 255 characters');
                }
            }
        });
    }

    /**
     * Get the validated data with proper defaults.
     */
    public function getValidatedWithDefaults(): array
    {
        $validated = $this->validated();

        // Set default values
        $resolutionData = $validated['resolution_data'];
        $resolutionData['is_resolution_permanent'] = $resolutionData['is_resolution_permanent'] ?? false;
        $resolutionData['apply_to_all'] = $resolutionData['apply_to_all'] ?? false;
        $resolutionData['force_resolution'] = $resolutionData['force_resolution'] ?? false;
        $resolutionData['skip_notifications'] = $resolutionData['skip_notifications'] ?? false;
        $resolutionData['notification_recipients'] = $resolutionData['notification_recipients'] ?? [];
        $validated['resolution_data'] = $resolutionData;

        return $validated;
    }

    /**
     * Get batch resolution metadata.
     */
    public function getBatchMetadata(): array
    {
        $resolutionData = $this->input('resolution_data', []);
        $conflictIds = $this->input('conflict_ids', []);

        return [
            'batch_name' => $resolutionData['batch_name'] ?? 'Batch Resolution ' . date('Y-m-d H:i:s'),
            'conflict_count' => count($conflictIds),
            'strategy' => $resolutionData['resolution_strategy'] ?? 'manual_resolution',
            'priority' => $resolutionData['priority'] ?? 5,
            'force_resolution' => $resolutionData['force_resolution'] ?? false,
            'apply_to_all' => $resolutionData['apply_to_all'] ?? false,
            'skip_notifications' => $resolutionData['skip_notifications'] ?? false,
        ];
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
        $textFields = ['override_reason', 'resolution_notes', 'batch_name'];
        foreach ($textFields as $field) {
            if (isset($resolutionData[$field])) {
                $resolutionData[$field] = trim(strip_tags($resolutionData[$field]));
            }
        }

        return $resolutionData;
    }

    /**
     * Check if this is a high-priority batch resolution.
     */
    public function isHighPriority(): bool
    {
        $resolutionData = $this->input('resolution_data', []);
        $priority = $resolutionData['priority'] ?? 5;
        $forceResolution = $resolutionData['force_resolution'] ?? false;
        $conflictCount = count($this->input('conflict_ids', []));

        return $priority >= 8 || $forceResolution || $conflictCount > 20;
    }
}