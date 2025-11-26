<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConflictDetection\ResolveConflictRequest;
use App\Http\Requests\ConflictDetection\BulkResolveRequest;
use App\Services\ConflictDetectionService;
use App\Models\ConflictDetection;
use App\Models\ConflictRule;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConflictDetectionController extends Controller
{
    protected ConflictDetectionService $conflictService;

    public function __construct(ConflictDetectionService $conflictService)
    {
        $this->conflictService = $conflictService;
    }

    /**
     * Display a listing of conflicts.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'conflict_type' => $request->input('conflict_type'),
            'severity' => $request->input('severity'),
            'status' => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'room_id' => $request->input('room_id'),
            'lecturer_id' => $request->input('lecturer_id'),
            'class_id' => $request->input('class_id'),
            'course_id' => $request->input('course_id'),
            'requires_approval' => $request->input('requires_approval'),
            'auto_resolvable' => $request->input('auto_resolvable'),
            'search' => $request->input('search'),
        ];

        $query = ConflictDetection::with([
            'primarySchedule.course',
            'conflictingSchedule.course',
            'room',
            'lecturer',
            'schoolClass',
            'resolver',
        ]);

        // Apply filters
        if ($filters['conflict_type']) {
            $query->byType($filters['conflict_type']);
        }

        if ($filters['severity']) {
            $query->bySeverity($filters['severity']);
        }

        if ($filters['status']) {
            $query->byStatus($filters['status']);
        }

        if ($filters['date_from']) {
            $query->where('conflict_date', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $query->where('conflict_date', '<=', $filters['date_to']);
        }

        if ($filters['room_id']) {
            $query->where('room_id', $filters['room_id']);
        }

        if ($filters['lecturer_id']) {
            $query->where('lecturer_id', $filters['lecturer_id']);
        }

        if ($filters['class_id']) {
            $query->where('class_id', $filters['class_id']);
        }

        if ($filters['course_id']) {
            $query->where('course_id', $filters['course_id']);
        }

        if ($filters['requires_approval'] !== null) {
            $query->where('requires_approval', $filters['requires_approval']);
        }

        if ($filters['auto_resolvable'] !== null) {
            $query->where('auto_resolvable', $filters['auto_resolvable']);
        }

        if ($filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('conflict_title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('conflict_description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('conflict_id', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Sort by priority if requested
        if ($request->input('sort_by_priority')) {
            $query->byPriority();
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $request->input('per_page', 15);
        $conflicts = $query->paginate($perPage);

        return response()->success($conflicts, 'Conflicts retrieved successfully');
    }

    /**
     * Get conflict details.
     */
    public function show(ConflictDetection $conflict): JsonResponse
    {
        $conflict->load([
            'primarySchedule.course',
            'primarySchedule.lecturer',
            'primarySchedule.room',
            'primarySchedule.schoolClass',
            'conflictingSchedule.course',
            'conflictingSchedule.lecturer',
            'conflictingSchedule.room',
            'conflictingSchedule.schoolClass',
            'room',
            'lecturer',
            'schoolClass',
            'course',
            'programStudy',
            'resolver',
            'creator',
            'escalatedTo',
        ]);

        return response()->success($conflict, 'Conflict details retrieved successfully');
    }

    /**
     * Detect conflicts for a specific schedule.
     */
    public function detectForSchedule(Request $request, Schedule $schedule): JsonResponse
    {
        try {
            $conflicts = $this->conflictService->detectConflictsForSchedule($schedule);

            return response()->success($conflicts, 'Conflict detection completed');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to detect conflicts: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Detect conflicts for multiple schedules.
     */
    public function detectForMultiple(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'schedule_ids' => 'required|array|min:1',
                'schedule_ids.*' => 'required|exists:schedules,id',
            ]);

            $conflicts = $this->conflictService->detectConflictsForMultiple($validated['schedule_ids']);

            return response()->success($conflicts, 'Conflict detection completed');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to detect conflicts: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Detect conflicts across all schedules.
     */
    public function detectAll(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'date_from',
                'date_to',
                'room_id',
                'lecturer_id',
            ]);

            $conflicts = $this->conflictService->detectAllConflicts($filters);

            return response()->success($conflicts, 'Global conflict detection completed');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to detect conflicts: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get conflict statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'date_from',
                'date_to',
            ]);

            $statistics = $this->conflictService->getConflictStatistics($filters);

            return response()->success($statistics, 'Conflict statistics retrieved successfully');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to retrieve conflict statistics: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get high-priority conflicts.
     */
    public function highPriority(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 20);
            $conflicts = $this->conflictService->getHighPriorityConflicts($limit);

            return response()->success($conflicts, 'High-priority conflicts retrieved successfully');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to retrieve high-priority conflicts: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Resolve a conflict.
     */
    public function resolve(ResolveConflictRequest $request, ConflictDetection $conflict): JsonResponse
    {
        try {
            $resolutionData = $request->validated();
            $result = $this->conflictService->resolveConflicts($conflict->primarySchedule, $resolutionData);

            return response()->success($result, 'Conflict resolved successfully');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to resolve conflict: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk resolve conflicts.
     */
    public function bulkResolve(BulkResolveRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $conflictIds = $validated['conflict_ids'];
            $resolutionData = $validated['resolution_data'];

            $resolved = [];
            $failed = [];

            foreach ($conflictIds as $conflictId) {
                $conflict = ConflictDetection::find($conflictId);
                if ($conflict) {
                    try {
                        $result = $this->conflictService->resolveConflicts(
                            $conflict->primarySchedule,
                            $resolutionData
                        );
                        $resolved[] = $result;
                    } catch (\Exception $e) {
                        $failed[] = [
                            'conflict_id' => $conflictId,
                            'error' => $e->getMessage(),
                        ];
                    }
                } else {
                    $failed[] = [
                        'conflict_id' => $conflictId,
                        'error' => 'Conflict not found',
                    ];
                }
            }

            return response()->success([
                'resolved' => $resolved,
                'failed' => $failed,
                'total_processed' => count($conflictIds),
                'success_count' => count($resolved),
            ], 'Bulk conflict resolution completed');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to resolve conflicts: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Ignore a conflict.
     */
    public function ignore(Request $request, ConflictDetection $conflict): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ignore_reason' => 'required|string|max:1000',
            ]);

            $conflict->update([
                'status' => 'ignored',
                'resolution_strategy' => 'ignore',
                'resolution_notes' => $validated['ignore_reason'],
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
            ]);

            return response()->success($conflict, 'Conflict ignored successfully');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to ignore conflict: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Escalate a conflict.
     */
    public function escalate(Request $request, ConflictDetection $conflict): JsonResponse
    {
        try {
            $validated = $request->validate([
                'escalate_to' => 'required|exists:users,id',
                'escalation_reason' => 'required|string|max:1000',
            ]);

            $conflict->update([
                'requires_approval' => true,
                'escalated_to' => $validated['escalate_to'],
                'escalated_at' => now(),
                'escalation_reason' => $validated['escalation_reason'],
                'updated_by' => auth()->id(),
            ]);

            return response()->success($conflict, 'Conflict escalated successfully');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to escalate conflict: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Bulk detect conflicts.
     */
    public function bulkDetect(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'schedule_ids' => 'nullable|array',
                'schedule_ids.*' => 'exists:schedules,id',
            ]);

            $scheduleIds = $validated['schedule_ids'] ?? [];
            $result = $this->conflictService->bulkDetectConflicts($scheduleIds);

            return response()->success($result, 'Bulk conflict detection completed');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to perform bulk conflict detection: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get conflict types.
     */
    public function getConflictTypes(): JsonResponse
    {
        $types = ConflictDetection::CONFLICT_TYPES;

        return response()->success($types, 'Conflict types retrieved successfully');
    }

    /**
     * Get severity levels.
     */
    public function getSeverityLevels(): JsonResponse
    {
        $levels = ConflictDetection::SEVERITY_LEVELS;

        return response()->success($levels, 'Severity levels retrieved successfully');
    }

    /**
     * Get resolution strategies.
     */
    public function getResolutionStrategies(): JsonResponse
    {
        $strategies = ConflictDetection::RESOLUTION_STRATEGIES;

        return response()->success($strategies, 'Resolution strategies retrieved successfully');
    }

    /**
     * Get conflict analytics.
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'period' => 'in:week,month,quarter,year',
                'date_from' => 'date',
                'date_to' => 'date|after_or_equal:date_from',
            ]);

            $period = $validated['period'] ?? 'month';
            $dateFrom = $validated['date_from'] ?? now()->subMonth();
            $dateTo = $validated['date_to'] ?? now();

            $analytics = [
                'conflict_trends' => $this->getConflictTrends($dateFrom, $dateTo, $period),
                'resolution_rates' => $this->getResolutionRates($dateFrom, $dateTo),
                'severity_distribution' => $this->getSeverityDistribution($dateFrom, $dateTo),
                'type_distribution' => $this->getTypeDistribution($dateFrom, $dateTo),
                'top_conflict_sources' => $this->getTopConflictSources($dateFrom, $dateTo),
            ];

            return response()->success($analytics, 'Conflict analytics retrieved successfully');
        } catch (\Exception $e) {
            return response()->error(
                'Failed to retrieve conflict analytics: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    /**
     * Get conflict trends over time.
     */
    private function getConflictTrends($dateFrom, $dateTo, $period): array
    {
        // Implementation for trend analysis
        return [
            'period' => $period,
            'date_range' => [
                'from' => $dateFrom->format('Y-m-d'),
                'to' => $dateTo->format('Y-m-d'),
            ],
            'data' => [], // Would contain actual trend data
        ];
    }

    /**
     * Get resolution rates.
     */
    private function getResolutionRates($dateFrom, $dateTo): array
    {
        $total = ConflictDetection::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $resolved = ConflictDetection::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'resolved')->count();

        return [
            'total_conflicts' => $total,
            'resolved_conflicts' => $resolved,
            'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get severity distribution.
     */
    private function getSeverityDistribution($dateFrom, $dateTo): array
    {
        return ConflictDetection::select('severity', \DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('severity')
            ->pluck('count', 'severity')
            ->toArray();
    }

    /**
     * Get type distribution.
     */
    private function getTypeDistribution($dateFrom, $dateTo): array
    {
        return ConflictDetection::select('conflict_type', \DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('conflict_type')
            ->pluck('count', 'conflict_type')
            ->toArray();
    }

    /**
     * Get top conflict sources.
     */
    private function getTopConflictSources($dateFrom, $dateTo): array
    {
        // Implementation for top conflict sources
        return [
            'rooms' => [], // Top rooms with conflicts
            'lecturers' => [], // Top lecturers with conflicts
            'courses' => [], // Top courses with conflicts
        ];
    }
}