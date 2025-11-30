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
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function classes()
    {
        return $this->belongsToMany(Kelas::class, 'class_student', 'student_id', 'class_id')
            ->withPivot('enrollment_date', 'status', 'notes', 'created_by')
            ->withTimestamps()
            ->wherePivot('status', 'active');
    }

    public function allClasses()
    {
        return $this->belongsToMany(Kelas::class, 'class_student', 'student_id', 'class_id')
            ->withPivot('enrollment_date', 'status', 'notes', 'created_by')
            ->withTimestamps();
    }

    public function activeClasses()
    {
        return $this->classes()->where('classes.is_active', true);
    }

    public function currentClass()
    {
        return $this->activeClasses()
            ->where('classes.academic_year', 'like', '%' . date('Y') . '%')
            ->first();
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

    public function scopeByEnrollmentYear($query, $enrollmentYear)
    {
        return $query->whereYear('enrollment_date', $enrollmentYear);
    }

    public function scopeByExpectedGraduation($query, $expectedGraduation)
    {
        return $query->whereYear('graduation_date', $expectedGraduation);
    }

    public function scopeByGpaRange($query, $minGpa, $maxGpa = null)
    {
        $query->where('gpa', '>=', $minGpa);
        if ($maxGpa) {
            $query->where('gpa', '<=', $maxGpa);
        }
        return $query;
    }

    public function scopeByCreditsCompleted($query, $credits)
    {
        // This would need to be implemented based on actual credits tracking
        return $query;
    }

    public function scopeRegular($query)
    {
        return $query->where('is_regular', true);
    }

    public function scopeNonRegular($query)
    {
        return $query->where('is_regular', false);
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

    // Additional business logic methods similar to Lecturer model
    public function getExpectedGraduationDateAttribute()
    {
        if ($this->enrollment_date && $this->programStudy) {
            $duration = $this->programStudy->duration_years;
            return Carbon::parse($this->enrollment_date)->addYears($duration);
        }
        return null;
    }

    public function getAcademicStandingAttribute()
    {
        if ($this->gpa >= 3.5) return 'Cum Laude';
        if ($this->gpa >= 3.0) return 'Sangat Memuaskan';
        if ($this->gpa >= 2.5) return 'Memuaskan';
        if ($this->gpa >= 2.0) return 'Cukup';
        return 'Kurang';
    }

    public function getCreditsCompletedAttribute()
    {
        // This should be calculated from course enrollments
        // For now return 0 as placeholder
        return 0;
    }

    public function getMaxCreditsAttribute()
    {
        return $this->programStudy ? $this->programStudy->minimum_credits : 144;
    }

    public function getRemainingCreditsAttribute()
    {
        return max(0, $this->getMaxCreditsAttribute() - $this->getCreditsCompletedAttribute());
    }

    public function getEnrollmentStatusAttribute()
    {
        if ($this->isGraduated()) return 'Lulus';
        if ($this->isDroppedOut()) return 'Drop Out';
        if ($this->isOnLeave()) return 'Cuti';
        if ($this->isActive()) return 'Aktif';
        return 'Tidak Aktif';
    }

    public function canRegisterForCourses()
    {
        return $this->isActive() && $this->current_semester && !$this->isGraduated();
    }

    public function getBatchLabelAttribute()
    {
        return "Angkatan {$this->batch_year}";
    }

    public function getFormattedGpaAttribute()
    {
        return number_format($this->gpa, 2);
    }

    public function getFullNameWithDetailsAttribute()
    {
        $status = $this->getStatusLabelAttribute();
        return "{$this->name} ({$this->student_number}) - {$status}";
    }

    public function hasUserAccount()
    {
        return !is_null($this->user_id);
    }

    public function isOnlineStudent()
    {
        return $this->hasUserAccount() && $this->user && $this->user->email_verified_at;
    }

    public function getAttendanceRateAttribute()
    {
        // Calculate attendance rate from attendance records
        $totalClasses = $this->attendances()->count();
        if ($totalClasses === 0) return 0;

        $attendedClasses = $this->attendances()->where('status', 'present')->count();
        return round(($attendedClasses / $totalClasses) * 100, 2);
    }

    public function getAverageGradeAttribute()
    {
        // Calculate average grade from grades table
        $grades = $this->grades()->whereNotNull('score')->pluck('score');
        if ($grades->isEmpty()) return 0;

        return round($grades->avg(), 2);
    }
}
