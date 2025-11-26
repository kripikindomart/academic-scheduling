<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use App\Services\BuildingService;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BuildingController extends Controller
{
    protected BuildingService $buildingService;

    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search',
                'building_type',
                'department',
                'faculty',
                'is_active',
                'min_floor',
                'max_floor',
                'min_rooms',
                'max_rooms',
            ]);

            $perPage = $request->get('per_page', 20);
            $sortBy = $request->get('sort_by', 'name');
            $sortDir = $request->get('sort_dir', 'asc');

            $paginator = $this->buildingService->getBuildings($filters, $perPage, $sortBy, $sortDir);

            return ResponseService::paginated(
                $paginator,
                'Buildings retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve buildings: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:buildings,name',
            'code' => 'required|string|max:50|unique:buildings,code',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'floor_count' => 'nullable|integer|min:1|max:100',
            'total_rooms' => 'nullable|integer|min:0',
            'area' => 'nullable|numeric|min:0',
            'year_built' => 'nullable|integer|min:1900|max:' . date('Y'),
            'building_type' => 'nullable|in:academic,laboratory,administrative,library,workshop,multipurpose,sports,residence,other',
            'department' => 'nullable|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $building = $this->buildingService->createBuilding($validated, $request->user());

            return ResponseService::success(
                $building,
                'Building created successfully',
                201
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to create building: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Building $building): JsonResponse
    {
        $building->load(['creator', 'updater', 'rooms' => function ($query) {
            $query->select('id', 'building_id', 'name', 'capacity', 'availability_status')
                ->where('is_active', true)
                ->orderBy('name');
        }]);

        return ResponseService::success($building, 'Building retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Building $building): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:buildings,name,' . $building->id,
            'code' => 'required|string|max:50|unique:buildings,code,' . $building->id,
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'floor_count' => 'nullable|integer|min:1|max:100',
            'total_rooms' => 'nullable|integer|min:0',
            'area' => 'nullable|numeric|min:0',
            'year_built' => 'nullable|integer|min:1900|max:' . date('Y'),
            'building_type' => 'nullable|in:academic,laboratory,administrative,library,workshop,multipurpose,sports,residence,other',
            'department' => 'nullable|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $updatedBuilding = $this->buildingService->updateBuilding($building, $validated);

            return ResponseService::success(
                $updatedBuilding,
                'Building updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update building: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Building $building): JsonResponse
    {
        try {
            $this->buildingService->deleteBuilding($building);

            return ResponseService::success(
                null,
                'Building deleted successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to delete building: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get building statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->buildingService->getBuildingStatistics();

            return ResponseService::success(
                $statistics,
                'Building statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve building statistics: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get buildings with room count.
     */
    public function getWithRoomCount(): JsonResponse
    {
        try {
            $buildings = $this->buildingService->getWithRoomCount();

            return ResponseService::success($buildings, 'Buildings with room count retrieved successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve buildings with room count: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get buildings by type.
     */
    public function getByType($type): JsonResponse
    {
        try {
            $buildings = $this->buildingService->getByType($type);

            return ResponseService::success($buildings, 'Buildings retrieved successfully by type');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve buildings by type: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get trashed buildings.
     */
    public function trash(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search',
                'building_type',
            ]);

            $perPage = $request->get('per_page', 20);
            $paginator = $this->buildingService->getTrashedBuildings($perPage, $filters);

            return ResponseService::paginated(
                $paginator,
                'Trashed buildings retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to retrieve trashed buildings: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Restore deleted building.
     */
    public function restore($id): JsonResponse
    {
        try {
            $building = $this->buildingService->restoreBuilding($id);

            return ResponseService::success($building, 'Building restored successfully');
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to restore building: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Permanently delete building.
     */
    public function forceDelete($id): JsonResponse
    {
        $this->buildingService->forceDeleteBuilding($id);

        return ResponseService::success(null, 'Building permanently deleted');
    }

    /**
     * Toggle building status.
     */
    public function toggleStatus(Building $building, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        try {
            $updatedBuilding = $this->buildingService->toggleBuildingStatus($building->id, $validated['is_active']);

            return ResponseService::success(
                $updatedBuilding,
                'Building status updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to update building status: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Duplicate building.
     */
    public function duplicate(Building $building, Request $request): JsonResponse
    {
        try {
            $duplicatedBuilding = $this->buildingService->duplicateBuilding($building->id, $request->user());

            return ResponseService::success(
                $duplicatedBuilding,
                'Building duplicated successfully',
                201
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to duplicate building: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk update buildings.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'building_ids' => 'required|array',
            'building_ids.*' => 'exists:buildings,id',
            'updates' => 'required|array',
            'updates.building_type' => 'sometimes|in:academic,laboratory,administrative,library,workshop,multipurpose,sports,residence,other',
            'updates.is_active' => 'sometimes|boolean',
            'updates.department' => 'sometimes|string|max:255',
            'updates.faculty' => 'sometimes|string|max:255',
        ]);

        try {
            $updatedCount = $this->buildingService->bulkUpdateBuildings(
                $validated['building_ids'],
                $validated['updates']
            );

            return ResponseService::success(
                ['updated_count' => $updatedCount],
                "Successfully updated {$updatedCount} buildings"
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk update buildings: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk delete buildings.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'building_ids' => 'required|array',
            'building_ids.*' => 'exists:buildings,id',
        ]);

        try {
            $deletedCount = $this->buildingService->bulkDeleteBuildings($validated['building_ids']);

            return ResponseService::success(
                ['deleted_count' => $deletedCount],
                "Successfully deleted {$deletedCount} buildings"
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk delete buildings: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk toggle building status.
     */
    public function bulkToggleStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'building_ids' => 'required|array',
            'building_ids.*' => 'exists:buildings,id',
            'is_active' => 'required|boolean',
        ]);

        try {
            $updatedCount = $this->buildingService->bulkToggleBuildingStatus(
                $validated['building_ids'],
                $validated['is_active']
            );

            $status = $validated['is_active'] ? 'activated' : 'deactivated';

            return ResponseService::success(
                ['updated_count' => $updatedCount],
                "Successfully {$status} {$updatedCount} buildings"
            );
        } catch (\Exception $e) {
            return ResponseService::error(
                'Failed to bulk toggle building status: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Import buildings from CSV/Excel.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // Max 10MB
        ]);

        $result = $this->buildingService->importBuildings(
            $request->file('file'),
            $request->user()
        );

        return ResponseService::success($result, 'Buildings import completed');
    }

    /**
     * Export buildings to CSV/Excel.
     */
    public function export(Request $request): JsonResponse
    {
        $filters = $request->only([
            'building_type',
            'department',
            'faculty',
            'is_active',
        ]);

        $format = $request->input('format', 'csv');
        $filePath = $this->buildingService->exportBuildings($filters, $format);

        return ResponseService::success(
            ['download_url' => asset($filePath)],
            'Buildings export completed'
        );
    }

    /**
     * Get building search suggestions.
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->input('query');
        $limit = $request->input('limit', 10);

        $suggestions = $this->buildingService->getBuildingSearchSuggestions($query, $limit);

        return ResponseService::success($suggestions, 'Search suggestions retrieved successfully');
    }
}