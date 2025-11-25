<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
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
            'room_code' => 'nullable|string|max:20|unique:rooms,room_code',
            'name' => 'required|string|max:255',
            'building' => 'required|string|max:100',
            'floor' => 'required|integer|min:1',
            'room_type' => 'required|in:classroom,laboratory,seminar_room,auditorium,workshop,library,office,meeting_room,multipurpose',
            'capacity' => 'required|integer|min:1|max:1000',
            'current_occupancy' => 'nullable|integer|min:0|max:capacity',
            'area' => 'nullable|decimal:8,2|min:1',
            'department' => 'nullable|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'description' => 'nullable|string|max:1000',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:255',
            'equipment' => 'nullable|array',
            'equipment.*' => 'string|max:255',
            'availability_status' => 'nullable|in:available,occupied,maintenance,reserved,unavailable|default:available',
            'is_active' => 'nullable|boolean|default:true',
            'accessibility_features' => 'nullable|array',
            'accessibility_features.*' => 'string|max:255',
            'maintenance_status' => 'nullable|in:good,needs_attention,under_maintenance,critical|default:good',
            'last_maintenance_date' => 'nullable|date|before_or_equal:today',
            'next_maintenance_date' => 'nullable|date|after:today',
            'responsible_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'rules_and_regulations' => 'nullable|array',
            'rules_and_regulations.*' => 'string|max:500',
            'usage_policies' => 'nullable|array',
            'usage_policies.*' => 'string|max:500',
            'schedule_rules' => 'nullable|array',
            'schedule_rules.*' => 'string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'qr_code' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'room_code.unique' => 'Room code already exists',
            'name.required' => 'Room name is required',
            'building.required' => 'Building is required',
            'floor.required' => 'Floor number is required',
            'floor.min' => 'Floor must be at least 1',
            'room_type.required' => 'Room type is required',
            'room_type.in' => 'Invalid room type selected',
            'capacity.required' => 'Capacity is required',
            'capacity.min' => 'Capacity must be at least 1',
            'capacity.max' => 'Capacity cannot exceed 1000',
            'current_occupancy.max' => 'Current occupancy cannot exceed room capacity',
            'area.min' => 'Area must be at least 1 square meter',
            'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future',
            'next_maintenance_date.after' => 'Next maintenance date must be in the future',
            'contact_phone.max' => 'Contact phone number cannot exceed 50 characters',
            'notes.max' => 'Notes cannot exceed 1000 characters',
            'photo.image' => 'Photo must be an image file',
            'photo.mimes' => 'Photo must be a JPEG, PNG, or JPG file',
            'photo.max' => 'Photo size cannot exceed 2MB',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'room_code' => 'Room Code',
            'name' => 'Room Name',
            'building' => 'Building',
            'floor' => 'Floor',
            'room_type' => 'Room Type',
            'capacity' => 'Capacity',
            'current_occupancy' => 'Current Occupancy',
            'area' => 'Area (m²)',
            'department' => 'Department',
            'faculty' => 'Faculty',
            'location' => 'Location',
            'description' => 'Description',
            'facilities' => 'Facilities',
            'equipment' => 'Equipment',
            'availability_status' => 'Availability Status',
            'is_active' => 'Active Status',
            'accessibility_features' => 'Accessibility Features',
            'maintenance_status' => 'Maintenance Status',
            'last_maintenance_date' => 'Last Maintenance Date',
            'next_maintenance_date' => 'Next Maintenance Date',
            'responsible_person' => 'Responsible Person',
            'contact_phone' => 'Contact Phone',
            'rules_and_regulations' => 'Rules and Regulations',
            'usage_policies' => 'Usage Policies',
            'schedule_rules' => 'Schedule Rules',
            'photo' => 'Photo',
            'qr_code' => 'QR Code',
            'notes' => 'Notes',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'availability_status' => $this->availability_status ?? 'available',
            'is_active' => $this->is_active ?? true,
            'maintenance_status' => $this->maintenance_status ?? 'good',
            'current_occupancy' => $this->current_occupancy ?? 0,
        ]);

        // Validate room code format
        if ($this->room_code && !preg_match('/^[A-Z]{2,4}\d{2,4}$/', $this->room_code)) {
            $this->merge([
                'room_code' => strtoupper($this->room_code)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate building and floor combination
            if ($this->building && $this->floor) {
                $existingRoom = \App\Models\Room::where('building', $this->building)
                    ->where('floor', $this->floor)
                    ->where('name', $this->name)
                    ->first();

                if ($existingRoom) {
                    $validator->errors()->add('name', 'A room with this name already exists on the same building and floor.');
                }
            }

            // Validate capacity requirements based on room type
            if ($this->room_type && $this->capacity) {
                $minCapacities = [
                    'classroom' => 20,
                    'laboratory' => 15,
                    'seminar_room' => 10,
                    'auditorium' => 50,
                    'workshop' => 10,
                    'library' => 30,
                    'office' => 1,
                    'meeting_room' => 5,
                    'multipurpose' => 25,
                ];

                $minCapacity = $minCapacities[$this->room_type] ?? 10;
                if ($this->capacity < $minCapacity) {
                    $validator->errors()->add('capacity', "Minimum capacity for {$this->room_type} is {$minCapacity}.");
                }
            }

            // Validate area requirements
            if ($this->capacity && $this->area) {
                $minAreaPerPerson = 2; // 2 square meters per person minimum
                $minRequiredArea = $this->capacity * $minAreaPerPerson;

                if ($this->area < $minRequiredArea) {
                    $validator->errors()->add('area', "Minimum area required for {$this->capacity} people is {$minRequiredArea} m².");
                }
            }

            // Validate maintenance dates
            if ($this->last_maintenance_date && $this->next_maintenance_date) {
                if ($this->next_maintenance_date <= $this->last_maintenance_date) {
                    $validator->errors()->add('next_maintenance_date', 'Next maintenance date must be after last maintenance date.');
                }
            }

            // Validate contact phone format
            if ($this->contact_phone) {
                $phoneRegex = '/^[\+]?[0-9\s\-\(\)]{10,20}$/';
                if (!preg_match($phoneRegex, $this->contact_phone)) {
                    $validator->errors()->add('contact_phone', 'Invalid phone number format.');
                }
            }
        });
    }
}