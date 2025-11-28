<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Lecturer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_number',
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
        'employment_status',
        'employment_type',
        'hire_date',
        'termination_date',
        'position',
        'rank',
        'specialization',
        'department',
        'faculty',
        'highest_education',
        'education_institution',
        'education_major',
        'graduation_year',
        'certifications',
        'research_interests',
        'publications',
                'office_room',
        'office_hours',
        'is_active',
        'photo',
        'notes',
        'program_study_id',
        'user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'graduation_year' => 'integer',
                'is_active' => 'boolean',
        'office_hours' => 'array',
        'certifications' => 'array',
        'research_interests' => 'array',
        'publications' => 'array',
    ];

    protected $dates = [
        'birth_date',
        'hire_date',
        'termination_date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programStudy()
    {
        return $this->belongsTo(ProgramStudy::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_lecturers', 'user_id', 'course_id')
            ->withPivot('role', 'assigned_at', 'assigned_by')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'graded_by');
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

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeByFaculty($query, $faculty)
    {
        return $query->where('faculty', $faculty);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', 'like', "%{$specialization}%");
    }

    public function scopeByRank($query, $rank)
    {
        return $query->where('rank', $rank);
    }

    public function getFullNameAttribute()
    {
        return "{$this->employee_number} - {$this->name}";
    }

    public function getFormattedEmployeeNumberAttribute()
    {
        return str_pad($this->employee_number, 10, '0', STR_PAD_LEFT);
    }

    public function getAgeAttribute()
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }

    public function getServiceYearsAttribute()
    {
        return $this->hire_date ? Carbon::parse($this->hire_date)->diffInYears(Carbon::now()) : 0;
    }

    public function getFullNameWithPositionAttribute()
    {
        return "{$this->name} ({$this->position})";
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->is_active;
    }

    public function isPermanent()
    {
        return $this->employment_type === 'permanent';
    }

    public function isContract()
    {
        return $this->employment_type === 'contract';
    }

    public function isPartTime()
    {
        return $this->employment_type === 'part_time';
    }

    public function getEmploymentStatusColorAttribute()
    {
        switch ($this->employment_type) {
            case 'permanent':
                return 'bg-green-100 text-green-800';
            case 'contract':
                return 'bg-blue-100 text-blue-800';
            case 'part_time':
                return 'bg-yellow-100 text-yellow-800';
            case 'guest':
                return 'bg-purple-100 text-purple-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function getEmploymentStatusLabelAttribute()
    {
        switch ($this->employment_type) {
            case 'permanent':
                return 'Tetap';
            case 'contract':
                return 'Kontrak';
            case 'part_time':
                return 'Paruh Waktu';
            case 'guest':
                return 'Tamu';
            default:
                return $this->employment_type;
        }
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'inactive':
                return 'bg-gray-100 text-gray-800';
            case 'on_leave':
                return 'bg-yellow-100 text-yellow-800';
            case 'terminated':
                return 'bg-red-100 text-red-800';
            case 'retired':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'Aktif';
            case 'inactive':
                return 'Tidak Aktif';
            case 'on_leave':
                return 'Cuti';
            case 'terminated':
                return 'Berhenti';
            case 'retired':
                return 'Pensiun';
            default:
                return $this->status;
        }
    }

    public function getAcademicTitleAttribute()
    {
        return "{$this->rank} {$this->name}";
    }

    public function getSpecializationsListAttribute()
    {
        return is_array($this->specialization)
            ? implode(', ', $this->specialization)
            : $this->specialization;
    }

    public function getResearchInterestsListAttribute()
    {
        return is_array($this->research_interests)
            ? implode(', ', $this->research_interests)
            : $this->research_interests;
    }

    public function getCurrentCoursesAttribute()
    {
        // Filter by assigned_at timestamp to get current courses
        return $this->courses()
            ->where('assigned_at', '>=', Carbon::now()->startOfYear())
            ->count();
    }

    public function getTotalCoursesAttribute()
    {
        return $this->courses()->count();
    }

    public function getAverageAcademicLoadAttribute()
    {
        $currentCourses = $this->getCurrentCoursesAttribute();
        return $currentCourses * 3; // Assuming each course is 3 credits
    }

    public function getOfficeHoursFormattedAttribute()
    {
        if (!is_array($this->office_hours)) {
            return 'Belum diatur';
        }

        $formatted = [];
        foreach ($this->office_hours as $day => $hours) {
            $formatted[] = "{$day}: {$hours['start']} - {$hours['end']}";
        }

        return implode(', ', $formatted);
    }

    public function canTeach(Course $course)
    {
        // Check if lecturer has relevant specialization
        if ($this->specialization && is_array($this->specialization)) {
            foreach ($this->specialization as $spec) {
                if (stripos($course->description, $spec) !== false ||
                    stripos($course->course_name, $spec) !== false) {
                    return true;
                }
            }
        }

        // Check if lecturer is in the same program study
        return $this->program_study_id === $course->program_study_id;
    }

    public function getWorkloadPercentageAttribute()
    {
        $currentLoad = $this->current_courses * 3; // 3 credits per course
        $maxLoad = $this->academic_load ?? 12; // Default max 12 credits

        return min(($currentLoad / $maxLoad) * 100, 100);
    }

    public function isAvailableForSchedule($day, $time)
    {
        if (!is_array($this->office_hours)) {
            return true;
        }

        $dayName = strtolower(date('l', strtotime($day)));
        return isset($this->office_hours[$dayName]) &&
               $time >= $this->office_hours[$dayName]['start'] &&
               $time <= $this->office_hours[$dayName]['end'];
    }

    public function getEducationalBackgroundAttribute()
    {
        return [
            'highest_education' => $this->highest_education,
            'institution' => $this->education_institution,
            'major' => $this->education_major,
            'graduation_year' => $this->graduation_year,
        ];
    }
}
