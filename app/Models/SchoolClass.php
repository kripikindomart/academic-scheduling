<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolClass extends Model
{
    use SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'class_name',
        'class_code',
        'program_study_id',
        'academic_year',
        'semester',
        'year_level',
        'capacity',
        'current_students',
        'academic_advisor_id',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'current_students' => 'integer',
        'year_level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
        'deleted_by',
    ];

    // Relationships
    public function programStudy(): BelongsTo
    {
        return $this->belongsTo(ProgramStudy::class);
    }

    public function academicAdvisor(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'academic_advisor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProgram($query, $programStudyId)
    {
        return $query->where('program_study_id', $programStudyId);
    }

    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeByYearLevel($query, $yearLevel)
    {
        return $query->where('year_level', $yearLevel);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('class_name', 'like', "%{$search}%")
              ->orWhere('class_code', 'like', "%{$search}%");
        });
    }

    // Helper methods
    public function isFull(): bool
    {
        return $this->current_students >= $this->capacity;
    }

    public function getAvailableSlots(): int
    {
        return max(0, $this->capacity - $this->current_students);
    }

    public function getOccupancyRate(): float
    {
        return $this->capacity > 0
            ? round(($this->current_students / $this->capacity) * 100, 2)
            : 0;
    }

    public function getDisplayName(): string
    {
        $programName = $this->programStudy?->name ?? 'Unknown';
        return "{$this->class_name} - {$programName}";
    }

    public function getFullIdentifier(): string
    {
        return "{$this->class_code} ({$this->academic_year} - {$this->semester})";
    }
}
