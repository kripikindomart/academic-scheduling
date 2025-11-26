<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'floor_count',
        'total_rooms',
        'area',
        'year_built',
        'building_type',
        'department',
        'faculty',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'floor_count' => 'integer',
        'total_rooms' => 'integer',
        'area' => 'decimal:2',
        'year_built' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected static function booted()
    {
        static::creating(function ($building) {
            $building->created_by = Auth::id();
        });

        static::updating(function ($building) {
            $building->updated_by = Auth::id();
        });
    }

    /**
     * Get the rooms for the building.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'building', 'code');
    }

    /**
     * Get active rooms for the building.
     */
    public function activeRooms(): HasMany
    {
        return $this->rooms()->where('is_active', true);
    }

    /**
     * Scope a query to only include active buildings.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search buildings by name or code.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by building type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('building_type', $type);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeByDepartment(Builder $query, string $department): Builder
    {
        return $query->where('department', $department);
    }

    /**
     * Scope a query to filter by faculty.
     */
    public function scopeByFaculty(Builder $query, string $faculty): Builder
    {
        return $query->where('faculty', $faculty);
    }

    /**
     * Get room count for the building.
     */
    public function getRoomCountAttribute(): int
    {
        return $this->rooms()->count();
    }

    /**
     * Get active room count for the building.
     */
    public function getActiveRoomCountAttribute(): int
    {
        return $this->activeRooms()->count();
    }

    /**
     * Get capacity sum for all rooms in the building.
     */
    public function getTotalCapacityAttribute(): int
    {
        return $this->rooms()->sum('capacity');
    }

    /**
     * Get building utilization percentage.
     */
    public function getUtilizationPercentageAttribute(): float
    {
        $totalCapacity = $this->rooms()->sum('capacity');
        $currentOccupancy = $this->rooms()->sum('current_occupancy');

        return $totalCapacity > 0 ? round(($currentOccupancy / $totalCapacity) * 100, 2) : 0;
    }

    /**
     * Get creator of the building.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get updater of the building.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get building types for dropdown.
     */
    public static function getBuildingTypes(): array
    {
        return [
            'academic' => 'Gedung Akademik',
            'laboratory' => 'Gedung Laboratorium',
            'administrative' => 'Gedung Administrasi',
            'library' => 'Perpustakaan',
            'workshop' => 'Bengkel',
            'multipurpose' => 'Gedung Serbaguna',
            'sports' => 'Gedung Olahraga',
            'residence' => 'Asrama',
            'other' => 'Lainnya',
        ];
    }

    /**
     * Check if building can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return $this->rooms()->count() === 0;
    }

    /**
     * Check if building has available rooms.
     */
    public function hasAvailableRooms(): bool
    {
        return $this->activeRooms()->where('availability_status', 'available')->exists();
    }

    /**
     * Get building statistics.
     */
    public static function getStatistics(): array
    {
        $totalBuildings = self::count();
        $activeBuildings = self::active()->count();
        $inactiveBuildings = $totalBuildings - $activeBuildings;
        $trashedBuildings = self::onlyTrashed()->count();

        $totalRooms = Room::count();
        $totalCapacity = Room::sum('capacity');

        return [
            'total_buildings' => $totalBuildings,
            'active_buildings' => $activeBuildings,
            'inactive_buildings' => $inactiveBuildings,
            'trashed_buildings' => $trashedBuildings,
            'total_rooms' => $totalRooms,
            'total_capacity' => $totalCapacity,
            'utilization_rate' => $totalCapacity > 0 ? round((Room::sum('current_occupancy') / $totalCapacity) * 100, 2) : 0,
        ];
    }
}