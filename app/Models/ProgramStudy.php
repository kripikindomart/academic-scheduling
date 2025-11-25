<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramStudy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'faculty',
        'level',
        'degree',
        'duration_years',
        'minimum_credits',
        'head_of_program',
        'email',
        'phone',
        'office_location',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'duration_years' => 'integer',
        'minimum_credits' => 'integer',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function lecturers()
    {
        return $this->belongsToMany(User::class, 'program_lecturers')
            ->withPivot('role', 'assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasManyThrough(Schedule::class, Course::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByFaculty($query, $faculty)
    {
        return $query->where('faculty', $faculty);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByDegree($query, $degree)
    {
        return $query->where('degree', $degree);
    }

    public function getTotalCoursesAttribute()
    {
        return $this->courses()->count();
    }

    public function getActiveCoursesAttribute()
    {
        return $this->courses()->active()->count();
    }

    public function getTotalStudentsAttribute()
    {
        return $this->students()->count();
    }

    public function getActiveStudentsAttribute()
    {
        return $this->students()->active()->count();
    }

    public function getTotalLecturersAttribute()
    {
        return $this->lecturers()->count();
    }

    public function getTotalSchedulesAttribute()
    {
        return $this->schedules()->count();
    }

    public function getFormattedCodeAttribute()
    {
        return strtoupper($this->code);
    }

    public function getFullNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }

    public function isUndergraduate()
    {
        return $this->level === 'undergraduate';
    }

    public function isGraduate()
    {
        return $this->level === 'graduate';
    }

    public function isDoctoral()
    {
        return $this->level === 'doctoral';
    }
}
