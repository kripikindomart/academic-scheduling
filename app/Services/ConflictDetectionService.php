<?php

namespace App\Services;

use App\Models\ConflictDetection;
use App\Models\ConflictRule;
use App\Models\Schedule;
use App\Models\Room;
use App\Models\Lecturer;
use App\Models\SchoolClass;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ConflictDetectionService
{
    /**
     * Detect conflicts for a specific schedule.
     */
    public function detectConflictsForSchedule(Schedule $schedule): array
    {
        $conflicts = [];
        $rules = ConflictRule::active()->effective()->get();

        foreach ($rules as $rule) {
            if ($rule->appliesTo($schedule)) {
                $result = $this->evaluateRule($rule, $schedule);
                if ($result['conflict_detected']) {
                    $conflicts[] = $this->createConflictDetection($rule, $schedule, $result);
                }
            }
        }

        // Run specific conflict detection methods
        $conflicts = array_merge($conflicts, $this->detectRoomConflicts($schedule));
        $conflicts = array_merge($conflicts, $this->detectLecturerConflicts($schedule));
        $conflicts = array_merge($conflicts, $this->detectClassConflicts($schedule));
        $conflicts = array_merge($conflicts, $this->detectCapacityConflicts($schedule));
        $conflicts = array_merge($conflicts, $this->detectTimeSlotConflicts($schedule));

        return $conflicts;
    }

    /**
     * Detect conflicts for multiple schedules.
     */
    public function detectConflictsForMultiple(array $scheduleIds): array
    {
        $allConflicts = [];
        $schedules = Schedule::whereIn('id', $scheduleIds)->get();

        foreach ($schedules as $schedule) {
            $conflicts = $this->detectConflictsForSchedule($schedule);
            $allConflicts = array_merge($allConflicts, $conflicts);
        }

        return $allConflicts;
    }

    /**
     * Check for conflicts across all schedules.
     */
    public function detectAllConflicts(array $filters = []): Collection
    {
        $schedules = Schedule::query();

        if (isset($filters['date_from'])) {
            $schedules->where('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $schedules->where('date', '<=', $filters['date_to']);
        }

        if (isset($filters['room_id'])) {
            $schedules->where('room_id', $filters['room_id']);
        }

        if (isset($filters['lecturer_id'])) {
            $schedules->where('lecturer_id', $filters['lecturer_id']);
        }

        $schedules = $schedules->get();
        $allConflicts = collect();

        foreach ($schedules as $schedule) {
            $conflicts = $this->detectConflictsForSchedule($schedule);
            $allConflicts = $allConflicts->merge($conflicts);
        }

        return $allConflicts;
    }

    /**
     * Detect room conflicts.
     */
    private function detectRoomConflicts(Schedule $schedule): array
    {
        $conflicts = [];
        $conflictingSchedules = Schedule::where('room_id', $schedule->room_id)
            ->where('date', $schedule->date)
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($schedule) {
                $query->where(function ($q) use ($schedule) {
                    // Schedule starts during another schedule
                    $q->where('start_time', '<=', $schedule->start_time)
                      ->where('end_time', '>', $schedule->start_time);
                })->orWhere(function ($q) use ($schedule) {
                    // Schedule ends during another schedule
                    $q->where('start_time', '<', $schedule->end_time)
                      ->where('end_time', '>=', $schedule->end_time);
                })->orWhere(function ($q) use ($schedule) {
                    // Schedule completely contains another schedule
                    $q->where('start_time', '>=', $schedule->start_time)
                      ->where('end_time', '<=', $schedule->end_time);
                });
            })
            ->get();

        foreach ($conflictingSchedules as $conflictingSchedule) {
            $conflicts[] = $this->createConflictDetection(
                $this->getOrCreateRule('room_conflict'),
                $schedule,
                [
                    'conflict_type' => 'room_conflict',
                    'severity' => 'high',
                    'conflicting_schedule_id' => $conflictingSchedule->id,
                    'conflict_title' => 'Room Double Booking',
                    'conflict_description' => sprintf(
                        "Room %s is already booked by %s from %s to %s",
                        $schedule->room->name,
                        $conflictingSchedule->course->course_name,
                        $conflictingSchedule->start_time->format('H:i'),
                        $conflictingSchedule->end_time->format('H:i')
                    ),
                    'impact_score' => $this->calculateImpactScore($schedule, $conflictingSchedule),
                ]
            );
        }

        return $conflicts;
    }

    /**
     * Detect lecturer conflicts.
     */
    private function detectLecturerConflicts(Schedule $schedule): array
    {
        $conflicts = [];
        $conflictingSchedules = Schedule::where('lecturer_id', $schedule->lecturer_id)
            ->where('date', $schedule->date)
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($schedule) {
                $query->where(function ($q) use ($schedule) {
                    $q->where('start_time', '<=', $schedule->start_time)
                      ->where('end_time', '>', $schedule->start_time);
                })->orWhere(function ($q) use ($schedule) {
                    $q->where('start_time', '<', $schedule->end_time)
                      ->where('end_time', '>=', $schedule->end_time);
                });
            })
            ->get();

        foreach ($conflictingSchedules as $conflictingSchedule) {
            $conflicts[] = $this->createConflictDetection(
                $this->getOrCreateRule('lecturer_conflict'),
                $schedule,
                [
                    'conflict_type' => 'lecturer_conflict',
                    'severity' => 'high',
                    'conflicting_schedule_id' => $conflictingSchedule->id,
                    'conflict_title' => 'Lecturer Double Assignment',
                    'conflict_description' => sprintf(
                        "Lecturer %s is already assigned to %s from %s to %s",
                        $schedule->lecturer->name,
                        $conflictingSchedule->course->course_name,
                        $conflictingSchedule->start_time->format('H:i'),
                        $conflictingSchedule->end_time->format('H:i')
                    ),
                    'impact_score' => $this->calculateImpactScore($schedule, $conflictingSchedule),
                ]
            );
        }

        return $conflicts;
    }

    /**
     * Detect class conflicts.
     */
    private function detectClassConflicts(Schedule $schedule): array
    {
        $conflicts = [];

        if (!$schedule->class_id) {
            return $conflicts;
        }

        $conflictingSchedules = Schedule::where('class_id', $schedule->class_id)
            ->where('date', $schedule->date)
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($schedule) {
                $query->where(function ($q) use ($schedule) {
                    $q->where('start_time', '<=', $schedule->start_time)
                      ->where('end_time', '>', $schedule->start_time);
                })->orWhere(function ($q) use ($schedule) {
                    $q->where('start_time', '<', $schedule->end_time)
                      ->where('end_time', '>=', $schedule->end_time);
                });
            })
            ->get();

        foreach ($conflictingSchedules as $conflictingSchedule) {
            $conflicts[] = $this->createConflictDetection(
                $this->getOrCreateRule('class_conflict'),
                $schedule,
                [
                    'conflict_type' => 'class_conflict',
                    'severity' => 'high',
                    'conflicting_schedule_id' => $conflictingSchedule->id,
                    'conflict_title' => 'Class Double Assignment',
                    'conflict_description' => sprintf(
                        "Class %s is already assigned to %s from %s to %s",
                        $schedule->schoolClass->class_name,
                        $conflictingSchedule->course->course_name,
                        $conflictingSchedule->start_time->format('H:i'),
                        $conflictingSchedule->end_time->format('H:i')
                    ),
                    'impact_score' => $this->calculateImpactScore($schedule, $conflictingSchedule),
                ]
            );
        }

        return $conflicts;
    }

    /**
     * Detect capacity conflicts.
     */
    private function detectCapacityConflicts(Schedule $schedule): array
    {
        $conflicts = [];

        if (!$schedule->room_id || !$schedule->class_id) {
            return $conflicts;
        }

        $class = $schedule->schoolClass;
        $room = $schedule->room;

        if ($class->current_students > $room->capacity) {
            $conflicts[] = $this->createConflictDetection(
                $this->getOrCreateRule('capacity_conflict'),
                $schedule,
                [
                    'conflict_type' => 'capacity_conflict',
                    'severity' => 'medium',
                    'conflict_title' => 'Room Capacity Exceeded',
                    'conflict_description' => sprintf(
                        "Class %s has %d students but room %s only has capacity for %d",
                        $class->class_name,
                        $class->current_students,
                        $room->name,
                        $room->capacity
                    ),
                    'affected_students_count' => $class->current_students,
                    'impact_score' => 50 + (($class->current_students - $room->capacity) * 2),
                ]
            );
        }

        return $conflicts;
    }

    /**
     * Detect time slot conflicts.
     */
    private function detectTimeSlotConflicts(Schedule $schedule): array
    {
        $conflicts = [];

        // Check for minimum time gap between consecutive classes in same room
        $minGapMinutes = 15; // Configurable minimum gap

        $previousSchedule = Schedule::where('room_id', $schedule->room_id)
            ->where('date', $schedule->date)
            ->where('end_time', '<=', $schedule->start_time)
            ->orderBy('end_time', 'desc')
            ->first();

        if ($previousSchedule) {
            $timeDiff = $schedule->start_time->diffInMinutes($previousSchedule->end_time);
            if ($timeDiff < $minGapMinutes) {
                $conflicts[] = $this->createConflictDetection(
                    $this->getOrCreateRule('time_slot_conflict'),
                    $schedule,
                    [
                        'conflict_type' => 'time_slot_conflict',
                        'severity' => 'low',
                        'conflict_title' => 'Insufficient Time Gap',
                        'conflict_description' => sprintf(
                            "Only %d minutes between %s and %s (minimum %d minutes required)",
                            $timeDiff,
                            $previousSchedule->course->course_name,
                            $schedule->course->course_name,
                            $minGapMinutes
                        ),
                        'impact_score' => 25,
                    ]
                );
            }
        }

        return $conflicts;
    }

    /**
     * Evaluate a specific rule against a schedule.
     */
    private function evaluateRule(ConflictRule $rule, Schedule $schedule): array
    {
        // This would implement the rule evaluation logic
        // For now, return a basic structure
        return [
            'conflict_detected' => false,
            'rule_code' => $rule->rule_code,
            'severity' => $rule->severity,
        ];
    }

    /**
     * Create a conflict detection record.
     */
    private function createConflictDetection(ConflictRule $rule, Schedule $schedule, array $data): ConflictDetection
    {
        $conflictData = array_merge([
            'conflict_type' => $data['conflict_type'] ?? $rule->conflict_type,
            'primary_schedule_id' => $schedule->id,
            'room_id' => $schedule->room_id,
            'lecturer_id' => $schedule->lecturer_id,
            'class_id' => $schedule->class_id,
            'course_id' => $schedule->course_id,
            'program_study_id' => $schedule->program_study_id,
            'conflict_date' => $schedule->date,
            'conflict_start_time' => $schedule->start_time,
            'conflict_end_time' => $schedule->end_time,
            'conflict_duration_minutes' => $schedule->start_time->diffInMinutes($schedule->end_time),
            'severity' => $data['severity'] ?? $rule->severity,
            'status' => 'detected',
            'detection_method' => 'automated',
            'requires_approval' => $rule->requires_approval,
            'auto_resolvable' => $rule->auto_resolvable,
            'resolution_priority' => $rule->priority_score,
            'detection_rules' => [$rule->rule_code],
            'created_by' => auth()->id(),
        ], $data);

        return ConflictDetection::create($conflictData);
    }

    /**
     * Get or create a rule for conflict type.
     */
    private function getOrCreateRule(string $conflictType): ConflictRule
    {
        $rule = ConflictRule::where('conflict_type', $conflictType)->first();

        if (!$rule) {
            $rule = ConflictRule::createFromTemplate("{$conflictType}_template");
        }

        return $rule;
    }

    /**
     * Calculate impact score for conflict.
     */
    private function calculateImpactScore(Schedule $schedule, Schedule $conflictingSchedule): float
    {
        $score = 0;

        // Base score from severity
        $score += 50;

        // Add student impact
        $primaryStudents = $schedule->schoolClass?->current_students ?? 0;
        $conflictingStudents = $conflictingSchedule->schoolClass?->current_students ?? 0;
        $totalAffectedStudents = $primaryStudents + $conflictingStudents;
        $score += min($totalAffectedStudents * 0.5, 30);

        // Add duration impact
        $duration = $schedule->start_time->diffInMinutes($schedule->end_time);
        $score += min($duration * 0.1, 10);

        // Add course level impact
        $courseWeights = [
            'undergraduate' => 1,
            'graduate' => 1.5,
            'doctoral' => 2
        ];
        $courseLevel = $schedule->course->level ?? 'undergraduate';
        $score *= $courseWeights[$courseLevel] ?? 1;

        return min(round($score, 2), 100);
    }

    /**
     * Resolve conflicts for a schedule.
     */
    public function resolveConflicts(Schedule $schedule, array $resolutionData): array
    {
        $conflicts = ConflictDetection::where('primary_schedule_id', $schedule->id)
            ->where('status', '!=', 'resolved')
            ->get();

        $resolved = [];
        $failed = [];

        foreach ($conflicts as $conflict) {
            try {
                $result = $this->applyResolution($conflict, $resolutionData);
                $resolved[] = $result;
            } catch (\Exception $e) {
                $failed[] = [
                    'conflict_id' => $conflict->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'resolved' => $resolved,
            'failed' => $failed,
            'total_processed' => count($conflicts),
        ];
    }

    /**
     * Apply resolution to a conflict.
     */
    private function applyResolution(ConflictDetection $conflict, array $resolutionData): ConflictDetection
    {
        $resolutionStrategy = $resolutionData['resolution_strategy'] ?? 'manual_resolution';
        $resolutionNotes = $resolutionData['resolution_notes'] ?? null;

        $conflict->update([
            'resolution_strategy' => $resolutionStrategy,
            'resolution_notes' => $resolutionNotes,
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
            'resolution_data' => $resolutionData,
        ]);

        return $conflict;
    }

    /**
     * Get conflict statistics.
     */
    public function getConflictStatistics(array $filters = []): array
    {
        $query = ConflictDetection::query();

        if (isset($filters['date_from'])) {
            $query->where('conflict_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('conflict_date', '<=', $filters['date_to']);
        }

        $totalConflicts = $query->count();
        $resolvedConflicts = $query->where('status', 'resolved')->count();
        $unresolvedConflicts = $totalConflicts - $resolvedConflicts;

        $conflictsByType = ConflictDetection::select('conflict_type', DB::raw('count(*) as count'))
            ->groupBy('conflict_type')
            ->pluck('count', 'conflict_type')
            ->toArray();

        $conflictsBySeverity = ConflictDetection::select('severity', DB::raw('count(*) as count'))
            ->groupBy('severity')
            ->pluck('count', 'severity')
            ->toArray();

        $recentConflicts = ConflictDetection::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_conflicts' => $totalConflicts,
            'resolved_conflicts' => $resolvedConflicts,
            'unresolved_conflicts' => $unresolvedConflicts,
            'resolution_rate' => $totalConflicts > 0 ? round(($resolvedConflicts / $totalConflicts) * 100, 2) : 0,
            'conflicts_by_type' => $conflictsByType,
            'conflicts_by_severity' => $conflictsBySeverity,
            'recent_conflicts' => $recentConflicts,
        ];
    }

    /**
     * Get high-priority conflicts.
     */
    public function getHighPriorityConflicts(int $limit = 20): Collection
    {
        return ConflictDetection::unresolved()
            ->byPriority()
            ->highImpact()
            ->limit($limit)
            ->with(['primarySchedule.course', 'conflictingSchedule.course', 'room'])
            ->get();
    }

    /**
     * Bulk detect conflicts for all schedules.
     */
    public function bulkDetectConflicts(array $scheduleIds = []): array
    {
        if (empty($scheduleIds)) {
            // Process all active schedules
            $scheduleIds = Schedule::pluck('id')->toArray();
        }

        $processed = 0;
        $conflictsFound = 0;
        $errors = [];

        // Process in batches to avoid memory issues
        $batchSize = 100;
        $batches = array_chunk($scheduleIds, $batchSize);

        foreach ($batches as $batch) {
            try {
                $schedules = Schedule::whereIn('id', $batch)->get();

                foreach ($schedules as $schedule) {
                    $conflicts = $this->detectConflictsForSchedule($schedule);
                    $processed++;
                    $conflictsFound += count($conflicts);
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'batch' => $batch,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'processed_schedules' => $processed,
            'conflicts_found' => $conflictsFound,
            'errors' => $errors,
        ];
    }
}