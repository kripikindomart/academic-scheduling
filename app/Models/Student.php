<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_number',
        'name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'city',
        'province',
        'postal_code',
        'nationality',
        'religion',
        'blood_type',
        'id_card_number',
        'passport_number',
        'status',
        'enrollment_date',
        'graduation_date',
        'current_semester',
        'current_year',
        'gpa',
        'class',
        'batch_year',
        'is_regular',
        'is_active',
        'father_name',
        'mother_name',
        'parent_phone',
        'parent_email',
        'parent_address',
        'photo',
        'notes',
        'program_study_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'enrollment_date' => 'date',
        'graduation_date' => 'date',
        'current_semester' => 'integer',
        'current_year' => 'integer',
        'gpa' => 'float',
        'is_regular' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'birth_date',
        'enrollment_date',
        'graduation_date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function programStudy()
    {
        return $this->belongsTo(ProgramStudy::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'student_schedules')
            ->withPivot('enrollment_date', 'status')
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

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByBatchYear($query, $batchYear)
    {
        return $query->where('batch_year', $batchYear);
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeByClass($query, $class)
    {
        return $query->where('class', $class);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('current_semester', $semester);
    }

    public function getFullNameAttribute()
    {
        return "{$this->student_number} - {$this->name}";
    }

    public function getFormattedStudentNumberAttribute()
    {
        return str_pad($this->student_number, 10, '0', STR_PAD_LEFT);
    }

    public function getAgeAttribute()
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }

    public function getFullNameWithProgramAttribute()
    {
        $program = $this->programStudy ? $this->programStudy->name : '';
        return "{$this->name} ({$program})";
    }

    public function isGraduated()
    {
        return $this->status === 'graduated';
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->is_active;
    }

    public function isOnLeave()
    {
        return $this->status === 'on_leave';
    }

    public function isDroppedOut()
    {
        return $this->status === 'dropped_out';
    }

    public function getStudyDurationAttribute()
    {
        if ($this->enrollment_date) {
            return Carbon::parse($this->enrollment_date)->diffInYears(Carbon::now());
        }
        return 0;
    }

    public function getProgressPercentageAttribute()
    {
        $programStudy = $this->programStudy;
        if (!$programStudy) return 0;

        $expectedYears = $programStudy->duration_years;
        $currentYears = $this->getStudyDurationAttribute();

        return min(($currentYears / $expectedYears) * 100, 100);
    }

    public function canEnroll()
    {
        return $this->isActive() && !$this->isGraduated();
    }

    public function getGPAColorAttribute()
    {
        if ($this->gpa >= 3.5) return 'text-green-600';
        if ($this->gpa >= 2.75) return 'text-blue-600';
        if ($this->gpa >= 2.0) return 'text-yellow-600';
        return 'text-red-600';
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'graduated':
                return 'bg-blue-100 text-blue-800';
            case 'dropped_out':
                return 'bg-red-100 text-red-800';
            case 'on_leave':
                return 'bg-yellow-100 text-yellow-800';
            case 'inactive':
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'Aktif';
            case 'graduated':
                return 'Lulus';
            case 'dropped_out':
                return 'Drop Out';
            case 'on_leave':
                return 'Cuti';
            case 'inactive':
                return 'Tidak Aktif';
            default:
                return $this->status;
        }
    }
}
