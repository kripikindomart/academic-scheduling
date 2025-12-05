<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_code',
        'name',
        'building',
        'floor',
        'room_type',
        'capacity',
        'current_occupancy',
        'area',
        'department',
        'faculty',
        'location',
        'description',
        'facilities',
        'equipment',
        'availability_status',
        'is_active',
        'accessibility_features',
        'maintenance_status',
        'last_maintenance_date',
        'next_maintenance_date',
        'responsible_person',
        'contact_phone',
        'rules_and_regulations',
        'usage_policies',
        'schedule_rules',
        'photo',
        'qr_code',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_occupancy' => 'integer',
        'area' => 'decimal:2',
        'is_active' => 'boolean',
        'facilities' => 'array',
        'equipment' => 'array',
        'accessibility_features' => 'array',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'rules_and_regulations' => 'array',
        'usage_policies' => 'array',
        'schedule_rules' => 'array',
    ];

    protected $appends = ['status'];

    protected $dates = [
        'last_maintenance_date',
        'next_maintenance_date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getStatusAttribute()
    {
        return $this->is_active ? 'active' : 'inactive';
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function activeSchedules()
    {
        return $this->hasMany(Schedule::class)
            ->where('status', 'active')
            ->whereDate('end_time', '>=', now());
    }

    public function todaySchedules()
    {
        return $this->hasMany(Schedule::class)
            ->whereDate('start_time', '<=', now())
            ->whereDate('end_time', '>=', now())
            ->orderBy('start_time');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available');
    }

    public function scopeByBuilding($query, $building)
    {
        return $query->where('building', $building);
    }

    public function scopeByFloor($query, $floor)
    {
        return $query->where('floor', $floor);
    }

    public function scopeByRoomType($query, $roomType)
    {
        return $query->where('room_type', $roomType);
    }

    public function scopeByCapacity($query, $minCapacity, $maxCapacity = null)
    {
        $query->where('capacity', '>=', $minCapacity);
        if ($maxCapacity) {
            $query->where('capacity', '<=', $maxCapacity);
        }
        return $query;
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByFaculty($query, $faculty)
    {
        return $query->where('faculty', $faculty);
    }

    public function scopeByMaintenanceStatus($query, $status)
    {
        return $query->where('maintenance_status', $status);
    }

    public function getOccupancyRateAttribute()
    {
        return $this->capacity > 0 ? round(($this->current_occupancy / $this->capacity) * 100, 1) : 0;
    }

    public function getAvailableCapacityAttribute()
    {
        return max(0, $this->capacity - $this->current_occupancy);
    }

    public function getFullAddressAttribute()
    {
        return "Lantai {$this->floor}, {$this->building}, {$this->location}";
    }

    public function getRoomIdentifierAttribute()
    {
        return "{$this->building}-{$this->room_code}";
    }

    public function getStatusColorAttribute()
    {
        switch ($this->availability_status) {
            case 'available':
                return 'bg-green-100 text-green-800';
            case 'occupied':
                return 'bg-red-100 text-red-800';
            case 'maintenance':
                return 'bg-yellow-100 text-yellow-800';
            case 'reserved':
                return 'bg-blue-100 text-blue-800';
            case 'unavailable':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->availability_status) {
            case 'available':
                return 'Tersedia';
            case 'occupied':
                return 'Terpakai';
            case 'maintenance':
                return 'Perbaikan';
            case 'reserved':
                return 'Disewa';
            case 'unavailable':
                return 'Tidak Tersedia';
            default:
                return $this->availability_status;
        }
    }

    public function getMaintenanceStatusColorAttribute()
    {
        switch ($this->maintenance_status) {
            case 'good':
                return 'bg-green-100 text-green-800';
            case 'needs_attention':
                return 'bg-yellow-100 text-yellow-800';
            case 'under_maintenance':
                return 'bg-orange-100 text-orange-800';
            case 'critical':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function getMaintenanceStatusLabelAttribute()
    {
        switch ($this->maintenance_status) {
            case 'good':
                return 'Baik';
            case 'needs_attention':
                return 'Perlu Perhatian';
            case 'under_maintenance':
                return 'Dalam Perbaikan';
            case 'critical':
                return 'Kritis';
            default:
                return $this->maintenance_status;
        }
    }

    public function getFacilitiesListAttribute()
    {
        return is_array($this->facilities) ? implode(', ', $this->facilities) : $this->facilities;
    }

    public function getEquipmentListAttribute()
    {
        return is_array($this->equipment) ? implode(', ', $this->equipment) : $this->equipment;
    }

    public function isAvailableForSchedule($date, $startTime, $endTime)
    {
        // Check if room is under maintenance
        if ($this->maintenance_status === 'under_maintenance' ||
            ($this->next_maintenance_date && now()->gt($this->next_maintenance_date))) {
            return false;
        }

        // Check if room is available
        if ($this->availability_status !== 'available') {
            return false;
        }

        // Check for schedule conflicts
        $conflictSchedule = $this->schedules()
            ->where('date', $date)
            ->where('status', 'active')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($subQuery) use ($startTime, $endTime) {
                        $subQuery->where('start_time', '<', $startTime)
                            ->where('end_time', '>', $endTime);
                    });
            })
            ->first();

        return !$conflictSchedule;
    }

    public function canAccommodate($requiredCapacity)
    {
        return $this->available_capacity >= $requiredCapacity && $this->is_active;
    }

    public function getTodayScheduleAttribute()
    {
        return $this->todaySchedules()
            ->with(['course', 'lecturer'])
            ->get();
    }

    public function getWeeklyScheduleAttribute()
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return $this->schedules()
            ->whereDate('date', '>=', $startOfWeek)
            ->whereDate('date', '<=', $endOfWeek)
            ->where('status', 'active')
            ->with(['course', 'lecturer'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    public function getUtilizationRateAttribute($period = 'month')
    {
        $startDate = now()->subMonth();
        $endDate = now();

        if ($period === 'week') {
            $startDate = now()->subWeek();
        } elseif ($period === 'year') {
            $startDate = now()->subYear();
        }

        $totalPossibleHours = $this->calculateTotalAvailableHours($startDate, $endDate);
        $scheduledHours = $this->schedules()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->where('status', 'active')
            ->selectRaw('SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours')
            ->value('total_hours') ?? 0;

        return $totalPossibleHours > 0 ? round(($scheduledHours / $totalPossibleHours) * 100, 1) : 0;
    }

    public function getUpcomingSchedulesAttribute($limit = 5)
    {
        return $this->schedules()
            ->whereDate('date', '>=', now())
            ->where('status', 'active')
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit($limit)
            ->with(['course', 'lecturer'])
            ->get();
    }

    public function hasFacility($facility)
    {
        if (!is_array($this->facilities)) {
            return false;
        }

        return in_array(strtolower($facility), array_map('strtolower', $this->facilities));
    }

    public function hasEquipment($equipment)
    {
        if (!is_array($this->equipment)) {
            return false;
        }

        return in_array(strtolower($equipment), array_map('strtolower', $this->equipment));
    }

    public function isOverdueForMaintenance()
    {
        return $this->next_maintenance_date && now()->gt($this->next_maintenance_date);
    }

    public function getMaintenanceAlertAttribute()
    {
        if (!$this->next_maintenance_date) {
            return null;
        }

        $daysUntil = now()->diffInDays($this->next_maintenance_date, false);

        if ($daysUntil < 0) {
            return ['type' => 'danger', 'message' => 'Overdue for maintenance'];
        } elseif ($daysUntil <= 7) {
            return ['type' => 'warning', 'message' => "Maintenance due in {$daysUntil} days"];
        } elseif ($daysUntil <= 30) {
            return ['type' => 'info', 'message' => "Maintenance due in {$daysUntil} days"];
        }

        return null;
    }

    public function getPreferredForCourseType($courseType)
    {
        switch ($courseType) {
            case 'lecture':
                return $this->room_type === 'classroom' && $this->capacity >= 30;
            case 'lab':
                return $this->room_type === 'laboratory' && $this->hasFacility('computer');
            case 'seminar':
                return $this->room_type === 'seminar_room' && $this->capacity >= 20;
            case 'workshop':
                return $this->room_type === 'workshop' && $this->hasFacility('tools');
            default:
                return $this->room_type === 'classroom';
        }
    }

    private function calculateTotalAvailableHours($startDate, $endDate)
    {
        $days = $startDate->diffInDays($endDate);

        // Calculate available hours per day (e.g., 8 AM to 8 PM = 12 hours)
        $hoursPerDay = 12;

        // Exclude weekends
        $weekdays = 0;
        for ($i = 0; $i < $days; $i++) {
            $currentDay = $startDate->copy()->addDays($i);
            if ($currentDay->isWeekday()) {
                $weekdays++;
            }
        }

        return $weekdays * $hoursPerDay;
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'occupancy_rate' => $this->occupancy_rate,
            'available_capacity' => $this->available_capacity,
            'full_address' => $this->full_address,
            'room_identifier' => $this->room_identifier,
            'utilization_rate' => $this->utilization_rate,
            'maintenance_alert' => $this->maintenance_alert,
        ]);
    }
}
