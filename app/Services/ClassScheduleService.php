<?php

namespace App\Services;

use App\Models\ClassSchedule;
use App\Models\ClassScheduleDetail;
use App\Models\Schedule;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Room;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClassScheduleService
{
    /**
     * Get paginated class schedules with filtering and search capabilities.
     *
     * @param array $filters
     * @param int $perPage
     * @param string $sortBy
     * @param string $sortDirection
     * @return array
     */
    public function getClassSchedules(array $filters = [], int $perPage = 15, string $sortBy = 'created_at', string $sortDirection = 'desc'): array
    {
        $query = ClassSchedule::with([
            'programStudy:id,name',
            'schoolClass:id,name,code,batch_year,academic_year',
            'academicYear:id,academic_calendar_year,admission_period',
            'creator:id,name,email',
        ]);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply sorting
        $allowedSorts = ['created_at', 'updated_at', 'title', 'status', 'semester', 'academic_year'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at';
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'desc';

        $classSchedules = $query->orderBy($sortBy, $sortDirection)
                               ->paginate($perPage);

        return [
            'data' => $classSchedules->items(),
            'message' => 'Class schedules retrieved successfully',
            'meta' => [
                'current_page' => $classSchedules->currentPage(),
                'last_page' => $classSchedules->lastPage(),
                'per_page' => $classSchedules->perPage(),
                'total' => $classSchedules->total(),
                'from' => $classSchedules->firstItem(),
                'to' => $classSchedules->lastItem(),
            ],
        ];
    }

    /**
     * Create a new class schedule.
     *
     * @param array $data
     * @return ClassSchedule
     */
    public function createClassSchedule(array $data): ClassSchedule
    {
        return DB::transaction(function () use ($data) {
            // Generate unique schedule code
            $scheduleCode = $this->generateClassScheduleCode($data);

            $classSchedule = ClassSchedule::create(array_merge($data, [
                'schedule_code' => $scheduleCode,
                'status' => 'draft',
                'created_by' => auth('sanctum')->id(),
                'updated_by' => auth('sanctum')->id(),
                'created_from_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]));

            // Log activity
            Log::channel('activity')->info('Class schedule created', [
                'class_schedule_id' => $classSchedule->id,
                'schedule_code' => $classSchedule->schedule_code,
                'title' => $classSchedule->title,
                'program_study' => $classSchedule->programStudy->name,
                'class' => $classSchedule->schoolClass->name,
                'user_id' => auth('sanctum')->id(),
            ]);

            return $classSchedule;
        });
    }

    /**
     * Update an existing class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @param array $data
     * @return ClassSchedule
     */
    public function updateClassSchedule(ClassSchedule $classSchedule, array $data): ClassSchedule
    {
        return DB::transaction(function () use ($classSchedule, $data) {
            $originalData = $classSchedule->only([
                'title', 'status', 'online_percentage', 'offline_percentage'
            ]);

            $classSchedule->update(array_merge($data, [
                'updated_by' => auth('sanctum')->id(),
            ]));

            // Log significant changes
            $this->logClassScheduleChanges($classSchedule, $originalData);

            // Log activity
            Log::channel('activity')->info('Class schedule updated', [
                'class_schedule_id' => $classSchedule->id,
                'schedule_code' => $classSchedule->schedule_code,
                'changes' => array_keys($data),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $classSchedule->fresh();
        });
    }

    /**
     * Delete a class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @return bool
     */
    public function deleteClassSchedule(ClassSchedule $classSchedule): bool
    {
        return DB::transaction(function () use ($classSchedule) {
            $deleted = $classSchedule->delete();

            if ($deleted) {
                Log::channel('activity')->warning('Class schedule deleted', [
                    'class_schedule_id' => $classSchedule->id,
                    'schedule_code' => $classSchedule->schedule_code,
                    'title' => $classSchedule->title,
                    'user_id' => auth('sanctum')->id(),
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Get class schedule statistics.
     *
     * @return array
     */
    public function getClassScheduleStatistics(): array
    {
        $stats = [
            'total_class_schedules' => ClassSchedule::count(),
            'active_class_schedules' => ClassSchedule::where('status', 'active')->count(),
            'draft_class_schedules' => ClassSchedule::where('status', 'draft')->count(),
            'completed_class_schedules' => ClassSchedule::where('status', 'completed')->count(),
            'cancelled_class_schedules' => ClassSchedule::where('status', 'cancelled')->count(),
        ];

        // This week class schedules
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $stats['this_week'] = ClassSchedule::whereBetween('created_at', [$weekStart, $weekEnd])->count();

        // This month class schedules
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $stats['this_month'] = ClassSchedule::whereBetween('created_at', [$monthStart, $monthEnd])->count();

        return $stats;
    }

    /**
     * Add course to class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @param array $data
     * @return ClassScheduleDetail
     */
    public function addCourseToClassSchedule(ClassSchedule $classSchedule, array $data): ClassScheduleDetail
    {
        return DB::transaction(function () use ($classSchedule, $data) {
            // Calculate total meetings based on date range and day of week
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $totalMeetings = $this->calculateTotalMeetings($startDate, $endDate, $data['day_of_week']);

            $classScheduleDetail = ClassScheduleDetail::create(array_merge($data, [
                'class_schedule_id' => $classSchedule->id,
                'total_meetings' => $totalMeetings,
                'created_by' => auth('sanctum')->id(),
                'updated_by' => auth('sanctum')->id(),
            ]));

            // Log activity
            Log::channel('activity')->info('Course added to class schedule', [
                'class_schedule_id' => $classSchedule->id,
                'class_schedule_detail_id' => $classScheduleDetail->id,
                'course_id' => $data['course_id'],
                'lecturer_id' => $data['lecturer_id'],
                'user_id' => auth('sanctum')->id(),
            ]);

            return $classScheduleDetail->load(['course', 'lecturer', 'room']);
        });
    }

    /**
     * Remove course from class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @param int $detailId
     * @return bool
     */
    public function removeCourseFromClassSchedule(ClassSchedule $classSchedule, int $detailId): bool
    {
        return DB::transaction(function () use ($classSchedule, $detailId) {
            $detail = ClassScheduleDetail::where('class_schedule_id', $classSchedule->id)
                                        ->where('id', $detailId)
                                        ->firstOrFail();

            $deleted = $detail->delete();

            if ($deleted) {
                Log::channel('activity')->warning('Course removed from class schedule', [
                    'class_schedule_id' => $classSchedule->id,
                    'class_schedule_detail_id' => $detailId,
                    'user_id' => auth('sanctum')->id(),
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Generate schedules from class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @return array
     */
    public function generateSchedules(ClassSchedule $classSchedule): array
    {
        return DB::transaction(function () use ($classSchedule) {
            $generatedSchedules = [];
            $conflicts = [];

            foreach ($classSchedule->details as $detail) {
                $scheduleDates = $this->generateScheduleDates($detail);

                foreach ($scheduleDates as $index => $date) {
                    try {
                        $schedule = Schedule::create([
                            'class_schedule_id' => $classSchedule->id,
                            'class_schedule_detail_id' => $detail->id,
                            'schedule_code' => $this->generateScheduleCode($classSchedule, $detail, $index + 1),
                            'title' => $detail->course->course_name,
                            'description' => "Generated from class schedule: {$classSchedule->title}",
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $detail->start_time,
                            'end_time' => $detail->end_time,
                            'day_of_week' => $detail->day_of_week,
                            'duration_minutes' => $detail->getDurationInMinutes(),
                            'schedule_type' => $detail->meeting_type,
                            'is_recurring' => false,
                            'course_id' => $detail->course_id,
                            'lecturer_id' => $detail->lecturer_id,
                            'room_id' => $detail->room_id,
                            'program_study_id' => $classSchedule->program_study_id,
                            'class_id' => $classSchedule->class_id,
                            'semester' => $classSchedule->semester,
                            'academic_year' => $classSchedule->academicYear->academic_calendar_year ?? '',
                            'status' => 'approved',
                            'conflict_status' => 'none',
                            'session_type' => $detail->is_online ? 'online' : 'regular',
                            'is_mandatory' => true,
                            'is_online' => $detail->is_online,
                            'meeting_link' => $detail->is_online ? null : '',
                            'is_published' => true,
                            'is_locked' => false,
                            'created_by' => auth('sanctum')->id(),
                            'updated_by' => auth('sanctum')->id(),
                            'created_from_ip' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);

                        $generatedSchedules[] = $schedule;

                    } catch (\Exception $e) {
                        $conflicts[] = [
                            'detail_id' => $detail->id,
                            'course_name' => $detail->course->course_name,
                            'date' => $date->format('Y-m-d'),
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }

            // Update class schedule status
            $classSchedule->update([
                'status' => empty($conflicts) ? 'active' : 'active',
                'updated_by' => auth('sanctum')->id(),
            ]);

            // Log activity
            Log::channel('activity')->info('Schedules generated from class schedule', [
                'class_schedule_id' => $classSchedule->id,
                'total_generated' => count($generatedSchedules),
                'total_conflicts' => count($conflicts),
                'user_id' => auth('sanctum')->id(),
            ]);

            return [
                'generated_schedules' => $generatedSchedules,
                'conflicts' => $conflicts,
                'total_generated' => count($generatedSchedules),
                'total_conflicts' => count($conflicts),
            ];
        });
    }

    /**
     * Get generated schedules for class schedule.
     *
     * @param ClassSchedule $classSchedule
     * @param array $filters
     * @return array
     */
    public function getGeneratedSchedules(ClassSchedule $classSchedule, array $filters = []): array
    {
        $query = Schedule::where('class_schedule_id', $classSchedule->id)
                        ->with(['course:id,course_code,course_name', 'lecturer:id,name', 'room:id,room_code']);

        // Apply filters
        if (!empty($filters['start_date'])) {
            $query->whereDate('date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('date', '<=', $filters['end_date']);
        }

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (!empty($filters['lecturer_id'])) {
            $query->where('lecturer_id', $filters['lecturer_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $schedules = $query->orderBy('date')->orderBy('start_time')->get();

        return [
            'data' => $schedules,
            'total' => $schedules->count(),
        ];
    }

    /**
     * Update class schedule status.
     *
     * @param ClassSchedule $classSchedule
     * @param array $data
     * @return ClassSchedule
     */
    public function updateStatus(ClassSchedule $classSchedule, array $data): ClassSchedule
    {
        return DB::transaction(function () use ($classSchedule, $data) {
            $classSchedule->update(array_merge($data, [
                'updated_by' => auth('sanctum')->id(),
            ]));

            // Log activity
            Log::channel('activity')->info('Class schedule status updated', [
                'class_schedule_id' => $classSchedule->id,
                'old_status' => $classSchedule->getOriginal('status'),
                'new_status' => $data['status'],
                'user_id' => auth('sanctum')->id(),
            ]);

            return $classSchedule->fresh();
        });
    }

    /**
     * Bulk update class schedules.
     *
     * @param array $classScheduleIds
     * @param array $updates
     * @return int
     */
    public function bulkUpdateClassSchedules(array $classScheduleIds, array $updates): int
    {
        return DB::transaction(function () use ($classScheduleIds, $updates) {
            $classSchedules = ClassSchedule::whereIn('id', $classScheduleIds)->get();
            $updatedCount = 0;

            foreach ($classSchedules as $classSchedule) {
                try {
                    $classSchedule->update(array_merge($updates, [
                        'updated_by' => auth('sanctum')->id(),
                    ]));
                    $updatedCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to update class schedule in bulk operation', [
                        'class_schedule_id' => $classSchedule->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Log activity
            Log::channel('activity')->info('Class schedules bulk updated', [
                'updated_count' => $updatedCount,
                'total_requested' => count($classScheduleIds),
                'updates' => array_keys($updates),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $updatedCount;
        });
    }

    /**
     * Bulk delete class schedules.
     *
     * @param array $classScheduleIds
     * @return int
     */
    public function bulkDeleteClassSchedules(array $classScheduleIds): int
    {
        return DB::transaction(function () use ($classScheduleIds) {
            $classSchedules = ClassSchedule::whereIn('id', $classScheduleIds)->get();
            $deletedCount = 0;

            foreach ($classSchedules as $classSchedule) {
                try {
                    $classSchedule->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to delete class schedule in bulk operation', [
                        'class_schedule_id' => $classSchedule->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Log activity
            Log::channel('activity')->warning('Class schedules bulk deleted', [
                'deleted_count' => $deletedCount,
                'total_requested' => count($classScheduleIds),
                'user_id' => auth('sanctum')->id(),
            ]);

            return $deletedCount;
        });
    }

    /**
     * Generate class schedule code.
     *
     * @param array $data
     * @return string
     */
    private function generateClassScheduleCode(array $data): string
    {
        $prefix = 'CSCH';
        $date = date('Ymd');
        $programCode = $data['program_study_id'] ? substr($data['program_study_id'], -3) : 'GEN';
        $random = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));

        return "{$prefix}-{$date}-{$programCode}-{$random}";
    }

    /**
     * Generate schedule code.
     *
     * @param ClassSchedule $classSchedule
     * @param ClassScheduleDetail $detail
     * @param int $meetingNumber
     * @return string
     */
    private function generateScheduleCode(ClassSchedule $classSchedule, ClassScheduleDetail $detail, int $meetingNumber): string
    {
        $prefix = 'SCH';
        $date = date('Ymd');
        $courseCode = $detail->course->course_code ?? 'GEN';
        $meetingNum = str_pad($meetingNumber, 2, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$courseCode}-{$meetingNum}";
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
                    $query->where('title', 'like', "%{$value}%")
                          ->orWhere('schedule_code', 'like', "%{$value}%");
                    break;
                case 'program_study_id':
                    $query->where('program_study_id', $value);
                    break;
                case 'class_id':
                    $query->where('class_id', $value);
                    break;
                case 'academic_year_id':
                    $query->where('academic_year_id', $value);
                    break;
                case 'semester':
                    $query->where('semester', $value);
                    break;
                case 'status':
                    $query->where('status', $value);
                    break;
                // Ignore unsupported filters like 'day' and 'room_id' for ClassSchedule
                case 'day':
                case 'room_id':
                    // These filters are not applicable to ClassSchedule queries
                    break;
            }
        }
    }

    /**
     * Calculate total meetings between dates for specific day of week.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $dayOfWeek
     * @return int
     */
    private function calculateTotalMeetings(Carbon $startDate, Carbon $endDate, string $dayOfWeek): int
    {
        $meetings = 0;
        $current = $startDate->copy();

        while ($current <= $endDate) {
            if (strtolower($current->dayName) === $dayOfWeek) {
                $meetings++;
            }
            $current->addDay();
        }

        return $meetings;
    }

    /**
     * Generate schedule dates based on class schedule detail.
     *
     * @param ClassScheduleDetail $detail
     * @return array
     */
    private function generateScheduleDates(ClassScheduleDetail $detail): array
    {
        $dates = [];
        $startDate = Carbon::parse($detail->start_date);
        $endDate = Carbon::parse($detail->end_date);
        $meetingCount = 0;

        $current = $startDate->copy();
        while ($current <= $endDate && $meetingCount < $detail->total_meetings) {
            if (strtolower($current->dayName) === $detail->day_of_week) {
                $dates[] = $current->copy();
                $meetingCount++;
            }
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Log significant class schedule changes.
     *
     * @param ClassSchedule $classSchedule
     * @param array $originalData
     * @return void
     */
    private function logClassScheduleChanges(ClassSchedule $classSchedule, array $originalData): void
    {
        $changes = [];

        foreach ($originalData as $key => $oldValue) {
            $newValue = $classSchedule->{$key};
            if ($oldValue != $newValue) {
                $changes[] = [
                    'field' => $key,
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            Log::channel('activity')->info('Class schedule fields changed', [
                'class_schedule_id' => $classSchedule->id,
                'changes' => $changes,
                'user_id' => auth('sanctum')->id(),
            ]);
        }
    }
}