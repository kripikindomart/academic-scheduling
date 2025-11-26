<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RoomsExport;
use App\Imports\RoomsImport;

class RoomService
{
    /**
     * Get paginated list of rooms with filtering.
     */
    public function getRooms(array $filters = [], int $perPage = 15, string $sortBy = 'name', string $sortDirection = 'asc'): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Room::with(['creator', 'updater']);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('room_code', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['building'])) {
            $query->where('building', $filters['building']);
        }

        if (!empty($filters['floor'])) {
            $query->where('floor', $filters['floor']);
        }

        if (!empty($filters['room_type'])) {
            $query->where('room_type', $filters['room_type']);
        }

        if (!empty($filters['min_capacity'])) {
            $query->where('capacity', '>=', $filters['min_capacity']);
        }

        if (!empty($filters['max_capacity'])) {
            $query->where('capacity', '<=', $filters['max_capacity']);
        }

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        if (!empty($filters['faculty'])) {
            $query->where('faculty', $filters['faculty']);
        }

        if (!empty($filters['availability_status'])) {
            $query->where('availability_status', $filters['availability_status']);
        }

        if (!empty($filters['maintenance_status'])) {
            $query->where('maintenance_status', $filters['maintenance_status']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Filter by facilities
        if (!empty($filters['has_facilities'])) {
            $facilities = $filters['has_facilities'];
            if (is_array($facilities)) {
                foreach ($facilities as $facility) {
                    $query->whereJsonContains('facilities', $facility);
                }
            }
        }

        // Apply sorting
        $allowedSorts = ['name', 'room_code', 'building', 'floor', 'room_type', 'capacity', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'name';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Check for potential duplicates before creating.
     */
    public function checkForDuplicates(array $roomData): array
    {
        $potentialDuplicates = [];

        // Check for duplicate room_code (including in trash)
        $existingByCode = Room::withTrashed()
            ->where('room_code', $roomData['room_code'])
            ->first();

        if ($existingByCode) {
            $potentialDuplicates[] = [
                'type' => 'room_code',
                'value' => $roomData['room_code'],
                'existing' => $existingByCode,
                'message' => $existingByCode->deleted_at
                    ? 'Kode ruangan ini sudah ada di trash'
                    : 'Kode ruangan ini sudah digunakan'
            ];
        }

        // Check for duplicate name with same building (including in trash)
        if (isset($roomData['building']) && isset($roomData['name'])) {
            $existingByName = Room::withTrashed()
                ->where('name', $roomData['name'])
                ->where('building', $roomData['building'])
                ->first();

            if ($existingByName) {
                $potentialDuplicates[] = [
                    'type' => 'name_building',
                    'value' => $roomData['name'] . ' di ' . $roomData['building'],
                    'existing' => $existingByName,
                    'message' => $existingByName->deleted_at
                        ? 'Nama ruangan ini sudah ada di trash untuk gedung ini'
                        : 'Nama ruangan ini sudah digunakan di gedung ini'
                ];
            }
        }

        return [
            'has_duplicates' => !empty($potentialDuplicates),
            'duplicates' => $potentialDuplicates
        ];
    }

    /**
     * Create a new room.
     */
    public function createRoom(array $data): Room
    {
        return DB::transaction(function () use ($data) {
            // Generate room code if not provided
            if (empty($data['room_code'])) {
                $building = $data['building'];
                $roomType = $data['room_type'] ?? 'classroom';
                $sequence = Room::where('building', $building)->count() + 1;

                // Use SP (Study Program) code - default to SPS
                $spCode = 'SPS';

                // Create building code from building name
                $words = explode(' ', $building);
                $buildingMainName = '';
                $skipNext = false;

                foreach ($words as $word) {
                    $upperWord = strtoupper($word);
                    if ($skipNext) {
                        $skipNext = false;
                        continue;
                    }
                    // Skip faculty indicators like "Fakultas", "Sekolah" etc.
                    if (in_array($upperWord, ['FAKULTAS', 'FK', 'SEKOLAH', 'SCHOOL'])) {
                        $skipNext = true; // Skip the next word (faculty name)
                        continue;
                    }
                    if ($buildingMainName) {
                        $buildingMainName .= ' ';
                    }
                    $buildingMainName .= $word;
                }

                // Get first 3 chars of building main name
                $buildingCode = '';
                if ($buildingMainName) {
                    $buildingCode = strtoupper(substr($buildingMainName, 0, 3));
                } else {
                    $buildingCode = 'GEN';
                }

                // Room type code
                $roomTypeCode = '';
                switch ($roomType) {
                    case 'classroom':
                        $roomTypeCode = 'KLS';
                        break;
                    case 'laboratory':
                        $roomTypeCode = 'LAB';
                        break;
                    case 'seminar_room':
                        $roomTypeCode = 'SMR';
                        break;
                    case 'auditorium':
                        $roomTypeCode = 'AUD';
                        break;
                    case 'workshop':
                        $roomTypeCode = 'WSH';
                        break;
                    case 'library':
                        $roomTypeCode = 'LIB';
                        break;
                    case 'office':
                        $roomTypeCode = 'OFC';
                        break;
                    case 'meeting_room':
                        $roomTypeCode = 'MTR';
                        break;
                    case 'multipurpose':
                        $roomTypeCode = 'MPL';
                        break;
                    default:
                        $roomTypeCode = 'RUM';
                }

                $data['room_code'] = $spCode . '-' . $buildingCode . '-' . $roomTypeCode . str_pad($sequence, 2, '0', STR_PAD_LEFT);
            }

            // Set current user as creator
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            // Handle JSON fields
            if (isset($data['facilities']) && is_array($data['facilities'])) {
                $data['facilities'] = json_encode($data['facilities']);
            }

            if (isset($data['equipment']) && is_array($data['equipment'])) {
                $data['equipment'] = json_encode($data['equipment']);
            }

            if (isset($data['accessibility_features']) && is_array($data['accessibility_features'])) {
                $data['accessibility_features'] = json_encode($data['accessibility_features']);
            }

            if (isset($data['rules_and_regulations']) && is_array($data['rules_and_regulations'])) {
                $data['rules_and_regulations'] = json_encode($data['rules_and_regulations']);
            }

            if (isset($data['usage_policies']) && is_array($data['usage_policies'])) {
                $data['usage_policies'] = json_encode($data['usage_policies']);
            }

            if (isset($data['schedule_rules']) && is_array($data['schedule_rules'])) {
                $data['schedule_rules'] = json_encode($data['schedule_rules']);
            }

            $room = Room::create($data);

            Log::info('Room created', [
                'room_id' => $room->id,
                'room_code' => $room->room_code,
                'created_by' => auth()->id()
            ]);

            return $room->load(['creator']);
        });
    }

    /**
     * Update an existing room.
     */
    public function updateRoom(Room $room, array $data): Room
    {
        return DB::transaction(function () use ($room, $data) {
            $data['updated_by'] = auth()->id();

            // Handle JSON fields
            if (isset($data['facilities']) && is_array($data['facilities'])) {
                $data['facilities'] = json_encode($data['facilities']);
            }

            if (isset($data['equipment']) && is_array($data['equipment'])) {
                $data['equipment'] = json_encode($data['equipment']);
            }

            if (isset($data['accessibility_features']) && is_array($data['accessibility_features'])) {
                $data['accessibility_features'] = json_encode($data['accessibility_features']);
            }

            if (isset($data['rules_and_regulations']) && is_array($data['rules_and_regulations'])) {
                $data['rules_and_regulations'] = json_encode($data['rules_and_regulations']);
            }

            if (isset($data['usage_policies']) && is_array($data['usage_policies'])) {
                $data['usage_policies'] = json_encode($data['usage_policies']);
            }

            if (isset($data['schedule_rules']) && is_array($data['schedule_rules'])) {
                $data['schedule_rules'] = json_encode($data['schedule_rules']);
            }

            $room->update($data);

            Log::info('Room updated', [
                'room_id' => $room->id,
                'room_code' => $room->room_code,
                'updated_by' => auth()->id()
            ]);

            return $room->load(['creator', 'updater']);
        });
    }

    /**
     * Delete a room (soft delete).
     */
    public function deleteRoom(Room $room): bool
    {
        return DB::transaction(function () use ($room) {
            $roomId = $room->id;
            $roomCode = $room->room_code;

            // Check if room has active schedules
            $activeSchedules = $room->activeSchedules()->count();
            if ($activeSchedules > 0) {
                throw new \Exception("Cannot delete room with {$activeSchedules} active schedules");
            }

            $deleted = $room->delete();

            if ($deleted) {
                Log::info('Room deleted', [
                    'room_id' => $roomId,
                    'room_code' => $roomCode,
                    'deleted_by' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Get trashed (soft-deleted) rooms.
     */
    public function getTrashedRooms(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Room::onlyTrashed()->with(['creator', 'updater']);

        // Apply filters to trashed rooms
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('room_code', 'like', "%{$search}%")
                  ->orWhere('building', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['building'])) {
            $query->where('building', $filters['building']);
        }

        if (!empty($filters['room_type'])) {
            $query->where('room_type', $filters['room_type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('deleted_at', 'desc')
                     ->paginate($perPage);
    }

    /**
     * Restore a deleted room.
     */
    public function restoreRoom(int $roomId): Room
    {
        $room = Room::withTrashed()->findOrFail($roomId);

        DB::transaction(function () use ($room) {
            $room->restore();
            $room->update(['updated_by' => auth()->id()]);

            Log::info('Room restored', [
                'room_id' => $room->id,
                'room_code' => $room->room_code,
                'restored_by' => auth()->id()
            ]);
        });

        return $room->load(['creator', 'updater']);
    }

    /**
     * Permanently delete a room.
     */
    public function forceDeleteRoom(int $roomId): bool
    {
        $room = Room::withTrashed()->findOrFail($roomId);

        return DB::transaction(function () use ($room) {
            $roomId = $room->id;
            $roomCode = $room->room_code;

            $deleted = $room->forceDelete();

            if ($deleted) {
                Log::warning('Room permanently deleted', [
                    'room_id' => $roomId,
                    'room_code' => $roomCode,
                    'deleted_by' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Get room statistics.
     */
    public function getRoomStatistics(): array
    {
        $total = Room::count();
        $active = Room::where('is_active', true)->count();
        $inactive = Room::where('is_active', false)->count();
        $trashed = Room::onlyTrashed()->count();

        $query = Room::query();
        $available = $query->where('availability_status', 'available')->count();
        $occupied = Room::where('availability_status', 'occupied')->count();
        $maintenance = Room::where('availability_status', 'maintenance')->count();
        $reserved = Room::where('availability_status', 'reserved')->count();

        $roomTypeStats = $query->selectRaw('room_type, COUNT(*) as count')
            ->groupBy('room_type')
            ->pluck('count', 'room_type')
            ->toArray();

        $buildingStats = $query->selectRaw('building, COUNT(*) as count')
            ->groupBy('building')
            ->orderBy('building')
            ->get()
            ->pluck('count', 'building')
            ->toArray();

        $maintenanceStats = $query->selectRaw('maintenance_status, COUNT(*) as count')
            ->groupBy('maintenance_status')
            ->pluck('count', 'maintenance_status')
            ->toArray();

        $capacityStats = [
            'total_capacity' => $query->sum('capacity'),
            'average_capacity' => $query->avg('capacity'),
            'max_capacity' => $query->max('capacity'),
            'min_capacity' => $query->min('capacity'),
        ];

        // Calculate utilization rate (handle case where no schedules exist)
        try {
            $totalScheduledHours = Schedule::whereDate('start_date', '>=', now()->subMonth())
                ->whereDate('start_date', '<=', now())
                ->where('status', 'active')
                ->count();

            $totalPossibleHours = $total * 8 * 22; // 8 hours/day, 22 working days
            $utilizationRate = $totalPossibleHours > 0 ? round(($totalScheduledHours / $totalPossibleHours) * 100, 1) : 0;
        } catch (\Exception $e) {
            $totalScheduledHours = 0;
            $utilizationRate = 0;
        }

        return [
            'total_rooms' => $total,
            'active_rooms' => $active,
            'inactive_rooms' => $inactive,
            'trashed_rooms' => $trashed,
            'buildings' => count($buildingStats),
            'by_availability_status' => [
                'available' => $available,
                'occupied' => $occupied,
                'maintenance' => $maintenance,
                'reserved' => $reserved,
            ],
            'by_room_type' => $roomTypeStats,
            'by_building' => $buildingStats,
            'by_maintenance_status' => $maintenanceStats,
            'capacity_statistics' => $capacityStats,
            'utilization_rate' => $utilizationRate,
        ];
    }

    /**
     * Get available rooms for scheduling.
     */
    public function getAvailableRoomsForSchedule(string $date, string $startTime, string $endTime, array $requirements = []): array
    {
        $query = Room::where('is_active', true)
            ->where('availability_status', 'available')
            ->where('maintenance_status', '!=', 'under_maintenance');

        // Apply capacity requirement
        if (!empty($requirements['capacity'])) {
            $query->where('capacity', '>=', $requirements['capacity']);
        }

        // Apply room type requirement
        if (!empty($requirements['room_type'])) {
            $query->where('room_type', $requirements['room_type']);
        }

        // Apply building requirement
        if (!empty($requirements['building'])) {
            $query->where('building', $requirements['building']);
        }

        // Apply facility requirements
        if (!empty($requirements['facilities']) && is_array($requirements['facilities'])) {
            foreach ($requirements['facilities'] as $facility) {
                $query->whereJsonContains('facilities', $facility);
            }
        }

        // Get all potential rooms
        $allRooms = $query->get();

        // Filter rooms by schedule conflicts
        $availableRooms = $allRooms->filter(function ($room) use ($date, $startTime, $endTime) {
            return $room->isAvailableForSchedule($date, $startTime, $endTime);
        });

        return [
            'available_rooms' => $availableRooms,
            'total_available' => $availableRooms->count(),
            'date' => $date,
            'time_range' => "{$startTime} - {$endTime}",
            'requirements' => $requirements,
        ];
    }

    /**
     * Get room schedule.
     */
    public function getRoomSchedule(Room $room, string $period = 'week'): array
    {
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();

        if ($period === 'today') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($period === 'month') {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        $schedules = $room->schedules()
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->where('status', 'active')
            ->with(['course', 'lecturer'])
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get();

        return [
            'room' => $room->load(['creator', 'updater']),
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'schedules' => $schedules,
            'total_schedules' => $schedules->count(),
        ];
    }

    /**
     * Bulk update rooms.
     */
    public function bulkUpdateRooms(array $roomIds, array $updates): int
    {
        return DB::transaction(function () use ($roomIds, $updates) {
            $updates['updated_by'] = auth()->id();

            $updated = Room::whereIn('id', $roomIds)
                ->update($updates);

            Log::info('Bulk room update', [
                'room_count' => $updated,
                'room_ids' => $roomIds,
                'updates' => $updates,
                'updated_by' => auth()->id()
            ]);

            return $updated;
        });
    }

    /**
     * Import rooms from file.
     */
    public function importRooms($file, $user): array
    {
        try {
            $import = new RoomsImport($user);
            Excel::import($import, $file);

            return [
                'success' => true,
                'imported_count' => $import->getImportedCount(),
                'failed_count' => $import->getFailedCount(),
                'errors' => $import->getErrors(),
            ];
        } catch (\Exception $e) {
            Log::error('Room import failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Export rooms to file.
     */
    public function exportRooms(array $filters = [], string $format = 'csv'): string
    {
        $rooms = $this->getRoomsForExport($filters);

        $fileName = 'rooms_' . date('Y_m_d_H_i_s') . '.' . $format;
        $filePath = 'exports/' . $fileName;

        if ($format === 'excel') {
            Excel::store(new RoomsExport($rooms), $filePath, 'public', \Maatwebsite\Excel\Excel::XLSX);
        } else {
            Excel::store(new RoomsExport($rooms), $filePath, 'public', \Maatwebsite\Excel\Excel::CSV);
        }

        return Storage::url($filePath);
    }

    /**
     * Get rooms for export.
     */
    private function getRoomsForExport(array $filters): \Illuminate\Support\Collection
    {
        $query = Room::with(['creator', 'updater']);

        foreach ($filters as $key => $value) {
            if ($value) {
                $query->where($key, $value);
            }
        }

        return $query->get();
    }

    /**
     * Get room search suggestions.
     */
    public function getRoomSearchSuggestions(string $query, int $limit = 10): array
    {
        return Room::where('name', 'like', "%{$query}%")
            ->orWhere('room_code', 'like', "%{$query}%")
            ->orWhere('building', 'like', "%{$query}%")
            ->orWhere('location', 'like', "%{$query}%")
            ->limit($limit)
            ->get(['id', 'name', 'room_code', 'building', 'floor', 'capacity', 'room_type'])
            ->toArray();
    }

    /**
     * Update room availability status.
     */
    public function updateRoomAvailability(Room $room, string $status, ?string $reason = null): Room
    {
        return DB::transaction(function () use ($room, $status, $reason) {
            $oldStatus = $room->availability_status;

            $room->update([
                'availability_status' => $status,
                'updated_by' => auth()->id(),
            ]);

            // Add note about status change
            if ($reason) {
                $note = date('Y-m-d H:i:s') . ": Status changed from '{$oldStatus}' to '{$status}'. Reason: {$reason}";
                $room->notes = ($room->notes ?? '') . "\n\n" . $note;
                $room->save();
            }

            Log::info('Room availability status updated', [
                'room_id' => $room->id,
                'room_code' => $room->room_code,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'updated_by' => auth()->id()
            ]);

            return $room->load(['creator', 'updater']);
        });
    }

    /**
     * Schedule room maintenance.
     */
    public function scheduleMaintenance(Room $room, array $data): Room
    {
        return DB::transaction(function () use ($room, $data) {
            $room->update([
                'maintenance_status' => 'under_maintenance',
                'availability_status' => 'maintenance',
                'last_maintenance_date' => now(),
                'next_maintenance_date' => $data['next_maintenance_date'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            Log::info('Room maintenance scheduled', [
                'room_id' => $room->id,
                'room_code' => $room->room_code,
                'maintenance_date' => now(),
                'next_maintenance_date' => $data['next_maintenance_date'] ?? null,
                'updated_by' => auth()->id()
            ]);

            return $room->load(['creator', 'updater']);
        });
    }

    /**
     * Complete room maintenance.
     */
    public function completeMaintenance(Room $room, array $data): Room
    {
        return DB::transaction(function () use ($room, $data) {
            $room->update([
                'maintenance_status' => $data['maintenance_status'] ?? 'good',
                'availability_status' => 'available',
                'next_maintenance_date' => $data['next_maintenance_date'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            Log::info('Room maintenance completed', [
                'room_id' => $room->id,
                'room_code' => $room->room_code,
                'maintenance_status' => $room->maintenance_status,
                'next_maintenance_date' => $room->next_maintenance_date,
                'updated_by' => auth()->id()
            ]);

            return $room->load(['creator', 'updater']);
        });
    }

    /**
     * Get rooms that need maintenance attention.
     */
    public function getRoomsNeedingAttention(): \Illuminate\Database\Eloquent\Collection
    {
        return Room::where(function ($query) {
                $query->where('maintenance_status', 'needs_attention')
                    ->orWhere('maintenance_status', 'critical')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('next_maintenance_date')
                            ->where('next_maintenance_date', '<=', now()->addDays(7));
                    });
            })
            ->where('is_active', true)
            ->orderBy('next_maintenance_date', 'asc')
            ->get();
    }

    /**
     * Get room utilization report.
     */
    public function getRoomUtilizationReport(string $period = 'month'): array
    {
        $startDate = now()->subMonth();
        $endDate = now();

        if ($period === 'week') {
            $startDate = now()->subWeek();
        } elseif ($period === 'year') {
            $startDate = now()->subYear();
        }

        $rooms = Room::where('is_active', true)->get();
        $utilizationData = [];

        foreach ($rooms as $room) {
            $totalScheduledHours = $room->schedules()
                ->whereDate('start_date', '<=', $endDate)
                ->whereDate('end_date', '>=', $startDate)
                ->where('status', 'active')
                ->selectRaw('SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours')
                ->value('total_hours') ?? 0;

            $totalPossibleHours = $this->calculateRoomAvailableHours($startDate, $endDate);
            $utilizationRate = $totalPossibleHours > 0 ? round(($totalScheduledHours / $totalPossibleHours) * 100, 1) : 0;

            $utilizationData[] = [
                'room' => $room,
                'scheduled_hours' => $totalScheduledHours,
                'possible_hours' => $totalPossibleHours,
                'utilization_rate' => $utilizationRate,
            ];
        }

        // Sort by utilization rate (descending)
        usort($utilizationData, function ($a, $b) {
            return $b['utilization_rate'] - $a['utilization_rate'];
        });

        $averageUtilization = collect($utilizationData)->avg('utilization_rate');

        return [
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'average_utilization' => round($averageUtilization, 1),
            'rooms' => $utilizationData,
            'total_rooms_analyzed' => count($utilizationData),
        ];
    }

    /**
     * Calculate available hours for a room.
     */
    private function calculateRoomAvailableHours($startDate, $endDate): int
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

    /**
     * Toggle room status.
     */
    public function toggleRoomStatus(int $id, bool $isActive): array
    {
        return DB::transaction(function () use ($id, $isActive) {
            $room = Room::findOrFail($id);

            $oldStatus = $room->is_active;
            $room->update([
                'is_active' => $isActive,
                'updated_by' => auth()->id(),
            ]);

            // Log activity
            Log::channel('activity')->info('Room status toggled', [
                'room_id' => $room->id,
                'room_code' => $room->room_code,
                'name' => $room->name,
                'old_status' => $oldStatus,
                'new_status' => $isActive,
                'updated_by' => auth()->id(),
            ]);

            return [
                'room' => $room->fresh(['creator', 'updater']),
                'message' => 'Room status updated successfully'
            ];
        });
    }

    /**
     * Duplicate a room.
     */
    public function duplicateRoom(int $id, $user): array
    {
        return DB::transaction(function () use ($id, $user) {
            $originalRoom = Room::findOrFail($id);

            $newRoom = $originalRoom->replicate();
            $newRoom->room_code = $this->generateUniqueRoomCode($originalRoom->room_code);
            $newRoom->name = $this->generateDuplicateName($originalRoom->name);
            $newRoom->created_by = $user->id;
            $newRoom->updated_by = null;
            $newRoom->created_at = now();
            $newRoom->updated_at = null;

            $newRoom->save();

            // Log activity
            Log::channel('activity')->info('Room duplicated', [
                'original_room_id' => $originalRoom->id,
                'new_room_id' => $newRoom->id,
                'room_code' => $newRoom->room_code,
                'name' => $newRoom->name,
                'duplicated_by' => $user->id,
            ]);

            return [
                'room' => $newRoom->fresh(['creator', 'updater']),
                'message' => 'Room duplicated successfully'
            ];
        });
    }

    /**
     * Bulk delete rooms.
     */
    public function bulkDeleteRooms(array $roomIds): int
    {
        return DB::transaction(function () use ($roomIds) {
            $rooms = Room::whereIn('id', $roomIds)->get();
            $deletedCount = 0;

            foreach ($rooms as $room) {
                if ($room->delete()) {
                    $deletedCount++;

                    // Log activity
                    Log::channel('activity')->info('Room deleted (bulk)', [
                        'room_id' => $room->id,
                        'room_code' => $room->room_code,
                        'name' => $room->name,
                        'deleted_by' => auth()->id(),
                    ]);
                }
            }

            return $deletedCount;
        });
    }

    /**
     * Bulk force delete rooms permanently.
     */
    public function bulkForceDeleteRooms(array $roomIds): int
    {
        return DB::transaction(function () use ($roomIds) {
            $rooms = Room::onlyTrashed()->whereIn('id', $roomIds)->get();
            $deletedCount = 0;

            foreach ($rooms as $room) {
                // Delete related schedules
                $room->schedules()->forceDelete();

                // Force delete the room
                if ($room->forceDelete()) {
                    $deletedCount++;

                    // Log activity
                    Log::channel('activity')->warning('Room permanently deleted (bulk)', [
                        'room_id' => $room->id,
                        'room_code' => $room->room_code,
                        'name' => $room->name,
                        'permanently_deleted_by' => auth()->id(),
                    ]);
                }
            }

            return $deletedCount;
        });
    }

    /**
     * Bulk toggle room status.
     */
    public function bulkToggleRoomStatus(array $roomIds, bool $isActive): array
    {
        return DB::transaction(function () use ($roomIds, $isActive) {
            $rooms = Room::whereIn('id', $roomIds)->get();
            $updatedCount = 0;

            foreach ($rooms as $room) {
                $oldStatus = $room->is_active;
                $room->update([
                    'is_active' => $isActive,
                    'updated_by' => auth()->id(),
                ]);
                $updatedCount++;

                // Log activity
                Log::channel('activity')->info('Room status toggled (bulk)', [
                    'room_id' => $room->id,
                    'room_code' => $room->room_code,
                    'name' => $room->name,
                    'old_status' => $oldStatus,
                    'new_status' => $isActive,
                    'updated_by' => auth()->id(),
                ]);
            }

            return [
                'updated_count' => $updatedCount,
                'message' => "Successfully updated {$updatedCount} rooms"
            ];
        });
    }

    /**
     * Generate unique room code for duplicate.
     */
    private function generateUniqueRoomCode(string $originalCode): string
    {
        // If original code already has COPY suffix, extract base code
        $baseCode = preg_replace('/-COPY-\d+$/', '', $originalCode);

        // Find existing copies
        $existingCodes = Room::where('room_code', 'like', $baseCode . '-COPY-%')
            ->pluck('room_code')
            ->toArray();

        $counter = 1;

        do {
            $newCode = $baseCode . '-COPY-' . $counter;
            $exists = in_array($newCode, $existingCodes);
            $counter++;
        } while ($exists);

        return $newCode;
    }

    /**
     * Generate duplicate name.
     */
    private function generateDuplicateName(string $originalName): string
    {
        // If original name already has (Copy) suffix, increment the number
        if (preg_match('/^(.*) \(Copy (\d+)\)$/', $originalName, $matches)) {
            $baseName = $matches[1];
            $copyNumber = (int) $matches[2];
            return $baseName . ' (Copy ' . ($copyNumber + 1) . ')';
        } elseif (preg_match('/^(.*) \(Copy\)$/', $originalName, $matches)) {
            $baseName = $matches[1];
            return $baseName . ' (Copy 2)';
        } else {
            return $originalName . ' (Copy)';
        }
    }
}