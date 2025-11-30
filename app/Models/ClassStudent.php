<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    protected $table = 'class_student';

    protected $fillable = [
        'class_id',
        'student_id',
        'enrollment_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'enrollment_date',
    ];

    public function class()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeTransferred($query)
    {
        return $query->where('status', 'transferred');
    }

    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByEnrollmentDate($query, $date)
    {
        return $query->whereDate('enrollment_date', $date);
    }

    public function scopeByEnrollmentPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('enrollment_date', [$startDate, $endDate]);
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'Aktif';
            case 'inactive':
                return 'Tidak Aktif';
            case 'transferred':
                return 'Dipindahkan';
            case 'dropped':
                return 'Drop Out';
            default:
                return $this->status;
        }
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'inactive':
                return 'bg-gray-100 text-gray-800';
            case 'transferred':
                return 'bg-blue-100 text-blue-800';
            case 'dropped':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function getFormattedEnrollmentDateAttribute()
    {
        return $this->enrollment_date ? $this->enrollment_date->format('d/m/Y') : null;
    }

    public function getEnrollmentDurationAttribute()
    {
        if (!$this->enrollment_date) return null;

        return $this->enrollment_date->diffInDays(now());
    }

    public function getEnrollmentDurationDisplayAttribute()
    {
        $duration = $this->getEnrollmentDurationAttribute();
        if ($duration === null) return null;

        if ($duration < 30) {
            return $duration . ' hari';
        } elseif ($duration < 365) {
            $months = floor($duration / 30);
            $days = $duration % 30;
            return $months . ' bulan ' . ($days > 0 ? $days . ' hari' : '');
        } else {
            $years = floor($duration / 365);
            $months = floor(($duration % 365) / 30);
            return $years . ' tahun ' . ($months > 0 ? $months . ' bulan' : '');
        }
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    public function isTransferred()
    {
        return $this->status === 'transferred';
    }

    public function isDropped()
    {
        return $this->status === 'dropped';
    }

    public function canTransfer()
    {
        return $this->isActive();
    }

    public function canDeactivate()
    {
        return $this->isActive();
    }

    public function canActivate()
    {
        return $this->isInactive();
    }

    public function activate()
    {
        $this->update(['status' => 'active']);
        return $this;
    }

    public function deactivate($reason = null)
    {
        $this->update([
            'status' => 'inactive',
            'notes' => $reason ?: $this->notes
        ]);
        return $this;
    }

    public function transfer($reason = null)
    {
        $this->update([
            'status' => 'transferred',
            'notes' => $reason ?: $this->notes
        ]);
        return $this;
    }

    public function drop($reason = null)
    {
        $this->update([
            'status' => 'dropped',
            'notes' => $reason ?: $this->notes
        ]);
        return $this;
    }

    // Helper methods for status changes
    public function getAvailableStatusTransitionsAttribute()
    {
        $transitions = [];

        switch ($this->status) {
            case 'active':
                $transitions = [
                    'inactive' => 'Non-aktifkan',
                    'transferred' => 'Pindahkan',
                    'dropped' => 'Drop Out'
                ];
                break;
            case 'inactive':
                $transitions = [
                    'active' => 'Aktifkan kembali',
                ];
                break;
        }

        return $transitions;
    }

    // Scopes for common queries
    public function scopeWithStatus($query, $status)
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }
        return $query->where('status', $status);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('enrollment_date', '>=', now()->subDays($days));
    }

    public function scopeInAcademicYear($query, $academicYear)
    {
        return $query->whereHas('class', function ($q) use ($academicYear) {
            $q->where('academic_year', $academicYear);
        });
    }

    public function scopeInSemester($query, $semester)
    {
        return $query->whereHas('class', function ($q) use ($semester) {
            $q->where('semester', $semester);
        });
    }

    public function scopeInProgramStudy($query, $programStudyId)
    {
        return $query->whereHas('class', function ($q) use ($programStudyId) {
            $q->where('program_study_id', $programStudyId);
        });
    }

    public function scopeInBatch($query, $batchYear)
    {
        return $query->whereHas('class', function ($q) use ($batchYear) {
            $q->where('batch_year', $batchYear);
        });
    }

    public function getEnrollmentPeriodAttribute()
    {
        if (!$this->enrollment_date) return null;

        $startDate = $this->enrollment_date;
        $endDate = now();

        $startYear = $startDate->year;
        $endYear = $endDate->year;

        if ($startYear === $endYear) {
            return $startYear;
        }

        return $startYear . '/' . $endYear;
    }

    public function getEnrollmentYearAttribute()
    {
        return $this->enrollment_date ? $this->enrollment_date->year : null;
    }

    public function getEnrollmentMonthAttribute()
    {
        return $this->enrollment_date ? $this->enrollment_date->format('F') : null;
    }
}