<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Services\RoomService;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    protected RoomService $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'building' => $request->input('building'),
            'floor' => $request->input('floor'),
            'room_type' => $request->input('room_type'),
            'min_capacity' => $request->input('min_capacity'),
            'max_capacity' => $request->input('max_capacity'),
            'department' => $request->input('department'),
            'faculty' => $request->input('faculty'),
            'availability_status' => $request->input('availability_status'),
            'maintenance_status' => $request->input('maintenance_status'),
            'is_active' => $request->input('is_active'),
            'has_facilities' => $request->input('has_facilities'),
        ];

        $result = $this->roomService->getRooms(
            $filters,
            $request->input('per_page', 15),
            $request->input('sort_by', 'name'),
            $request->input('sort_direction', 'asc')
        );

        return response()->success($result['data'], $result['message'], $result['meta']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        $room = $this->roomService->createRoom($request->validated());

        return response()->success($room, 'Room created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room): JsonResponse
    {
        $room->load([
            'creator',
            'updater',
            'schedules' => function ($query) {
                $query->whereDate('date', '>=', now()->subWeek())
                    ->where('status', 'active')
                    ->with(['course', 'lecturer'])
                    ->orderBy('date')
                    ->orderBy('start_time');
            }
        ]);

        return response()->success($room, 'Room retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room): JsonResponse
    {
        $updatedRoom = $this->roomService->updateRoom($room, $request->validated());

        return response()->success($updatedRoom, 'Room updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room): JsonResponse
    {
        $this->roomService->deleteRoom($room);

        return response()->success(null, 'Room deleted successfully');
    }

    /**
     * Get room statistics.
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->roomService->getRoomStatistics();

        return response()->success($statistics, 'Room statistics retrieved successfully');
    }

    /**
     * Get available rooms for scheduling.
     */
    public function getAvailableForSchedule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'nullable|integer|min:1',
            'room_type' => 'nullable|in:classroom,laboratory,seminar_room,auditorium,workshop,library,office,meeting_room,multipurpose',
            'building' => 'nullable|string|max:100',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string|max:255',
        ]);

        $availableRooms = $this->roomService->getAvailableRoomsForSchedule(
            $validated['date'],
            $validated['start_time'],
            $validated['end_time'],
            $validated
        );

        return response()->success($availableRooms, 'Available rooms for scheduling retrieved successfully');
    }

    /**
     * Get room schedule.
     */
    public function getSchedule(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'period' => 'nullable|in:today,week,month',
        ]);

        $period = $validated['period'] ?? 'week';
        $schedule = $this->roomService->getRoomSchedule($room, $period);

        return response()->success($schedule, 'Room schedule retrieved successfully');
    }

    /**
     * Get rooms by building.
     */
    public function getByBuilding(Request $request, $building): JsonResponse
    {
        $filters = [
            'building' => $building,
            'search' => $request->input('search'),
            'room_type' => $request->input('room_type'),
            'is_active' => $request->input('is_active'),
        ];

        $rooms = $this->roomService->getRooms($filters);

        return response()->success($rooms['data'], $rooms['message'], $rooms['meta']);
    }

    /**
     * Get rooms by type.
     */
    public function getByType(Request $request, $type): JsonResponse
    {
        $filters = [
            'room_type' => $type,
            'search' => $request->input('search'),
            'building' => $request->input('building'),
            'is_active' => $request->input('is_active'),
        ];

        $rooms = $this->roomService->getRooms($filters);

        return response()->success($rooms['data'], $rooms['message'], $rooms['meta']);
    }

    /**
     * Get available rooms only.
     */
    public function getAvailable(Request $request): JsonResponse
    {
        $filters = [
            'availability_status' => 'available',
            'search' => $request->input('search'),
            'building' => $request->input('building'),
            'room_type' => $request->input('room_type'),
            'min_capacity' => $request->input('min_capacity'),
        ];

        $rooms = $this->roomService->getRooms($filters);

        return response()->success($rooms['data'], $rooms['message'], $rooms['meta']);
    }

    /**
     * Get rooms needing maintenance.
     */
    public function getNeedingMaintenance(): JsonResponse
    {
        $rooms = $this->roomService->getRoomsNeedingAttention();

        return response()->success($rooms, 'Rooms needing maintenance attention retrieved successfully');
    }

    /**
     * Update room availability status.
     */
    public function updateAvailability(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance,reserved,unavailable',
            'reason' => 'nullable|string|max:500',
        ]);

        $updatedRoom = $this->roomService->updateRoomAvailability($room, $validated['status'], $validated['reason']);

        return response()->success($updatedRoom, 'Room availability status updated successfully');
    }

    /**
     * Schedule room maintenance.
     */
    public function scheduleMaintenance(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'next_maintenance_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $updatedRoom = $this->roomService->scheduleMaintenance($room, $validated);

        return response()->success($updatedRoom, 'Room maintenance scheduled successfully');
    }

    /**
     * Complete room maintenance.
     */
    public function completeMaintenance(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'maintenance_status' => 'required|in:good,needs_attention,critical',
            'next_maintenance_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $updatedRoom = $this->roomService->completeMaintenance($room, $validated);

        return response()->success($updatedRoom, 'Room maintenance completed successfully');
    }

    /**
     * Get room utilization report.
     */
    public function getUtilizationReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period' => 'nullable|in:week,month,year',
        ]);

        $period = $validated['period'] ?? 'month';
        $report = $this->roomService->getRoomUtilizationReport($period);

        return response()->success($report, 'Room utilization report retrieved successfully');
    }

    /**
     * Bulk update rooms.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'updates' => 'required|array',
            'updates.availability_status' => 'sometimes|in:available,occupied,maintenance,reserved,unavailable',
            'updates.maintenance_status' => 'sometimes|in:good,needs_attention,under_maintenance,critical',
            'updates.is_active' => 'sometimes|boolean',
            'updates.department' => 'sometimes|string|max:255',
            'updates.faculty' => 'sometimes|string|max:255',
        ]);

        $updatedCount = $this->roomService->bulkUpdateRooms(
            $validated['room_ids'],
            $validated['updates']
        );

        return response()->success(
            ['updated_count' => $updatedCount],
            "Successfully updated {$updatedCount} rooms"
        );
    }

    /**
     * Import rooms from CSV/Excel.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // Max 10MB
        ]);

        $result = $this->roomService->importRooms(
            $request->file('file'),
            $request->user()
        );

        return response()->success($result, 'Rooms import completed');
    }

    /**
     * Export rooms to CSV/Excel.
     */
    public function export(Request $request): JsonResponse
    {
        $filters = $request->only([
            'building',
            'room_type',
            'availability_status',
            'maintenance_status',
            'is_active',
            'faculty',
            'department',
        ]);

        $format = $request->input('format', 'csv');
        $filePath = $this->roomService->exportRooms($filters, $format);

        return response()->success(
            ['download_url' => asset($filePath)],
            'Rooms export completed'
        );
    }

    /**
     * Get room search suggestions.
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->input('query');
        $limit = $request->input('limit', 10);

        $suggestions = $this->roomService->getRoomSearchSuggestions($query, $limit);

        return response()->success($suggestions, 'Search suggestions retrieved successfully');
    }

    /**
     * Restore deleted room.
     */
    public function restore($id): JsonResponse
    {
        $room = $this->roomService->restoreRoom($id);

        return response()->success($room, 'Room restored successfully');
    }

    /**
     * Permanently delete room.
     */
    public function forceDelete($id): JsonResponse
    {
        $this->roomService->forceDeleteRoom($id);

        return response()->success(null, 'Room permanently deleted');
    }

    /**
     * Get rooms by capacity range.
     */
    public function getByCapacity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'min' => 'required|integer|min:1',
            'max' => 'nullable|integer|min:1',
        ]);

        $filters = [
            'min_capacity' => $validated['min'],
            'max_capacity' => $validated['max'],
            'search' => $request->input('search'),
            'building' => $request->input('building'),
            'room_type' => $request->input('room_type'),
        ];

        $rooms = $this->roomService->getRooms($filters);

        return response()->success($rooms['data'], $rooms['message'], $rooms['meta']);
    }
}
