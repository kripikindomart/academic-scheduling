<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ProgramStudyController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\LecturerController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ScheduleController;

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
});

// User management routes
Route::middleware(['auth:sanctum'])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->middleware('permission:users.view');
    Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create');
    Route::get('/{user}', [UserController::class, 'show'])->middleware('permission:users.view');
    Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:users.edit');
    Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete');
    Route::get('/roles', [UserController::class, 'roles'])->middleware('permission:users.view');
    Route::get('/permissions', [UserController::class, 'permissions'])->middleware('permission:users.view');
});

// Course management routes
Route::middleware(['auth:sanctum'])->prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->middleware('permission:courses.view');
    Route::post('/', [CourseController::class, 'store'])->middleware('permission:courses.create');
    Route::get('/available', [CourseController::class, 'available'])->middleware('permission:courses.view');
    Route::get('/statistics', [CourseController::class, 'statistics'])->middleware('permission:courses.view');
    Route::get('/{course}', [CourseController::class, 'show'])->middleware('permission:courses.view');
    Route::put('/{course}', [CourseController::class, 'update'])->middleware('permission:courses.edit');
    Route::delete('/{course}', [CourseController::class, 'destroy'])->middleware('permission:courses.delete');
    Route::post('/{course}/prerequisites', [CourseController::class, 'addPrerequisite'])->middleware('permission:courses.edit');
    Route::delete('/{course}/prerequisites', [CourseController::class, 'removePrerequisite'])->middleware('permission:courses.edit');

    // Bulk operations
    Route::post('/bulk-update', [CourseController::class, 'bulkUpdate'])->middleware('permission:courses.edit');
    Route::post('/bulk-delete', [CourseController::class, 'bulkDelete'])->middleware('permission:courses.delete');
    Route::post('/bulk-toggle-status', [CourseController::class, 'bulkToggleStatus'])->middleware('permission:courses.edit');
    Route::post('/import', [CourseController::class, 'import'])->middleware('permission:courses.create');
    Route::get('/export', [CourseController::class, 'export'])->middleware('permission:courses.view');
});

// Program study management routes
Route::middleware(['auth:sanctum'])->prefix('program-studies')->group(function () {
    Route::get('/', [ProgramStudyController::class, 'index'])->middleware('permission:program_studies.view');
    Route::post('/', [ProgramStudyController::class, 'store'])->middleware('permission:program_studies.create');
    Route::get('/faculties', [ProgramStudyController::class, 'faculties'])->middleware('permission:program_studies.view');
    Route::get('/statistics', [ProgramStudyController::class, 'statistics'])->middleware('permission:program_studies.view');
    Route::get('/trash', [ProgramStudyController::class, 'trash'])->middleware('permission:program_studies.view');
    Route::get('/export', [ProgramStudyController::class, 'export'])->middleware('permission:program_studies.view');
    Route::get('/download/{filename}', [ProgramStudyController::class, 'download'])->middleware('permission:program_studies.view');

    // Bulk operations
    Route::post('/bulk-update', [ProgramStudyController::class, 'bulkUpdate'])->middleware('permission:program_studies.edit');
    Route::post('/bulk-delete', [ProgramStudyController::class, 'bulkDelete'])->middleware('permission:program_studies.delete');
    Route::post('/bulk-toggle-status', [ProgramStudyController::class, 'bulkToggleStatus'])->middleware('permission:program_studies.edit');
    Route::post('/import', [ProgramStudyController::class, 'import'])->middleware('permission:program_studies.create');

    // Specific program study routes - must come after static routes
    Route::get('/{program_study}', [ProgramStudyController::class, 'show'])->middleware('permission:program_studies.view');
    Route::put('/{program_study}', [ProgramStudyController::class, 'update'])->middleware('permission:program_studies.edit');
    Route::delete('/{program_study}/force-delete', [ProgramStudyController::class, 'forceDelete'])->middleware('permission:program_studies.delete');
    Route::delete('/{program_study}', [ProgramStudyController::class, 'destroy'])->middleware('permission:program_studies.delete');
    Route::post('/{program_study}/restore', [ProgramStudyController::class, 'restore'])->middleware('permission:program_studies.delete');
    Route::put('/{program_study}/toggle-status', [ProgramStudyController::class, 'toggleStatus'])->middleware('permission:program_studies.edit');
    Route::post('/{program_study}/duplicate', [ProgramStudyController::class, 'duplicate'])->middleware('permission:program_studies.create');
    Route::post('/{program_study}/lecturers', [ProgramStudyController::class, 'assignLecturer'])->middleware('permission:program_studies.edit');
    Route::delete('/{program_study}/lecturers', [ProgramStudyController::class, 'removeLecturer'])->middleware('permission:program_studies.edit');
});

