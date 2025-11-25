<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_code',
        'course_name',
        'description',
        'credits',
        'semester',
        'academic_year',
        'course_type',
        'level',
        'capacity',
        'current_enrollment',
        'is_active',
        'program_study_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'credits' => 'integer',
        'capacity' => 'integer',
        'current_enrollment' => 'integer',
        'is_active' => 'boolean',
    ];

    public function programStudy()
    {
        return $this->belongsTo(ProgramStudy::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function lecturers()
    {
        return $this->belongsToMany(User::class, 'course_lecturers')
            ->withPivot('role', 'assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    public function prerequisites()
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_course_id')
            ->withTimestamps();
    }

    public function dependents()
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'prerequisite_course_id', 'course_id')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_study_id', $programId);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('course_type', $type);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->capacity - $this->current_enrollment;
    }

    public function isFull()
    {
        return $this->current_enrollment >= $this->capacity;
    }

    public function hasPrerequisites()
    {
        return $this->prerequisites()->count() > 0;
    }

    public function getPrerequisiteCodes()
    {
        return $this->prerequisites()->pluck('course_code')->toArray();
    }
}
