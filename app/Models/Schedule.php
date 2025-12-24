<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'schedule_code',
        'class_schedule_id',
        'class_schedule_detail_id',
        'title',
        'description',
        'date',
        'start_time',
        'end_time',
        'day_of_week',
        'duration_minutes',
        'schedule_type',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_end_date',
        'course_id',
        'program_study_id',
        'class_id',
        'lecturer_id',
        'room_id',

        'academic_year',
        'week_number',
        'meeting_number',
        'session_number',
        'total_sessions',
        'status',
        'conflict_status',
        'conflict_details',
        'rejection_reason',
        'expected_attendees',
        'actual_attendees',
        'attendance_rate',
        'session_type',
        'is_mandatory',
        'is_online',
        'meeting_link',
        'notes',
        'materials',
        'is_published',
        'is_locked',
        'published_at',
        'locked_at',
        'approved_by',
        'approved_at',
        'approval_notes',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
        'rescheduled_from',
        'reschedule_reason',
        'created_by',
        'updated_by',
        'last_modified_at',
        'created_from_ip',
        'user_agent',
        'deleted_by',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_minutes' => 'integer',
        'is_recurring' => 'boolean',
        'recurrence_pattern' => 'array',
        'recurrence_end_date' => 'date',
        'week_number' => 'integer',
        'expected_attendees' => 'integer',
        'actual_attendees' => 'integer',
        'attendance_rate' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_online' => 'boolean',
        'is_published' => 'boolean',
        'is_locked' => 'boolean',
        'published_at' => 'datetime',
        'locked_at' => 'datetime',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_modified_at' => 'datetime',
        'materials' => 'array',
    ];

    protected $hidden = [
        'deleted_at',
        'deleted_by',
    ];

    protected $dates = [
        'date',
        'start_time',
        'end_time',
        'recurrence_end_date',
        'published_at',
        'locked_at',
        'approved_at',
        'cancelled_at',
        'last_modified_at',
    ];

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Multiple lecturers (many-to-many)
    public function lecturers(): BelongsToMany
    {
        return $this->belongsToMany(Lecturer::class, 'schedule_lecturer')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Primary lecturer relationship (for eager loading - returns relation)
    public function lecturer(): BelongsToMany
    {
        return $this->belongsToMany(Lecturer::class, 'schedule_lecturer')
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
        return $this->belongsToMany(Room::class, 'schedule_room')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Primary room relationship (for eager loading - returns relation)
    public function room(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'schedule_room')
                    ->wherePivot('is_primary', true)
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Helper to get primary room model
    public function getPrimaryRoom()
    {
        return $this->rooms()->wherePivot('is_primary', true)->first();
    }

    public function programStudy(): BelongsTo
    {
        return $this->belongsTo(ProgramStudy::class);
    }

    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function classScheduleDetail(): BelongsTo
    {
        return $this->belongsTo(ClassScheduleDetail::class, 'class_schedule_detail_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function rescheduledFrom(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'rescheduled_from');
    }

    public function rescheduledTo(): HasMany
    {
        return $this->hasMany(Schedule::class, 'rescheduled_from');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereDate('start_date', '<=', $endDate)
                    ->whereDate('end_date', '>=', $startDate);
    }

    public function scopeByTimeRange($query, $startTime, $endTime)
    {
        return $query->whereTime('start_time', '>=', $startTime)
                    ->whereTime('end_time', '<=', $endTime);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByLecturer($query, $lecturerId)
    {
        return $query->where('lecturer_id', $lecturerId);
    }

    public function scopeByRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_study_id', $programId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeByAcademicYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySessionType($query, $sessionType)
    {
        return $query->where('session_type', $sessionType);
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeOffline($query)
    {
        return $query->where('is_online', false);
    }

    public function scopeWithConflicts($query)
    {
        return $query->where('conflict_status', '!=', 'none');
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('schedule_code', 'like', "%{$search}%")
              ->orWhere('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereHas('course', function ($subQuery) use ($search) {
                  $subQuery->where('course_code', 'like', "%{$search}%")
                         ->orWhere('course_name', 'like', "%{$search}%");
              })
              ->orWhereHas('lecturer', function ($subQuery) use ($search) {
                  $subQuery->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('room', function ($subQuery) use ($search) {
                  $subQuery->where('room_code', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
              });
        });
    }

    // Helper methods
    public function getFormattedTimeRange(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function getDurationInHours(): float
    {
        return $this->duration_minutes / 60;
    }

    public function isUpcoming(): bool
    {
        return $this->date->isFuture() ||
               ($this->date->isToday() && $this->start_time->isFuture());
    }

    public function isOngoing(): bool
    {
        $now = now();
        return $this->date->isToday() &&
               $now->between($this->start_time, $this->end_time);
    }

    public function isPast(): bool
    {
        return $this->date->isPast() ||
               ($this->date->isToday() && $this->end_time->isPast());
    }

    public function canBeEdited(): bool
    {
        return !$this->is_locked &&
               in_array($this->status, ['draft', 'submitted']) &&
               !$this->is_past;
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['cancelled', 'completed']) &&
               !$this->is_past;
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'submitted' && !$this->is_locked;
    }

    public function hasConflict(): bool
    {
        return $this->conflict_status !== 'none';
    }

    public function getAttendancePercentage(): float
    {
        return $this->expected_attendees > 0
            ? round(($this->actual_attendees / $this->expected_attendees) * 100, 2)
            : 0;
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'orange',
            'completed' => 'purple',
            default => 'gray'
        };
    }

    public function getConflictStatusColor(): string
    {
        return match($this->conflict_status) {
            'none' => 'green',
            'detected' => 'red',
            'resolved' => 'yellow',
            default => 'gray'
        };
    }

    public function isTimeSlotOccupied($startTime, $endTime, $excludeSelf = false): bool
    {
        $query = static::where('date', $this->date)
                      ->where('room_id', $this->room_id)
                      ->where(function ($q) use ($startTime, $endTime) {
                          $q->where(function ($subQ) use ($startTime, $endTime) {
                              $subQ->where('start_time', '<=', $startTime)
                                   ->where('end_time', '>', $startTime);
                          })
                          ->orWhere(function ($subQ) use ($startTime, $endTime) {
                              $subQ->where('start_time', '<', $endTime)
                                   ->where('end_time', '>=', $endTime);
                          })
                          ->orWhere(function ($subQ) use ($startTime, $endTime) {
                              $subQ->where('start_time', '>=', $startTime)
                                   ->where('end_time', '<=', $endTime);
                          });
                      })
                      ->where('status', '!=', 'cancelled');

        if ($excludeSelf && $this->id) {
            $query->where('id', '!=', $this->id);
        }

        return $query->exists();
    }
}
