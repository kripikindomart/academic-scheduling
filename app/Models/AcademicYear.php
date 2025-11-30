<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'academic_calendar_year', // e.g., "2025-2026"
        'description',
        'start_date',
        'end_date',
        'is_active',
        'status',
        'admission_period', // 'ganjil' or 'genap'

        // Admission & Registration
        'admission_start_date',
        'admission_end_date',
        'registration_start_date',
        'registration_end_date',
        'course_registration_start_date',
        'course_registration_end_date',

        // Academic Calendar
        'class_start_date',
        'class_end_date',
        'mid_exam_start_date',
        'mid_exam_end_date',
        'final_exam_start_date',
        'final_exam_end_date',

        // S2 Specific
        'thesis_deadline',
        'yudisium_date',

        // Settings & Limits
        'max_credit_per_semester',
        'tuition_fee',
        'registration_fee',
        'is_visible_to_students',
        'allow_course_registration',
        'allow_schedule_changes',
        'settings',

        // Audit
        'created_by',
        'updated_by',
        'activated_by',
        'activated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'admission_start_date' => 'date',
        'admission_end_date' => 'date',
        'registration_start_date' => 'date',
        'registration_end_date' => 'date',
        'course_registration_start_date' => 'date',
        'course_registration_end_date' => 'date',
        'class_start_date' => 'date',
        'class_end_date' => 'date',
        'mid_exam_start_date' => 'date',
        'mid_exam_end_date' => 'date',
        'final_exam_start_date' => 'date',
        'final_exam_end_date' => 'date',
        'thesis_deadline' => 'date',
        'yudisium_date' => 'date',
        'activated_at' => 'datetime',
        'is_active' => 'boolean',
        'is_visible_to_students' => 'boolean',
        'allow_course_registration' => 'boolean',
        'allow_schedule_changes' => 'boolean',
        'settings' => 'array',
        'tuition_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
    ];

    /**
     * Get the user who created the academic year.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the academic year.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who activated the academic year.
     */
    public function activator()
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    /**
     * Scope a query to only include active academic years.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include visible academic years.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible_to_students', true);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get the current active academic year.
     */
    public static function getActive()
    {
        return static::active()->first();
    }

    /**
     * Set this academic year as active.
     * This will deactivate all other academic years.
     */
    public function setActive()
    {
        // Deactivate all other academic years
        static::where('is_active', true)->update([
            'is_active' => false,
            'status' => 'completed'
        ]);

        // Activate this academic year
        $this->update([
            'is_active' => true,
            'status' => 'active',
            'activated_by' => auth()->id(),
            'activated_at' => now()
        ]);
    }

    /**
     * Check if registration is currently open.
     */
    public function isRegistrationOpen()
    {
        $now = now()->toDateString();
        return $this->registration_start_date &&
               $this->registration_end_date &&
               $now >= $this->registration_start_date &&
               $now <= $this->registration_end_date;
    }

    /**
     * Check if course registration is currently open.
     */
    public function isCourseRegistrationOpen()
    {
        $now = now()->toDateString();
        return $this->allow_course_registration &&
               $this->course_registration_start_date &&
               $this->course_registration_end_date &&
               $now >= $this->course_registration_start_date &&
               $now <= $this->course_registration_end_date;
    }

    /**
     * Get current semester based on date.
     */
    public function getCurrentSemesterByDate()
    {
        $now = now();

        if ($this->semester_2_start_date && $now >= $this->semester_2_start_date) {
            return 2;
        }

        return 1;
    }

    /**
     * Get formatted tuition fee.
     */
    public function getFormattedTuitionFee()
    {
        return $this->tuition_fee ? 'Rp ' . number_format($this->tuition_fee, 0, ',', '.') : '-';
    }

    /**
     * Get formatted registration fee.
     */
    public function getFormattedRegistrationFee()
    {
        return $this->registration_fee ? 'Rp ' . number_format($this->registration_fee, 0, ',', '.') : '-';
    }

    /**
     * Get status badge HTML.
     */
    public function getStatusBadge()
    {
        $badges = [
            'upcoming' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">Akan Datang</span>',
            'active' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">Aktif</span>',
            'completed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">Selesai</span>',
        ];

        return $badges[$this->status] ?? $badges['upcoming'];
    }
}