// Student management routes
Route::middleware(['auth:sanctum'])->prefix('students')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->middleware('permission:students.view');
    Route::post('/', [StudentController::class, 'store'])->middleware('permission:students.create');
    Route::get('/statistics', [StudentController::class, 'statistics'])->middleware('permission:students.view');
    Route::get('/active', [StudentController::class, 'getActive'])->middleware('permission:students.view');
    Route::get('/low-gpa', [StudentController::class, 'getLowGpaStudents'])->middleware('permission:students.view');
    Route::get('/search-suggestions', [StudentController::class, 'searchSuggestions'])->middleware('permission:students.view');
    Route::post('/bulk-update', [StudentController::class, 'bulkUpdate'])->middleware('permission:students.edit');
    Route::post('/import', [StudentController::class, 'import'])->middleware('permission:students.create');
    Route::get('/export', [StudentController::class, 'export'])->middleware('permission:students.view');
    Route::post('/restore/{id}', [StudentController::class, 'restore'])->middleware('permission:students.delete');
    Route::delete('/force-delete/{id}', [StudentController::class, 'forceDelete'])->middleware('permission:students.delete');

    Route::get('/program-study/{programStudyId}', [StudentController::class, 'getByProgramStudy'])->middleware('permission:students.view');
    Route::get('/batch-year/{batchYear}', [StudentController::class, 'getByBatchYear'])->middleware('permission:students.view');

    Route::get('/{student}', [StudentController::class, 'show'])->middleware('permission:students.view');
    Route::put('/{student}', [StudentController::class, 'update'])->middleware('permission:students.edit');
    Route::delete('/{student}', [StudentController::class, 'destroy'])->middleware('permission:students.delete');
    Route::get('/{student}/academic-progress', [StudentController::class, 'academicProgress'])->middleware('permission:students.view');
    Route::put('/{student}/status', [StudentController::class, 'updateStatus'])->middleware('permission:students.edit');
    Route::get('/{student}/attendance-summary', [StudentController::class, 'attendanceSummary'])->middleware('permission:students.view');
});

