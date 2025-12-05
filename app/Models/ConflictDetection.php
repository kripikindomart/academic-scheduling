<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ConflictDetection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conflict_id',
        'conflict_type',
        'primary_schedule_id',
        'conflicting_schedule_id',
        'conflicting_schedules',
        'conflict_title',
        'conflict_description',
        'severity',
        'status',
        'room_id',
        'lecturer_id',
        'class_id',
        'course_id',
        'program_study_id',
        'conflict_date',
        'conflict_start_time',
        'conflict_end_time',
        'conflict_duration_minutes',
        'detection_method',
        'detection_rules',
        'conflict_data',
        'resolution_strategy',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
        'original_schedule_data',
        'resolution_data',
        'is_resolution_permanent',
        'affected_students_count',
        'impact_score',
        'affected_stakeholders',
        'requires_approval',
        'escalated_to',
        'escalated_at',
        'escalation_reason',
        'notification_recipients',
        'notification_history',
        'notifications_sent',
        'auto_resolvable',
        'auto_resolution_rules',
        'resolution_priority',
        'prevention_rules',
        'recurring_conflict',
        'recurrence_pattern',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'conflicting_schedules' => 'array',
        'detection_rules' => 'array',
        'conflict_data' => 'array',
        'original_schedule_data' => 'array',
        'resolution_data' => 'array',
        'affected_stakeholders' => 'array',
        'notification_recipients' => 'array',
        'notification_history' => 'array',
        'auto_resolution_rules' => 'array',
        'prevention_rules' => 'array',
        'impact_score' => 'decimal:2',
        'conflict_date' => 'date',
        'conflict_start_time' => 'datetime',
        'conflict_end_time' => 'datetime',
        'resolved_at' => 'datetime',
        'escalated_at' => 'datetime',
        'auto_resolvable' => 'boolean',
        'is_resolution_permanent' => 'boolean',
        'requires_approval' => 'boolean',
        'notifications_sent' => 'boolean',
        'recurring_conflict' => 'boolean',
    ];

    // Conflict types
    const CONFLICT_TYPES = [
        'room_conflict' => 'Room Conflict',
        'lecturer_conflict' => 'Lecturer Conflict',
        'class_conflict' => 'Class Conflict',
        'time_slot_conflict' => 'Time Slot Conflict',
        'capacity_conflict' => 'Capacity Conflict',
        'facility_conflict' => 'Facility Conflict',
        'prerequisite_conflict' => 'Prerequisite Conflict',
        'academic_load_conflict' => 'Academic Load Conflict',
    ];

    // Severity levels
    const SEVERITY_LEVELS = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    // Status options
    const STATUS_OPTIONS = [
        'detected' => 'Detected',
        'reviewing' => 'Reviewing',
        'resolved' => 'Resolved',
        'ignored' => 'Ignored',
    ];

    // Resolution strategies
    const RESOLUTION_STRATEGIES = [
        'none' => 'None',
        'reschedule_primary' => 'Reschedule Primary',
        'reschedule_conflicting' => 'Reschedule Conflicting',
        'change_room' => 'Change Room',
        'change_lecturer' => 'Change Lecturer',
        'change_class' => 'Change Class',
        'adjust_time' => 'Adjust Time',
        'override' => 'Override',
        'manual_resolution' => 'Manual Resolution',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate unique conflict ID
        static::creating(function ($conflict) {
            if (empty($conflict->conflict_id)) {
                $conflict->conflict_id = 'CONFL-' . date('Ymd') . '-' . strtoupper(uniqid());
            }

            // Set user who created the conflict
            if (auth()->check() && !$conflict->created_by) {
                $conflict->created_by = auth()->id();
            }
        });

        // Update modification tracking
        static::updating(function ($conflict) {
            if (auth()->check()) {
                $conflict->updated_by = auth()->id();
            }
        });

        // Soft delete tracking
        static::deleting(function ($conflict) {
            if (auth()->check() && !$conflict->isForceDeleting()) {
                $conflict->deleted_by = auth()->id();
                $conflict->save();
            }
        });
    }

    /**
     * Get the primary schedule that caused the conflict.
     */
    public function primarySchedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'primary_schedule_id');
    }

    /**
     * Get the conflicting schedule.
     */
    public function conflictingSchedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'conflicting_schedule_id');
    }

    /**
     * Get the room associated with the conflict.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the lecturer associated with the conflict.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    /**
     * Get the class associated with the conflict.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    /**
     * Get the course associated with the conflict.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the program study associated with the conflict.
     */
    public function programStudy(): BelongsTo
    {
        return $this->belongsTo(ProgramStudy::class);
    }

    /**
     * Get the user who resolved the conflict.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get the user who escalated the conflict.
     */
    public function escalatedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    /**
     * Get the user who created the conflict.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the conflict.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope conflicts by type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('conflict_type', $type);
    }

    /**
     * Scope conflicts by severity.
     */
    public function scopeBySeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope conflicts by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope conflicts by date range.
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('conflict_date', [$startDate, $endDate]);
    }

    /**
     * Scope conflicts that require approval.
     */
    public function scopeRequiresApproval(Builder $query): Builder
    {
        return $query->where('requires_approval', true);
    }

    /**
     * Scope auto-resolvable conflicts.
     */
    public function scopeAutoResolvable(Builder $query): Builder
    {
        return $query->where('auto_resolvable', true);
    }

    /**
     * Scope unresolved conflicts.
     */
    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->whereIn('status', ['detected', 'reviewing']);
    }

    /**
     * Scope conflicts by priority (highest first).
     */
    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE severity
                WHEN 'critical' THEN 1
                WHEN 'high' THEN 2
                WHEN 'medium' THEN 3
                WHEN 'low' THEN 4
            END
        ")->orderBy('resolution_priority', 'desc');
    }

    /**
     * Scope conflicts by impact score.
     */
    public function scopeHighImpact(Builder $query, float $threshold = 50.0): Builder
    {
        return $query->where('impact_score', '>=', $threshold);
    }

    /**
     * Scope recurring conflicts.
     */
    public function scopeRecurring(Builder $query): Builder
    {
        return $query->where('recurring_conflict', true);
    }

    /**
     * Get formatted conflict duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->conflict_duration_minutes;
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $remainingMinutes);
        }

        return sprintf('%dm', $remainingMinutes);
    }

    /**
     * Get conflict severity badge color.
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get conflict status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'detected' => 'red',
            'reviewing' => 'yellow',
            'resolved' => 'green',
            'ignored' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Check if conflict is resolved.
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if conflict requires attention.
     */
    public function requiresAttention(): bool
    {
        return in_array($this->status, ['detected', 'reviewing']) &&
               in_array($this->severity, ['high', 'critical']);
    }

    /**
     * Calculate impact score based on various factors.
     */
    public function calculateImpactScore(): float
    {
        $score = 0;

        // Base score from severity
        $severityScores = [
            'low' => 10,
            'medium' => 25,
            'high' => 50,
            'critical' => 100
        ];
        $score += $severityScores[$this->severity] ?? 0;

        // Add affected students impact
        $score += min($this->affected_students_count * 0.5, 20);

        // Add duration impact
        $score += min($this->conflict_duration_minutes * 0.1, 10);

        // Add conflict type weighting
        $typeWeights = [
            'room_conflict' => 15,
            'lecturer_conflict' => 20,
            'class_conflict' => 25,
            'time_slot_conflict' => 10,
            'capacity_conflict' => 15,
            'facility_conflict' => 10,
            'prerequisite_conflict' => 30,
            'academic_load_conflict' => 25
        ];
        $score += $typeWeights[$this->conflict_type] ?? 10;

        // Apply resolution priority modifier
        $score *= (1 + ($this->resolution_priority / 10));

        return min(round($score, 2), 100);
    }

    /**
     * Generate conflict summary.
     */
    public function getConflictSummaryAttribute(): string
    {
        return sprintf(
            "%s: %s between %s and %s on %s at %s",
            strtoupper($this->conflict_type),
            $this->conflict_title,
            $this->primarySchedule?->course->course_name ?? 'Unknown',
            $this->conflictingSchedule?->course->course_name ?? 'Unknown',
            $this->conflict_date->format('M j, Y'),
            $this->conflict_start_time->format('H:i')
        );
    }

    /**
     * Get suggested resolutions.
     */
    public function getSuggestedResolutionsAttribute(): array
    {
        $suggestions = [];

        switch ($this->conflict_type) {
            case 'room_conflict':
                $suggestions = [
                    'change_room' => 'Move to available room',
                    'adjust_time' => 'Adjust schedule time',
                    'reschedule_primary' => 'Reschedule primary schedule'
                ];
                break;
            case 'lecturer_conflict':
                $suggestions = [
                    'change_lecturer' => 'Assign different lecturer',
                    'adjust_time' => 'Adjust schedule time',
                    'reschedule_conflicting' => 'Reschedule conflicting schedule'
                ];
                break;
            case 'class_conflict':
                $suggestions = [
                    'change_class' => 'Assign to different class',
                    'reschedule_primary' => 'Reschedule primary schedule',
                    'override' => 'Override conflict (if allowed)'
                ];
                break;
            default:
                $suggestions = [
                    'manual_resolution' => 'Manual resolution required',
                    'escalate' => 'Escalate to administrator'
                ];
        }

        return $suggestions;
    }
}