<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'code',
        'program_study_id',
        'batch_year',
        'semester',
        'academic_year',
        'capacity',
        'current_students',
        'room_number',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_students' => 'integer',
        'batch_year' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id')
            ->withPivot('enrollment_date', 'status', 'notes', 'created_by')
            ->withTimestamps()
            ->wherePivot('status', 'active');
    }

    public function allStudents()
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id')
            ->withPivot('enrollment_date', 'status', 'notes', 'created_by')
            ->withTimestamps();
    }

    public function activeStudents()
    {
        return $this->students()->wherePivot('status', 'active');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_study_id', $programId);
    }

    public function scopeByBatch($query, $batchYear)
    {
        return $query->where('batch_year', $batchYear);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeHasCapacity($query)
    {
        return $query->whereRaw('current_students < capacity');
    }

    public function getFullNameAttribute()
    {
        $program = $this->programStudy ? $this->programStudy->name : '';
        return "{$this->name} ({$program} - {$this->academic_year})";
    }

    public function getCapacityStatusAttribute()
    {
        $percentage = $this->capacity > 0 ? ($this->current_students / $this->capacity) * 100 : 0;

        if ($percentage >= 100) return 'Penuh';
        if ($percentage >= 80) return 'Hampir Penuh';
        if ($percentage >= 50) return 'Sedang';
        return 'Kosong';
    }

    public function getCapacityPercentageAttribute()
    {
        return $this->capacity > 0 ? round(($this->current_students / $this->capacity) * 100, 1) : 0;
    }

    public function getAvailableSlotsAttribute()
    {
        return max(0, $this->capacity - $this->current_students);
    }

    public function getIsFullAttribute()
    {
        return $this->current_students >= $this->capacity;
    }

    public function getStatusColorAttribute()
    {
        if (!$this->is_active) return 'bg-gray-100 text-gray-800';

        $percentage = $this->capacity_percentage;
        if ($percentage >= 100) return 'bg-red-100 text-red-800';
        if ($percentage >= 80) return 'bg-yellow-100 text-yellow-800';
        if ($percentage >= 50) return 'bg-blue-100 text-blue-800';
        return 'bg-green-100 text-green-800';
    }

    public function getSemesterLabelAttribute()
    {
        return ucfirst($this->semester);
    }

    public function getBatchLabelAttribute()
    {
        return "Angkatan {$this->batch_year}";
    }

    public function canEnrollStudent()
    {
        return $this->is_active && !$this->is_full;
    }

    public function enrollStudent($studentId, $data = [])
    {
        if (!$this->canEnrollStudent()) {
            throw new \Exception('Kelas sudah penuh atau tidak aktif');
        }

        $existingEnrollment = $this->students()->where('student_id', $studentId)->first();
        if ($existingEnrollment) {
            throw new \Exception('Mahasiswa sudah terdaftar di kelas ini');
        }

        $enrollmentData = array_merge([
            'enrollment_date' => now(),
            'status' => 'active',
            'notes' => null,
            'created_by' => auth()->id(),
        ], $data);

        $this->students()->attach($studentId, $enrollmentData);

        // Update current students count
        $this->increment('current_students');

        return true;
    }

    public function removeStudent($studentId)
    {
        $student = $this->students()->where('student_id', $studentId)->first();
        if (!$student) {
            throw new \Exception('Mahasiswa tidak ditemukan di kelas ini');
        }

        $this->students()->updateExistingPivot($studentId, [
            'status' => 'inactive',
            'notes' => 'Dikeluarkan dari kelas'
        ]);

        $this->decrement('current_students');

        return true;
    }

    public function transferStudent($studentId, $toClassId)
    {
        $toClass = self::findOrFail($toClassId);

        if (!$toClass->canEnrollStudent()) {
            throw new \Exception('Kelas tujuan sudah penuh atau tidak aktif');
        }

        $student = $this->students()->where('student_id', $studentId)->first();
        if (!$student) {
            throw new \Exception('Mahasiswa tidak ditemukan di kelas ini');
        }

        $pivotData = $student->pivot;

        // Remove from current class
        $this->students()->updateExistingPivot($studentId, [
            'status' => 'transferred',
            'notes' => 'Dipindahkan ke kelas lain'
        ]);
        $this->decrement('current_students');

        // Add to new class
        $toClass->students()->attach($studentId, [
            'enrollment_date' => $pivotData->enrollment_date ?? now(),
            'status' => 'active',
            'notes' => 'Dipindahkan dari kelas ' . $this->name,
            'created_by' => auth()->id(),
        ]);
        $toClass->increment('current_students');

        return true;
    }

    public function getStatisticsAttribute()
    {
        $totalStudents = $this->current_students;
        $activeStudents = $this->activeStudents()->count();
        $inactiveStudents = $totalStudents - $activeStudents;

        return [
            'total_students' => $totalStudents,
            'active_students' => $activeStudents,
            'inactive_students' => $inactiveStudents,
            'available_slots' => $this->available_slots,
            'capacity_percentage' => $this->capacity_percentage,
            'status' => $this->capacity_status,
        ];
    }

    public function getCurrentStudentsAttribute()
    {
        // Always return the actual count from the relationship
        return $this->activeStudents()->count();
    }

    public function getAcademicYearDisplayAttribute()
    {
        return str_replace('/', '/', $this->academic_year);
    }
}