// Lecturer management routes
Route::middleware(['auth:sanctum'])->prefix('lecturers')->group(function () {
    Route::get('/', [LecturerController::class, 'index'])->middleware('permission:lecturers.view');
    Route::post('/', [LecturerController::class, 'store'])->middleware('permission:lecturers.create');
    Route::get('/statistics', [LecturerController::class, 'statistics'])->middleware('permission:lecturers.view');
    Route::get('/active', [LecturerController::class, 'getActive'])->middleware('permission:lecturers.view');
    Route::get('/high-workload', [LecturerController::class, 'getHighWorkloadLecturers'])->middleware('permission:lecturers.view');
    Route::get('/search-suggestions', [LecturerController::class, 'searchSuggestions'])->middleware('permission:lecturers.view');
    Route::get('/for-scheduling', [LecturerController::class, 'getForScheduling'])->middleware('permission:lecturers.view');
    Route::post('/bulk-update', [LecturerController::class, 'bulkUpdate'])->middleware('permission:lecturers.edit');
    Route::post('/import', [LecturerController::class, 'import'])->middleware('permission:lecturers.create');
    Route::get('/export', [LecturerController::class, 'export'])->middleware('permission:lecturers.view');
    Route::post('/restore/{id}', [LecturerController::class, 'restore'])->middleware('permission:lecturers.delete');
    Route::delete('/force-delete/{id}', [LecturerController::class, 'forceDelete'])->middleware('permission:lecturers.delete');

    Route::get('/program-study/{programStudyId}', [LecturerController::class, 'getByProgramStudy'])->middleware('permission:lecturers.view');
    Route::get('/faculty/{faculty}', [LecturerController::class, 'getByFaculty'])->middleware('permission:lecturers.view');
    Route::get('/employment-type/{type}', [LecturerController::class, 'getByEmploymentType'])->middleware('permission:lecturers.view');
    Route::get('/available-for-course/{course}', [LecturerController::class, 'availableForCourse'])->middleware('permission:lecturers.view');

    Route::get('/{lecturer}', [LecturerController::class, 'show'])->middleware('permission:lecturers.view');
    Route::put('/{lecturer}', [LecturerController::class, 'update'])->middleware('permission:lecturers.edit');
    Route::delete('/{lecturer}', [LecturerController::class, 'destroy'])->middleware('permission:lecturers.delete');
    Route::get('/{lecturer}/teaching-load', [LecturerController::class, 'teachingLoad'])->middleware('permission:lecturers.view');
    Route::put('/{lecturer}/status', [LecturerController::class, 'updateStatus'])->middleware('permission:lecturers.edit');
    Route::get('/{lecturer}/attendance-summary', [LecturerController::class, 'attendanceSummary'])->middleware('permission:lecturers.view');
    Route::post('/{lecturer}/assign-course/{course}', [LecturerController::class, 'assignCourse'])->middleware('permission:lecturers.edit');
});

// Room management routes
Route::middleware(['auth:sanctum'])->prefix('rooms')->group(function () {
    Route::get('/', [RoomController::class, 'index'])->middleware('permission:rooms.view');
    Route::post('/', [RoomController::class, 'store'])->middleware('permission:rooms.create');
    Route::get('/statistics', [RoomController::class, 'statistics'])->middleware('permission:rooms.view');
    Route::get('/available-for-schedule', [RoomController::class, 'getAvailableForSchedule'])->middleware('permission:rooms.view');
    Route::get('/available', [RoomController::class, 'getAvailable'])->middleware('permission:rooms.view');
    Route::get('/needing-maintenance', [RoomController::class, 'getNeedingMaintenance'])->middleware('permission:rooms.view');
    Route::get('/utilization-report', [RoomController::class, 'getUtilizationReport'])->middleware('permission:rooms.view');
    Route::get('/search-suggestions', [RoomController::class, 'searchSuggestions'])->middleware('permission:rooms.view');
    Route::get('/by-capacity', [RoomController::class, 'getByCapacity'])->middleware('permission:rooms.view');
    Route::post('/bulk-update', [RoomController::class, 'bulkUpdate'])->middleware('permission:rooms.edit');
    Route::post('/import', [RoomController::class, 'import'])->middleware('permission:rooms.create');
    Route::get('/export', [RoomController::class, 'export'])->middleware('permission:rooms.view');
    Route::post('/restore/{id}', [RoomController::class, 'restore'])->middleware('permission:rooms.delete');
    Route::delete('/force-delete/{id}', [RoomController::class, 'forceDelete'])->middleware('permission:rooms.delete');

    Route::get('/building/{building}', [RoomController::class, 'getByBuilding'])->middleware('permission:rooms.view');
    Route::get('/type/{type}', [RoomController::class, 'getByType'])->middleware('permission:rooms.view');

    Route::get('/{room}', [RoomController::class, 'show'])->middleware('permission:rooms.view');
    Route::put('/{room}', [RoomController::class, 'update'])->middleware('permission:rooms.edit');
    Route::delete('/{room}', [RoomController::class, 'destroy'])->middleware('permission:rooms.delete');
    Route::get('/{room}/schedule', [RoomController::class, 'getSchedule'])->middleware('permission:rooms.view');
    Route::put('/{room}/availability', [RoomController::class, 'updateAvailability'])->middleware('permission:rooms.edit');
    Route::post('/{room}/schedule-maintenance', [RoomController::class, 'scheduleMaintenance'])->middleware('permission:rooms.edit');
    Route::post('/{room}/complete-maintenance', [RoomController::class, 'completeMaintenance'])->middleware('permission:rooms.edit');
});

