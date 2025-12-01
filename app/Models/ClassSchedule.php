<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'schedule_code',
        'title',
        'program_study_id',
        'class_id',
        'academic_year_id',
        'semester',
        'online_percentage',
        'offline_percentage',
        'description',
        'status',
        'created_by',
        'updated_by',
        'created_from_ip',
        'user_agent',
    ];

    protected $casts = [
        'online_percentage' => 'decimal:2',
        'offline_percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function programStudy(): BelongsTo
    {
        return $this->belongsTo(ProgramStudy::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ClassScheduleDetail::class, 'class_schedule_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_schedule_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_study_id', $programId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    // Helper methods
    public function getFormattedPercentage(): string
    {
        return "Online: {$this->online_percentage}%, Offline: {$this->offline_percentage}%";
    }

    public function getTotalCourses(): int
    {
        return $this->details()->count();
    }

    public function getTotalGeneratedSchedules(): int
    {
        return $this->schedules()->count();
    }

    public function canBeGenerated(): bool
    {
        return $this->details()->count() > 0 && $this->status !== 'completed';
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'active' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }
}