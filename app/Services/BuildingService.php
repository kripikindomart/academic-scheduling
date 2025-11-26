<?php

namespace App\Services;

use App\Models\Building;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BuildingService
{
    /**
     * Get buildings with pagination and filtering.
     */
    public function getBuildings(array $filters = [], int $perPage = 20, string $sortBy = 'name', string $sortDir = 'asc'): LengthAwarePaginator
    {
        $query = Building::with(['creator', 'updater']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['building_type'])) {
            $query->byType($filters['building_type']);
        }

        if (!empty($filters['department'])) {
            $query->byDepartment($filters['department']);
        }

        if (!empty($filters['faculty'])) {
            $query->byFaculty($filters['faculty']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['min_floor'])) {
            $query->where('floor_count', '>=', $filters['min_floor']);
        }

        if (!empty($filters['max_floor'])) {
            $query->where('floor_count', '<=', $filters['max_floor']);
        }

        if (!empty($filters['min_rooms'])) {
            $query->where('total_rooms', '>=', $filters['min_rooms']);
        }

        if (!empty($filters['max_rooms'])) {
            $query->where('total_rooms', '<=', $filters['max_rooms']);
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage);
    }

    /**
     * Get building by ID with relationships.
     */
    public function getBuildingById(int $id): Building
    {
        return Building::with(['creator', 'updater', 'rooms'])->findOrFail($id);
    }

    /**
     * Create new building.
     */
    public function createBuilding(array $data, User $user): Building
    {
        DB::beginTransaction();
        try {
            $building = Building::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'description' => $data['description'] ?? null,
                'address' => $data['address'] ?? null,
                'floor_count' => $data['floor_count'] ?? 1,
                'total_rooms' => $data['total_rooms'] ?? 0,
                'area' => $data['area'] ?? null,
                'year_built' => $data['year_built'] ?? null,
                'building_type' => $data['building_type'] ?? 'academic',
                'department' => $data['department'] ?? null,
                'faculty' => $data['faculty'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'notes' => $data['notes'] ?? null,
                'created_by' => $user->id,
            ]);

            DB::commit();
            Log::info('Building created', ['building_id' => $building->id, 'user_id' => $user->id]);

            return $building->load(['creator', 'updater']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create building', ['error' => $e->getMessage()]);
            throw new Exception('Failed to create building: ' . $e->getMessage());
        }
    }

    /**
     * Update building.
     */
    public function updateBuilding(Building $building, array $data): Building
    {
        DB::beginTransaction();
        try {
            $building->update([
                'name' => $data['name'] ?? $building->name,
                'code' => $data['code'] ?? $building->code,
                'description' => $data['description'] ?? $building->description,
                'address' => $data['address'] ?? $building->address,
                'floor_count' => $data['floor_count'] ?? $building->floor_count,
                'total_rooms' => $data['total_rooms'] ?? $building->total_rooms,
                'area' => $data['area'] ?? $building->area,
                'year_built' => $data['year_built'] ?? $building->year_built,
                'building_type' => $data['building_type'] ?? $building->building_type,
                'department' => $data['department'] ?? $building->department,
                'faculty' => $data['faculty'] ?? $building->faculty,
                'is_active' => isset($data['is_active']) ? $data['is_active'] : $building->is_active,
                'notes' => $data['notes'] ?? $building->notes,
            ]);

            DB::commit();
            Log::info('Building updated', ['building_id' => $building->id]);

            return $building->load(['creator', 'updater']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update building', ['building_id' => $building->id, 'error' => $e->getMessage()]);
            throw new Exception('Failed to update building: ' . $e->getMessage());
        }
    }

    /**
     * Delete building (soft delete).
     */
    public function deleteBuilding(Building $building): bool
    {
        if (!$building->canBeDeleted()) {
            throw new Exception('Cannot delete building with existing rooms');
        }

        DB::beginTransaction();
        try {
            $building->delete();

            DB::commit();
            Log::info('Building deleted', ['building_id' => $building->id]);

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete building', ['building_id' => $building->id, 'error' => $e->getMessage()]);
            throw new Exception('Failed to delete building: ' . $e->getMessage());
        }
    }

    /**
     * Get building statistics.
     */
    public function getBuildingStatistics(): array
    {
        return Building::getStatistics();
    }

    /**
     * Get buildings with room count.
     */
    public function getWithRoomCount(): Collection
    {
        return Building::withCount(['rooms', 'activeRooms'])
            ->with(['rooms' => function ($query) {
                $query->select('id', 'building_id', 'name', 'capacity', 'availability_status')
                    ->where('is_active', true)
                    ->orderBy('name');
            }])
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get buildings by type.
     */
    public function getByType(string $type): Collection
    {
        return Building::byType($type)
            ->withCount(['rooms', 'activeRooms'])
            ->active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get trashed buildings.
     */
    public function getTrashedBuildings(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Building::onlyTrashed()->with(['creator', 'updater']);

        // Apply filters for trashed buildings
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['building_type'])) {
            $query->byType($filters['building_type']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Restore deleted building.
     */
    public function restoreBuilding(int $id): Building
    {
        $building = Building::withTrashed()->findOrFail($id);

        DB::beginTransaction();
        try {
            $building->restore();

            DB::commit();
            Log::info('Building restored', ['building_id' => $building->id]);

            return $building->load(['creator', 'updater']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore building', ['building_id' => $building->id, 'error' => $e->getMessage()]);
            throw new Exception('Failed to restore building: ' . $e->getMessage());
        }
    }

    /**
     * Force delete building.
     */
    public function forceDeleteBuilding(int $id): bool
    {
        $building = Building::withTrashed()->findOrFail($id);

        DB::beginTransaction();
        try {
            $building->forceDelete();

            DB::commit();
            Log::info('Building permanently deleted', ['building_id' => $building->id]);

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to permanently delete building', ['building_id' => $building->id, 'error' => $e->getMessage()]);
            throw new Exception('Failed to permanently delete building: ' . $e->getMessage());
        }
    }

    /**
     * Toggle building status.
     */
    public function toggleBuildingStatus(int $id, bool $isActive): Building
    {
        $building = Building::findOrFail($id);

        DB::beginTransaction();
        try {
            $building->update(['is_active' => $isActive]);

            DB::commit();
            Log::info('Building status toggled', ['building_id' => $building->id, 'is_active' => $isActive]);

            return $building->load(['creator', 'updater']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to toggle building status', ['building_id' => $building->id, 'error' => $e->getMessage()]);
            throw new Exception('Failed to toggle building status: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate building.
     */
    public function duplicateBuilding(int $id, User $user): Building
    {
        $originalBuilding = Building::findOrFail($id);

        DB::beginTransaction();
        try {
            $newBuilding = Building::create([
                'name' => $originalBuilding->name . ' (Copy)',
                'code' => $originalBuilding->code . '-COPY',
                'description' => $originalBuilding->description,
                'address' => $originalBuilding->address,
                'floor_count' => $originalBuilding->floor_count,
                'total_rooms' => 0, // Reset room count
                'area' => $originalBuilding->area,
                'year_built' => $originalBuilding->year_built,
                'building_type' => $originalBuilding->building_type,
                'department' => $originalBuilding->department,
                'faculty' => $originalBuilding->faculty,
                'is_active' => true, // Start as active
                'notes' => $originalBuilding->notes,
                'created_by' => $user->id,
            ]);

            DB::commit();
            Log::info('Building duplicated', ['original_id' => $originalBuilding->id, 'new_id' => $newBuilding->id]);

            return $newBuilding->load(['creator', 'updater']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to duplicate building', ['building_id' => $originalBuilding->id, 'error' => $e->getMessage()]);
            throw new Exception('Failed to duplicate building: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update buildings.
     */
    public function bulkUpdateBuildings(array $buildingIds, array $updates): int
    {
        DB::beginTransaction();
        try {
            $updated = Building::whereIn('id', $buildingIds)->update($updates);

            DB::commit();
            Log::info('Buildings bulk updated', ['count' => $updated, 'updates' => $updates]);

            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk update buildings', ['building_ids' => $buildingIds, 'error' => $e->getMessage()]);
            throw new Exception('Failed to bulk update buildings: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete buildings.
     */
    public function bulkDeleteBuildings(array $buildingIds): int
    {
        // Check if any buildings have rooms
        $buildingsWithRooms = Building::whereIn('id', $buildingIds)
            ->whereHas('rooms')
            ->count();

        if ($buildingsWithRooms > 0) {
            throw new Exception('Cannot delete buildings with existing rooms');
        }

        DB::beginTransaction();
        try {
            $deleted = Building::whereIn('id', $buildingIds)->delete();

            DB::commit();
            Log::info('Buildings bulk deleted', ['count' => $deleted]);

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk delete buildings', ['building_ids' => $buildingIds, 'error' => $e->getMessage()]);
            throw new Exception('Failed to bulk delete buildings: ' . $e->getMessage());
        }
    }

    /**
     * Bulk toggle building status.
     */
    public function bulkToggleBuildingStatus(array $buildingIds, bool $isActive): int
    {
        DB::beginTransaction();
        try {
            $updated = Building::whereIn('id', $buildingIds)->update(['is_active' => $isActive]);

            DB::commit();
            Log::info('Buildings bulk status toggled', ['count' => $updated, 'is_active' => $isActive]);

            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk toggle building status', ['building_ids' => $buildingIds, 'error' => $e->getMessage()]);
            throw new Exception('Failed to bulk toggle building status: ' . $e->getMessage());
        }
    }

    /**
     * Import buildings from CSV/Excel.
     */
    public function importBuildings($file, User $user): array
    {
        // Implementation would depend on your preferred import library
        // This is a placeholder for the import functionality
        return [
            'success' => true,
            'message' => 'Import functionality not yet implemented',
            'imported_count' => 0,
            'failed_count' => 0,
        ];
    }

    /**
     * Export buildings to CSV/Excel.
     */
    public function exportBuildings(array $filters = [], string $format = 'csv'): string
    {
        // Implementation would depend on your preferred export library
        // This is a placeholder for the export functionality
        return 'export/path/file.' . $format;
    }

    /**
     * Get building search suggestions.
     */
    public function getBuildingSearchSuggestions(string $query, int $limit = 10): array
    {
        return Building::where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->limit($limit)
            ->get(['id', 'name', 'code'])
            ->map(function ($building) {
                return [
                    'id' => $building->id,
                    'name' => $building->name,
                    'code' => $building->code,
                    'display' => "{$building->name} ({$building->code})",
                ];
            })
            ->toArray();
    }
}