// Schedule management routes
Route::middleware(['auth:sanctum'])->prefix('schedules')->group(function () {
    Route::get('/', [ScheduleController::class, 'index'])->middleware('permission:schedules.view');
    Route::post('/', [ScheduleController::class, 'store'])->middleware('permission:schedules.create');
    Route::get('/statistics', [ScheduleController::class, 'statistics'])->middleware('permission:schedules.view');
    Route::get('/check-conflicts', [ScheduleController::class, 'checkConflicts'])->middleware('permission:schedules.view');
    Route::get('/available-rooms', [ScheduleController::class, 'getAvailableRooms'])->middleware('permission:schedules.view');
    Route::get('/available-lecturers', [ScheduleController::class, 'getAvailableLecturers'])->middleware('permission:schedules.view');
    Route::get('/date-range', [ScheduleController::class, 'getByDateRange'])->middleware('permission:schedules.view');
    Route::get('/calendar', [ScheduleController::class, 'getCalendarView'])->middleware('permission:schedules.view');
    Route::get('/course/{courseId}', [ScheduleController::class, 'getByCourse'])->middleware('permission:schedules.view');
    Route::get('/lecturer/{lecturerId}', [ScheduleController::class, 'getByLecturer'])->middleware('permission:schedules.view');
    Route::get('/room/{roomId}', [ScheduleController::class, 'getByRoom'])->middleware('permission:schedules.view');

    Route::post('/bulk-update', [ScheduleController::class, 'bulkUpdate'])->middleware('permission:schedules.edit');
    Route::post('/bulk-delete', [ScheduleController::class, 'bulkDelete'])->middleware('permission:schedules.delete');
    Route::get('/export', [ScheduleController::class, 'export'])->middleware('permission:schedules.view');
    Route::post('/import', [ScheduleController::class, 'import'])->middleware('permission:schedules.create');

    Route::get('/{schedule}', [ScheduleController::class, 'show'])->middleware('permission:schedules.view');
    Route::put('/{schedule}', [ScheduleController::class, 'update'])->middleware('permission:schedules.edit');
    Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->middleware('permission:schedules.delete');

    // Workflow operations
    Route::post('/{schedule}/approve', [ScheduleController::class, 'approve'])->middleware('permission:schedules.approve');
    Route::post('/{schedule}/reject', [ScheduleController::class, 'reject'])->middleware('permission:schedules.reject');
    Route::post('/{schedule}/cancel', [ScheduleController::class, 'cancel'])->middleware('permission:schedules.cancel');
});

// Dashboard route
Route::get('/dashboard', function () {
    return response()->json([
        'message' => 'Welcome to Academic Scheduling System Dashboard',
        'version' => '1.0.0',
        'status' => 'success'
    ]);
})->middleware('auth:sanctum');

// API info route
Route::get('/', function () {
    return response()->json([
        'message' => 'Academic Scheduling System API',
        'version' => '1.0.0',
        'status' => 'success',
        'endpoints' => [
            'auth' => [
                'login' => '/api/auth/login',
                'logout' => '/api/auth/logout',
                'register' => '/api/auth/register',
                'user' => '/api/auth/user',
                'refresh' => '/api/auth/refresh'
            ],
            'users' => '/api/users',
            'courses' => '/api/courses',
            'program_studies' => '/api/program-studies',
            'students' => '/api/students',
            'lecturers' => '/api/lecturers',
            'rooms' => '/api/rooms',
            'dashboard' => '/api/dashboard'
        ]
    ]);
});