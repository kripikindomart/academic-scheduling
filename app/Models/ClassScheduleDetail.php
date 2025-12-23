<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ClassScheduleDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_schedule_id',
        'course_id',
        'day_of_week',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'sessions_per_meeting',
        'total_meetings',
        'meeting_type',
        'is_online',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'start_date' => 'date',
        'end_date' => 'date',
        'sessions_per_meeting' => 'integer',
        'total_meetings' => 'integer',
        'is_online' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Relationships
    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Multiple lecturers (many-to-many)
    public function lecturers(): BelongsToMany
    {
        return $this->belongsToMany(Lecturer::class, 'class_schedule_detail_lecturer')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Primary lecturer relationship (for eager loading - returns relation)
    public function lecturer(): BelongsToMany
    {
        return $this->belongsToMany(Lecturer::class, 'class_schedule_detail_lecturer')
                    ->wherePivot('is_primary', true)
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Helper to get primary lecturer model
    public function getPrimaryLecturer()
    {
        return $this->lecturers()->wherePivot('is_primary', true)->first();
    }

    // Multiple rooms (many-to-many)
    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'class_schedule_detail_room')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Primary room relationship (for eager loading - returns relation)
    public function room(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'class_schedule_detail_room')
                    ->wherePivot('is_primary', true)
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Helper to get primary room model
    public function getPrimaryRoom()
    {
        return $this->rooms()->wherePivot('is_primary', true)->first();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_schedule_detail_id');
    }

    // Scopes
    public function scopeByDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeOffline($query)
    {
        return $query->where('is_online', false);
    }

    // Helper methods
    public function getFormattedTimeRange(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function getFormattedDateRange(): string
    {
        return $this->start_date->format('d M Y') . ' - ' . $this->end_date->format('d M Y');
    }

    public function getDayName(): string
    {
        $days = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu'
        ];

        return $days[$this->day_of_week] ?? $this->day_of_week;
    }

    public function getDurationInMinutes(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function getMeetingTypeText(): string
    {
        return match($this->meeting_type) {
            'lecture' => 'Kuliah',
            'lab' => 'Praktikum',
            'seminar' => 'Seminar',
            'workshop' => 'Workshop',
            'online' => 'Online',
            default => 'Kuliah'
        };
    }

    public function canGenerateSchedules(): bool
    {
        return $this->total_meetings > 0 &&
               $this->start_date <= $this->end_date &&
               $this->sessions_per_meeting > 0;
    }

    public function calculateTotalMeetings(): int
    {
        if (!$this->start_date || !$this->end_date || !$this->day_of_week) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($this->start_date);
        $end = \Carbon\Carbon::parse($this->end_date);
        $meetings = 0;
        $englishDay = $this->getEnglishDayName();

        $current = $start->copy();
        while ($current <= $end) {
            if (strtolower($current->dayName) === $englishDay) {
                $meetings++;
            }
            $current->addDay();
        }

        return $meetings;
    }

  
    /**
     * Convert day name to English day name for date calculations
     */
    public function getEnglishDayName(): string
    {
        $days = [
            'senin' => 'monday',
            'selasa' => 'tuesday',
            'rabu' => 'wednesday',
            'kamis' => 'thursday',
            'jumat' => 'friday',
            'sabtu' => 'saturday'
        ];

        return $days[strtolower($this->day_of_week)] ?? $this->day_of_week;
    }

    /**
     * Override generateScheduleDates to handle both Indonesian and English day names
     */
    public function generateScheduleDates(): array
    {
        if (!$this->start_date || !$this->end_date || !$this->day_of_week) {
            return [];
        }

        $dates = [];
        $start = \Carbon\Carbon::parse($this->start_date);
        $end = \Carbon\Carbon::parse($this->end_date);
        $meetingCount = 0;
        $englishDay = $this->getEnglishDayName();

        $current = $start->copy();
        while ($current <= $end && $meetingCount < $this->total_meetings) {
            if (strtolower($current->dayName) === $englishDay) {
                $dates[] = $current->copy();
                $meetingCount++;
            }
            $current->addDay();
        }

        return $dates;
    }
}