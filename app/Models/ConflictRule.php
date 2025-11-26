<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ConflictRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rule_code',
        'rule_name',
        'rule_description',
        'rule_category',
        'conflict_type',
        'severity',
        'is_active',
        'is_blocking',
        'auto_resolvable',
        'priority_score',
        'conditions',
        'parameters',
        'validation_logic',
        'applicable_to',
        'exceptions',
        'schedule_filters',
        'is_time_sensitive',
        'time_constraints',
        'recurrence_patterns',
        'detection_method',
        'detection_algorithm',
        'detection_threshold',
        'resolution_strategies',
        'resolution_priorities',
        'resolution_constraints',
        'auto_resolution_rules',
        'resolution_templates',
        'requires_approval',
        'notification_rules',
        'escalation_rules',
        'notification_templates',
        'prevention_rules',
        'prevention_suggestions',
        'enforce_prevention',
        'compliance_category',
        'compliance_rules',
        'is_mandatory',
        'times_triggered',
        'times_resolved',
        'success_rate',
        'last_triggered_at',
        'depends_on',
        'conflicts_with',
        'overrides',
        'version',
        'change_log',
        'effective_from',
        'effective_to',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'parameters' => 'array',
        'applicable_to' => 'array',
        'exceptions' => 'array',
        'schedule_filters' => 'array',
        'time_constraints' => 'array',
        'recurrence_patterns' => 'array',
        'detection_algorithm' => 'array',
        'resolution_strategies' => 'array',
        'resolution_priorities' => 'array',
        'resolution_constraints' => 'array',
        'auto_resolution_rules' => 'array',
        'resolution_templates' => 'array',
        'notification_rules' => 'array',
        'escalation_rules' => 'array',
        'notification_templates' => 'array',
        'prevention_rules' => 'array',
        'prevention_suggestions' => 'array',
        'compliance_rules' => 'array',
        'depends_on' => 'array',
        'conflicts_with' => 'array',
        'overrides' => 'array',
        'change_log' => 'array',
        'is_active' => 'boolean',
        'is_blocking' => 'boolean',
        'auto_resolvable' => 'boolean',
        'is_time_sensitive' => 'boolean',
        'requires_approval' => 'boolean',
        'enforce_prevention' => 'boolean',
        'is_mandatory' => 'boolean',
        'times_triggered' => 'integer',
        'times_resolved' => 'integer',
        'success_rate' => 'decimal:2',
        'priority_score' => 'integer',
        'detection_threshold' => 'integer',
        'last_triggered_at' => 'datetime',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
    ];

    // Rule categories
    const RULE_CATEGORIES = [
        'room_scheduling' => 'Room Scheduling',
        'lecturer_availability' => 'Lecturer Availability',
        'class_scheduling' => 'Class Scheduling',
        'time_slot_management' => 'Time Slot Management',
        'capacity_constraints' => 'Capacity Constraints',
        'facility_requirements' => ' Facility Requirements',
        'academic_constraints' => 'Academic Constraints',
        'business_rules' => 'Business Rules',
        'compliance_rules' => 'Compliance Rules',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate unique rule code
        static::creating(function ($rule) {
            if (empty($rule->rule_code)) {
                $rule->rule_code = 'RULE-' . strtoupper(uniqid());
            }

            // Set user who created the rule
            if (auth()->check() && !$rule->created_by) {
                $rule->created_by = auth()->id();
            }
        });

        // Update modification tracking
        static::updating(function ($rule) {
            if (auth()->check()) {
                $rule->updated_by = auth()->id();
            }
        });
    }

    /**
     * Get conflict detections triggered by this rule.
     */
    public function conflictDetections(): HasMany
    {
        return $this->hasMany(ConflictDetection::class, 'rule_id');
    }

    /**
     * Get the user who created the rule.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the rule.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope active rules.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope blocking rules.
     */
    public function scopeBlocking(Builder $query): Builder
    {
        return $query->where('is_blocking', true);
    }

    /**
     * Scope auto-resolvable rules.
     */
    public function scopeAutoResolvable(Builder $query): Builder
    {
        return $query->where('auto_resolvable', true);
    }

    /**
     * Scope rules by category.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('rule_category', $category);
    }

    /**
     * Scope rules by conflict type.
     */
    public function scopeByConflictType(Builder $query, string $conflictType): Builder
    {
        return $query->where('conflict_type', $conflictType);
    }

    /**
     * Scope rules by severity.
     */
    public function scopeBySeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope mandatory rules.
     */
    public function scopeMandatory(Builder $query): Builder
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope rules by priority (highest first).
     */
    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderBy('priority_score', 'desc');
    }

    /**
     * Scope rules that are currently effective.
     */
    public function scopeEffective(Builder $query): Builder
    {
        $now = now();

        return $query->where(function ($query) use ($now) {
            $query->whereNull('effective_from')
                  ->orWhere('effective_from', '<=', $now);
        })->where(function ($query) use ($now) {
            $query->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', $now);
        });
    }

    /**
     * Check if rule is currently effective.
     */
    public function isEffective(): bool
    {
        $now = now();

        $effectiveFrom = $this->effective_from;
        $effectiveTo = $this->effective_to;

        if ($effectiveFrom && $effectiveFrom->isFuture()) {
            return false;
        }

        if ($effectiveTo && $effectiveTo->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if rule applies to specific schedule.
     */
    public function appliesTo(Schedule $schedule): bool
    {
        if (!$this->is_active || !$this->isEffective()) {
            return false;
        }

        // Check applicable_to filters
        if ($this->applicable_to) {
            // Implement specific filtering logic based on applicable_to array
        }

        // Check exceptions
        if ($this->exceptions && $this->isException($schedule)) {
            return false;
        }

        return true;
    }

    /**
     * Check if schedule is an exception to this rule.
     */
    private function isException(Schedule $schedule): bool
    {
        // Implement exception checking logic
        return false;
    }

    /**
     * Evaluate rule against schedule.
     */
    public function evaluate(Schedule $schedule): array
    {
        if (!$this->appliesTo($schedule)) {
            return ['conflict_detected' => false];
        }

        // Implement rule evaluation logic based on conditions
        $conflictDetected = $this->checkConditions($schedule);

        if ($conflictDetected) {
            $this->increment('times_triggered');
            $this->update(['last_triggered_at' => now()]);
        }

        return [
            'conflict_detected' => $conflictDetected,
            'rule_code' => $this->rule_code,
            'severity' => $this->severity,
            'resolution_strategies' => $this->resolution_strategies,
            'requires_approval' => $this->requires_approval,
        ];
    }

    /**
     * Check rule conditions against schedule.
     */
    private function checkConditions(Schedule $schedule): bool
    {
        if (!$this->conditions) {
            return false;
        }

        // Implement condition checking logic
        // This would parse the conditions JSON and evaluate against schedule data

        return false; // Placeholder
    }

    /**
     * Get rule performance metrics.
     */
    public function getPerformanceMetrics(): array
    {
        $totalTriggers = $this->times_triggered;
        $totalResolved = $this->times_resolved;
        $successRate = $totalTriggers > 0 ? ($totalResolved / $totalTriggers) * 100 : 0;

        return [
            'total_triggers' => $totalTriggers,
            'total_resolved' => $totalResolved,
            'success_rate' => round($successRate, 2),
            'last_triggered' => $this->last_triggered_at?->diffForHumans(),
            'avg_resolution_time' => $this->calculateAverageResolutionTime(),
        ];
    }

    /**
     * Calculate average resolution time for conflicts triggered by this rule.
     */
    private function calculateAverageResolutionTime(): string
    {
        // Calculate average time between detection and resolution
        // This would query the conflict_detections table

        return 'N/A'; // Placeholder
    }

    /**
     * Get rule severity badge color.
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
     * Get rule status badge.
     */
    public function getStatusBadge(): string
    {
        if (!$this->is_active) {
            return ['text' => 'Inactive', 'color' => 'gray'];
        }

        if (!$this->isEffective()) {
            return ['text' => 'Not Effective', 'color' => 'yellow'];
        }

        return ['text' => 'Active', 'color' => 'green'];
    }

    /**
     * Create rule from template.
     */
    public static function createFromTemplate(string $template, array $overrides = []): self
    {
        $templates = [
            'room_double_booking' => [
                'rule_code' => 'ROOM-001',
                'rule_name' => 'Room Double Booking Prevention',
                'rule_description' => 'Prevents scheduling multiple classes in the same room at the same time',
                'rule_category' => 'room_scheduling',
                'conflict_type' => 'room_conflict',
                'severity' => 'high',
                'is_blocking' => true,
                'priority_score' => 90,
            ],
            'lecturer_overload' => [
                'rule_code' => 'LECT-001',
                'rule_name' => 'Lecturer Workload Limit',
                'rule_description' => 'Prevents assigning more than maximum allowed credit hours to a lecturer',
                'rule_category' => 'lecturer_availability',
                'conflict_type' => 'academic_load_conflict',
                'severity' => 'medium',
                'is_blocking' => true,
                'priority_score' => 70,
            ],
            'class_capacity' => [
                'rule_code' => 'CLASS-001',
                'rule_name' => 'Class Capacity Limit',
                'rule_description' => 'Ensures room capacity can accommodate class size',
                'rule_category' => 'capacity_constraints',
                'conflict_type' => 'capacity_conflict',
                'severity' => 'medium',
                'is_blocking' => true,
                'priority_score' => 60,
            ],
        ];

        if (!isset($templates[$template])) {
            throw new \InvalidArgumentException("Unknown template: {$template}");
        }

        $data = array_merge($templates[$template], $overrides);
        return self::create($data);
    }
}