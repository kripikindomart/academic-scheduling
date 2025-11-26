<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Room;
use App\Models\ProgramStudy;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ScheduleService
{
    /**
     * Get paginated schedules with filtering and search capabilities.
     *
     * @param array $filters
     * @param int $perPage
     * @param string $sortBy
     * @param string $sortDirection
     * @return array
     */
    public function getSchedules(array $filters = [], int $perPage = 15, string $sortBy = 'date', string $sortDirection = 'asc'): array
    {
        $query = Schedule::with([
            'course:id,course_code,course_name,credits',
            'lecturer:id,name,email',
            'room:id,room_code,name,building,capacity',
            'programStudy:id,name,faculty',
            'schoolClass:id,class_name,class_code',
            'creator:id,name,email',
        ]);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply sorting
        $allowedSorts = ['date', 'start_time', 'end_time', 'title', 'status', 'schedule_type', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'date';
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

        $schedules = $query->orderBy($sortBy, $sortDirection)
                          ->paginate($perPage);

        return [
            'data' => $schedules->items(),
            'message' => 'Schedules retrieved successfully',
            'meta' => [
                'current_page' => $schedules->currentPage(),
                'last_page' => $schedules->lastPage(),
                'per_page' => $schedules->perPage(),
                'total' => $schedules->total(),
                'from' => $schedules->firstItem(),
                'to' => $schedules->lastItem(),
            ],
        ];
    }

    /**
     * Create a new schedule.
     *
     * @param array $data
     * @return Schedule
     */
    public function createSchedule(array $data): Schedule
    {
        return DB::transaction(function () use ($data) {
            // Generate unique schedule code
            $scheduleCode = $this->generateScheduleCode($data);

            $schedule = Schedule::create(array_merge($data, [
                'schedule_code' => $scheduleCode,
                'created_by' => auth('sanctum')->id(),
                'updated_by' => auth('sanctum')->id(),
                'last_modified_at' => now(),
                'created_from_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]));

            // Log activity
            Log::channel('activity')->info('Schedule created', [
                'schedule_id' => $schedule->id,
                'schedule_code' => $schedule->schedule_code,
                'course' => $schedule->course->course_name,
                'lecturer' => $schedule->lecturer->name,
                'room' => $schedule->room->room_code,
                'date' => $schedule->date,
                'time' => $schedule->getFormattedTimeRange(),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $schedule;
        });
    }

    /**
     * Update an existing schedule.
     *
     * @param Schedule $schedule
     * @param array $data
     * @return Schedule
     */
    public function updateSchedule(Schedule $schedule, array $data): Schedule
    {
        return DB::transaction(function () use ($schedule, $data) {
            $originalData = $schedule->only([
                'title', 'date', 'start_time', 'end_time', 'room_id', 'lecturer_id', 'course_id', 'status'
            ]);

            $schedule->update(array_merge($data, [
                'updated_by' => auth('sanctum')->id(),
                'last_modified_at' => now(),
            ]));

            // Log significant changes
            $this->logScheduleChanges($schedule, $originalData);

            // Log activity
            Log::channel('activity')->info('Schedule updated', [
                'schedule_id' => $schedule->id,
                'schedule_code' => $schedule->schedule_code,
                'changes' => array_keys($data),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $schedule->fresh();
        });
    }

    /**
     * Delete a schedule.
     *
     * @param Schedule $schedule
     * @return bool
     */
    public function deleteSchedule(Schedule $schedule): bool
    {
        return DB::transaction(function () use ($schedule) {
            $deleted = $schedule->delete();

            if ($deleted) {
                Log::channel('activity')->warning('Schedule deleted', [
                    'schedule_id' => $schedule->id,
                    'schedule_code' => $schedule->schedule_code,
                    'course' => $schedule->course->course_name,
                    'user_id' => auth('sanctum')->id(),
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Get schedule statistics.
     *
     * @return array
     */
    public function getScheduleStatistics(): array
    {
        $today = now()->format('Y-m-d');
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $stats = [
            'total_schedules' => Schedule::count(),
            'today_schedules' => Schedule::whereDate('date', $today)->count(),
            'upcoming_schedules' => Schedule::where('date', '>', $today)->count(),
            'past_schedules' => Schedule::where('date', '<', $today)->count(),
            'active_schedules' => Schedule::whereNotIn('status', ['cancelled', 'completed'])->count(),
            'conflicted_schedules' => Schedule::where('conflict_status', '!=', 'none')->count(),
            'published_schedules' => Schedule::where('is_published', true)->count(),
        ];

        // Status breakdown
        $stats['by_status'] = Schedule::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Schedule type breakdown
        $stats['by_type'] = Schedule::selectRaw('schedule_type, COUNT(*) as count')
            ->groupBy('schedule_type')
            ->get()
            ->pluck('count', 'schedule_type')
            ->toArray();

        // This week schedules
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $stats['this_week'] = Schedule::whereBetween('date', [$weekStart, $weekEnd])->count();

        // This month schedules
        $stats['this_month'] = Schedule::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        return $stats;
    }

    /**
     * Check for schedule conflicts.
     *
     * @param array $data
     * @param int|null $excludeScheduleId
     * @return array
     */
    public function checkConflicts(array $data, ?int $excludeScheduleId = null): array
    {
        $conflicts = [];

        // Check room conflicts
        $roomConflicts = $this->checkRoomConflicts($data, $excludeScheduleId);
        if (!empty($roomConflicts)) {
            $conflicts['room'] = $roomConflicts;
        }

        // Check lecturer conflicts
        $lecturerConflicts = $this->checkLecturerConflicts($data, $excludeScheduleId);
        if (!empty($lecturerConflicts)) {
            $conflicts['lecturer'] = $lecturerConflicts;
        }

        // Check class conflicts
        if (!empty($data['class_id'])) {
            $classConflicts = $this->checkClassConflicts($data, $excludeScheduleId);
            if (!empty($classConflicts)) {
                $conflicts['class'] = $classConflicts;
            }
        }

        return $conflicts;
    }

    /**
     * Get available rooms for given time slot.
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int $minCapacity
     * @return array
     */
    public function getAvailableRooms(string $date, string $startTime, string $endTime, int $minCapacity = 1): array
    {
        $conflictingRoomIds = Schedule::where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })
                ->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })
                ->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->where('status', '!=', 'cancelled')
            ->pluck('room_id')
            ->unique();

        $availableRooms = Room::where('is_active', true)
            ->where('availability_status', 'available')
            ->where('capacity', '>=', $minCapacity)
            ->whereNotIn('id', $conflictingRoomIds)
            ->get(['id', 'room_code', 'name', 'building', 'capacity', 'room_type']);

        return $availableRooms->toArray();
    }

    /**
     * Get available lecturers for given time slot.
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int|null $programStudyId
     * @return array
     */
    public function getAvailableLecturers(string $date, string $startTime, string $endTime, ?int $programStudyId = null): array
    {
        $conflictingLecturerIds = Schedule::where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })
                ->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })
                ->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->where('status', '!=', 'cancelled')
            ->pluck('lecturer_id')
            ->unique();

        $query = Lecturer::where('is_active', true)
            ->whereNotIn('id', $conflictingLecturerIds);

        if ($programStudyId) {
            $query->where('program_study_id', $programStudyId);
        }

        $availableLecturers = $query->get(['id', 'name', 'email', 'employee_number', 'specialization']);

        return $availableLecturers->toArray();
    }

    /**
     * Get schedules by date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @param array $filters
     * @return array
     */
    public function getSchedulesByDateRange(string $startDate, string $endDate, array $filters = []): array
    {
        $query = Schedule::with([
            'course:id,course_code,course_name',
            'lecturer:id,name',
            'room:id,room_code,name,building',
        ])->whereBetween('date', [$startDate, $endDate]);

        $this->applyFilters($query, $filters);

        $schedules = $query->orderBy('date')->orderBy('start_time')->get();

        return [
            'data' => $schedules,
            'message' => 'Schedules retrieved successfully',
            'date_range' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];
    }

    /**
     * Get schedule calendar view data.
     *
     * @param string $year
     * @param string $month
     * @param array $filters
     * @return array
     */
    public function getCalendarView(string $year, string $month, array $filters = []): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $schedules = Schedule::with(['course:id,course_code', 'lecturer:id,name', 'room:id,room_code'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->where('is_published', true)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Group by date
        $calendarData = [];
        foreach ($schedules as $schedule) {
            $dateKey = $schedule->date->format('Y-m-d');
            if (!isset($calendarData[$dateKey])) {
                $calendarData[$dateKey] = [];
            }
            $calendarData[$dateKey][] = $schedule;
        }

        return [
            'data' => $calendarData,
            'year' => $year,
            'month' => $month,
            'month_name' => $startDate->format('F'),
            'total_schedules' => $schedules->count(),
        ];
    }

    /**
     * Approve a schedule.
     *
     * @param Schedule $schedule
     * @param string|null $approvalNotes
     * @return Schedule
     */
    public function approveSchedule(Schedule $schedule, ?string $approvalNotes = null): Schedule
    {
        return DB::transaction(function () use ($schedule, $approvalNotes) {
            $schedule->update([
                'status' => 'approved',
                'approved_by' => auth('sanctum')->id(),
                'approved_at' => now(),
                'approval_notes' => $approvalNotes,
                'updated_by' => auth('sanctum')->id(),
                'last_modified_at' => now(),
            ]);

            Log::channel('activity')->info('Schedule approved', [
                'schedule_id' => $schedule->id,
                'schedule_code' => $schedule->schedule_code,
                'approved_by' => auth('sanctum')->id(),
                'approval_notes' => $approvalNotes,
            ]);

            return $schedule->fresh();
        });
    }

    /**
     * Reject a schedule.
     *
     * @param Schedule $schedule
     * @param string $rejectionReason
     * @return Schedule
     */
    public function rejectSchedule(Schedule $schedule, string $rejectionReason): Schedule
    {
        return DB::transaction(function () use ($schedule, $rejectionReason) {
            $schedule->update([
                'status' => 'rejected',
                'rejection_reason' => $rejectionReason,
                'updated_by' => auth('sanctum')->id(),
                'last_modified_at' => now(),
            ]);

            Log::channel('activity')->warning('Schedule rejected', [
                'schedule_id' => $schedule->id,
                'schedule_code' => $schedule->schedule_code,
                'rejected_by' => auth('sanctum')->id(),
                'rejection_reason' => $rejectionReason,
            ]);

            return $schedule->fresh();
        });
    }

    /**
     * Cancel a schedule.
     *
     * @param Schedule $schedule
     * @param string $cancellationReason
     * @return Schedule
     */
    public function cancelSchedule(Schedule $schedule, string $cancellationReason): Schedule
    {
        return DB::transaction(function () use ($schedule, $cancellationReason) {
            $schedule->update([
                'status' => 'cancelled',
                'cancelled_by' => auth('sanctum')->id(),
                'cancelled_at' => now(),
                'cancellation_reason' => $cancellationReason,
                'updated_by' => auth('sanctum')->id(),
                'last_modified_at' => now(),
            ]);

            Log::channel('activity')->warning('Schedule cancelled', [
                'schedule_id' => $schedule->id,
                'schedule_code' => $schedule->schedule_code,
                'cancelled_by' => auth('sanctum')->id(),
                'cancellation_reason' => $cancellationReason,
            ]);

            return $schedule->fresh();
        });
    }

    /**
     * Generate schedule code.
     *
     * @param array $data
     * @return string
     */
    private function generateScheduleCode(array $data): string
    {
        $prefix = 'SCH';
        $date = date('Ymd');
        $courseCode = $data['course_id'] ? Course::find($data['course_id'])->course_code : 'GEN';
        $random = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));

        return "{$prefix}-{$date}-{$courseCode}-{$random}";
    }

    /**
     * Apply filters to the query.
     *
     * @param mixed $query
     * @param array $filters
     * @return void
     */
    private function applyFilters($query, array $filters): void
    {
        foreach ($filters as $key => $value) {
            if (empty($value)) continue;

            switch ($key) {
                case 'search':
                    $query->search($value);
                    break;
                case 'course_id':
                    $query->where('course_id', $value);
                    break;
                case 'lecturer_id':
                    $query->where('lecturer_id', $value);
                    break;
                case 'room_id':
                    $query->where('room_id', $value);
                    break;
                case 'program_study_id':
                    $query->where('program_study_id', $value);
                    break;
                case 'class_id':
                    $query->where('class_id', $value);
                    break;
                case 'semester':
                    $query->where('semester', $value);
                    break;
                case 'academic_year':
                    $query->where('academic_year', $value);
                    break;
                case 'status':
                    $query->where('status', $value);
                    break;
                case 'schedule_type':
                    $query->where('schedule_type', $value);
                    break;
                case 'session_type':
                    $query->where('session_type', $value);
                    break;
                case 'is_published':
                    $query->where('is_published', $value);
                    break;
                case 'is_online':
                    $query->where('is_online', $value);
                    break;
                case 'date_from':
                    $query->whereDate('date', '>=', $value);
                    break;
                case 'date_to':
                    $query->whereDate('date', '<=', $value);
                    break;
                case 'conflict_status':
                    $query->where('conflict_status', $value);
                    break;
            }
        }
    }

    /**
     * Check room conflicts.
     *
     * @param array $data
     * @param int|null $excludeScheduleId
     * @return array
     */
    private function checkRoomConflicts(array $data, ?int $excludeScheduleId = null): array
    {
        $query = Schedule::where('date', $data['date'])
            ->where('room_id', $data['room_id'])
            ->where(function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->where('start_time', '<=', $data['start_time'])
                      ->where('end_time', '>', $data['start_time']);
                })
                ->orWhere(function ($q) use ($data) {
                    $q->where('start_time', '<', $data['end_time'])
                      ->where('end_time', '>=', $data['end_time']);
                })
                ->orWhere(function ($q) use ($data) {
                    $q->where('start_time', '>=', $data['start_time'])
                      ->where('end_time', '<=', $data['end_time']);
                });
            })
            ->where('status', '!=', 'cancelled');

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        $conflicts = $query->with(['course:id,course_code', 'lecturer:id,name'])
                         ->get(['id', 'schedule_code', 'title', 'start_time', 'end_time', 'course_id', 'lecturer_id']);

        return $conflicts->toArray();
    }

    /**
     * Check lecturer conflicts.
     *
     * @param array $data
     * @param int|null $excludeScheduleId
     * @return array
     */
    private function checkLecturerConflicts(array $data, ?int $excludeScheduleId = null): array
    {
        $query = Schedule::where('date', $data['date'])
            ->where('lecturer_id', $data['lecturer_id'])
            ->where(function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->where('start_time', '<=', $data['start_time'])
                      ->where('end_time', '>', $data['start_time']);
                })
                ->orWhere(function ($q) use ($data) {
                    $q->where('start_time', '<', $data['end_time'])
                      ->where('end_time', '>=', $data['end_time']);
                })
                ->orWhere(function ($q) use ($data) {
                    $q->where('start_time', '>=', $data['start_time'])
                      ->where('end_time', '<=', $data['end_time']);
                });
            })
            ->where('status', '!=', 'cancelled');

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        $conflicts = $query->with(['course:id,course_code', 'room:id,room_code,building'])
                         ->get(['id', 'schedule_code', 'title', 'start_time', 'end_time', 'course_id', 'room_id']);

        return $conflicts->toArray();
    }

    /**
     * Check class conflicts.
     *
     * @param array $data
     * @param int|null $excludeScheduleId
     * @return array
     */
    private function checkClassConflicts(array $data, ?int $excludeScheduleId = null): array
    {
        $query = Schedule::where('date', $data['date'])
            ->where('class_id', $data['class_id'])
            ->where(function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->where('start_time', '<=', $data['start_time'])
                      ->where('end_time', '>', $data['start_time']);
                })
                ->orWhere(function ($q) use ($data) {
                    $q->where('start_time', '<', $data['end_time'])
                      ->where('end_time', '>=', $data['end_time']);
                })
                ->orWhere(function ($q) use ($data) {
                    $q->where('start_time', '>=', $data['start_time'])
                      ->where('end_time', '<=', $data['end_time']);
                });
            })
            ->where('status', '!=', 'cancelled');

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        $conflicts = $query->with(['course:id,course_code', 'lecturer:id,name', 'room:id,room_code'])
                         ->get(['id', 'schedule_code', 'title', 'start_time', 'end_time', 'course_id', 'lecturer_id', 'room_id']);

        return $conflicts->toArray();
    }

    /**
     * Bulk update schedules.
     *
     * @param array $scheduleIds
     * @param array $updates
     * @return int
     */
    public function bulkUpdateSchedules(array $scheduleIds, array $updates): int
    {
        return DB::transaction(function () use ($scheduleIds, $updates) {
            $schedules = Schedule::whereIn('id', $scheduleIds)->get();
            $updatedCount = 0;

            foreach ($schedules as $schedule) {
                try {
                    // Skip locked schedules for critical fields
                    $criticalFields = ['title', 'date', 'start_time', 'end_time', 'room_id', 'lecturer_id', 'course_id'];
                    $canUpdate = true;

                    if ($schedule->is_locked) {
                        foreach ($criticalFields as $field) {
                            if (isset($updates[$field])) {
                                $canUpdate = false;
                                break;
                            }
                        }
                    }

                    if ($canUpdate) {
                        $schedule->update(array_merge($updates, [
                            'updated_by' => auth('sanctum')->id(),
                            'last_modified_at' => now(),
                        ]));
                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to update schedule in bulk operation', [
                        'schedule_id' => $schedule->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Log activity
            Log::channel('activity')->info('Schedules bulk updated', [
                'updated_count' => $updatedCount,
                'total_requested' => count($scheduleIds),
                'updates' => array_keys($updates),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $updatedCount;
        });
    }

    /**
     * Bulk delete schedules.
     *
     * @param array $scheduleIds
     * @return int
     */
    public function bulkDeleteSchedules(array $scheduleIds): int
    {
        return DB::transaction(function () use ($scheduleIds) {
            $schedules = Schedule::whereIn('id', $scheduleIds)->get();
            $deletedCount = 0;

            foreach ($schedules as $schedule) {
                try {
                    // Cannot delete locked or approved schedules
                    if ($schedule->is_locked || $schedule->status === 'approved') {
                        continue;
                    }

                    $schedule->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to delete schedule in bulk operation', [
                        'schedule_id' => $schedule->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Log activity
            Log::channel('activity')->warning('Schedules bulk deleted', [
                'deleted_count' => $deletedCount,
                'total_requested' => count($scheduleIds),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $deletedCount;
        });
    }

    /**
     * Export schedules to file.
     *
     * @param array $filters
     * @param string $format
     * @return string
     */
    public function exportSchedules(array $filters = [], string $format = 'csv'): string
    {
        $schedules = Schedule::with([
            'course:id,course_code,course_name',
            'lecturer:id,name,email',
            'room:id,room_code,name,building',
            'programStudy:id,name,faculty',
            'schoolClass:id,class_name,class_code',
        ]);

        $this->applyFilters($schedules, $filters);
        $schedules = $schedules->get();

        $filename = 'schedules_' . date('Y-m-d_H-i-s') . '.' . $format;

        // This would typically use a library like Laravel Excel
        // For now, return a placeholder path
        return storage_path('app/exports/' . $filename);
    }

    /**
     * Log significant schedule changes.
     *
     * @param Schedule $schedule
     * @param array $originalData
     * @return void
     */
    private function logScheduleChanges(Schedule $schedule, array $originalData): void
    {
        $changes = [];

        foreach ($originalData as $key => $oldValue) {
            $newValue = $schedule->{$key};
            if ($oldValue != $newValue) {
                $changes[] = [
                    'field' => $key,
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            Log::channel('activity')->info('Schedule fields changed', [
                'schedule_id' => $schedule->id,
                'changes' => $changes,
                'user_id' => auth('sanctum')->id(),
            ]);
        }
    }
}