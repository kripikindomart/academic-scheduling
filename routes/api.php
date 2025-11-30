<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ProgramStudyController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\LecturerController;
use App\Http\Controllers\Api\LecturerImportController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\ConflictDetectionController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\AcademicYearController;

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

// Academic Year management routes
Route::middleware(['auth:sanctum'])->prefix('academic-years')->group(function () {
    // Basic CRUD
    Route::get('/', [AcademicYearController::class, 'index'])->middleware('permission:academic_years.view');
    Route::post('/', [AcademicYearController::class, 'store'])->middleware('permission:academic_years.create');

    // Special endpoints (must come before {id} routes)
    Route::get('/active', [AcademicYearController::class, 'active'])->middleware('permission:academic_years.view');
    Route::get('/statistics', [AcademicYearController::class, 'statistics'])->middleware('permission:academic_years.view');
    Route::post('/set-active', [AcademicYearController::class, 'setActive'])->middleware('permission:academic_years.manage');

    // Individual academic year actions
    Route::post('/{id}/duplicate', [AcademicYearController::class, 'duplicate'])->middleware('permission:academic_years.create');
    Route::post('/{id}/toggle-active', [AcademicYearController::class, 'toggleActive'])->middleware('permission:academic_years.manage');

    // Generic {id} routes (must come last)
    Route::get('/{id}', [AcademicYearController::class, 'show'])->middleware('permission:academic_years.view');
    Route::put('/{id}', [AcademicYearController::class, 'update'])->middleware('permission:academic_years.edit');
    Route::delete('/{id}', [AcademicYearController::class, 'destroy'])->middleware('permission:academic_years.delete');
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
    // Basic CRUD
    Route::get('/', [StudentController::class, 'index'])->middleware('permission:students.view');
    Route::post('/', [StudentController::class, 'store'])->middleware('permission:students.create');
    Route::get('/statistics', [StudentController::class, 'statistics'])->middleware('permission:students.view');
    Route::get('/active', [StudentController::class, 'getActive'])->middleware('permission:students.view');
    Route::get('/low-gpa', [StudentController::class, 'getLowGpaStudents'])->middleware('permission:students.view');
    Route::get('/search-suggestions', [StudentController::class, 'searchSuggestions'])->middleware('permission:students.view');

    // Import/Export
    Route::get('/download-template', [StudentController::class, 'downloadTemplate'])->middleware('permission:students.create');
    Route::post('/import', [StudentController::class, 'import'])->middleware('permission:students.create');
    Route::get('/export', [StudentController::class, 'export'])->middleware('permission:students.view');

    // Bulk Operations
    Route::post('/bulk-update', [StudentController::class, 'bulkUpdate'])->middleware('permission:students.edit');
    Route::post('/bulk-delete', [StudentController::class, 'bulkDelete'])->middleware('permission:students.delete');
    Route::post('/bulk-restore', [StudentController::class, 'bulkRestore'])->middleware('permission:students.delete');
    Route::post('/bulk-force-delete', [StudentController::class, 'bulkForceDelete'])->middleware('permission:students.delete');
    Route::post('/bulk-create-user-accounts', [StudentController::class, 'bulkCreateUserAccounts'])->middleware('permission:students.edit');

    // Trash Management (More specific routes first)
    Route::get('/trash', [StudentController::class, 'trash'])->middleware('permission:students.view');
    Route::post('/{id}/restore', [StudentController::class, 'restore'])->middleware('permission:students.delete');
    Route::delete('/force-delete/{id}', [StudentController::class, 'forceDelete'])->middleware('permission:students.delete');

    // Filtering Routes
    Route::get('/program-study/{programStudyId}', [StudentController::class, 'getByProgramStudy'])->middleware('permission:students.view');
    Route::get('/batch-year/{batchYear}', [StudentController::class, 'getByBatchYear'])->middleware('permission:students.view');
    Route::get('/class/{className}', [StudentController::class, 'getByClass'])->middleware('permission:students.view');
    Route::get('/semester/{semester}', [StudentController::class, 'getBySemester'])->middleware('permission:students.view');
    Route::get('/gender/{gender}', [StudentController::class, 'getByGender'])->middleware('permission:students.view');
    Route::get('/gpa-range', [StudentController::class, 'getByGpaRange'])->middleware('permission:students.view');
    Route::get('/age-range', [StudentController::class, 'getByAgeRange'])->middleware('permission:students.view');
    Route::get('/enrollment-year/{year}', [StudentController::class, 'getByEnrollmentYear'])->middleware('permission:students.view');
    Route::get('/expected-graduation/{year}', [StudentController::class, 'getExpectedGraduation'])->middleware('permission:students.view');

    // Status-based Routes
    Route::get('/graduated', [StudentController::class, 'getGraduated'])->middleware('permission:students.view');
    Route::get('/on-leave', [StudentController::class, 'getOnLeave'])->middleware('permission:students.view');
    Route::get('/dropped-out', [StudentController::class, 'getDroppedOut'])->middleware('permission:students.view');
    Route::get('/regular', [StudentController::class, 'getRegular'])->middleware('permission:students.view');
    Route::get('/non-regular', [StudentController::class, 'getNonRegular'])->middleware('permission:students.view');

    // Individual Student Routes
    Route::get('/{student}', [StudentController::class, 'show'])->middleware('permission:students.view');
    Route::put('/{student}', [StudentController::class, 'update'])->middleware('permission:students.edit');
    Route::delete('/{student}', [StudentController::class, 'destroy'])->middleware('permission:students.delete');
    Route::post('/{student}/duplicate', [StudentController::class, 'duplicate'])->middleware('permission:students.create');

    // Student-specific operations
    Route::get('/{student}/academic-progress', [StudentController::class, 'academicProgress'])->middleware('permission:students.view');
    Route::put('/{student}/status', [StudentController::class, 'updateStatus'])->middleware('permission:students.edit');
    Route::get('/{student}/attendance-summary', [StudentController::class, 'attendanceSummary'])->middleware('permission:students.view');
    Route::post('/{student}/create-user-account', [StudentController::class, 'createUserAccount'])->middleware('permission:students.edit');
    Route::delete('/{student}/user-account', [StudentController::class, 'removeUserAccount'])->middleware('permission:students.edit');

    // Academic operations
    Route::post('/{student}/enroll-course/{course}', [StudentController::class, 'enrollInCourse'])->middleware('permission:students.edit');
    Route::delete('/{student}/drop-course/{course}', [StudentController::class, 'dropFromCourse'])->middleware('permission:students.edit');
    Route::get('/{student}/grades', [StudentController::class, 'getGrades'])->middleware('permission:students.view');
    Route::get('/{student}/schedule', [StudentController::class, 'getSchedule'])->middleware('permission:students.view');
    Route::get('/{student}/transcript', [StudentController::class, 'getTranscript'])->middleware('permission:students.view');

    // File operations
    Route::post('/{student}/upload-photo', [StudentController::class, 'uploadPhoto'])->middleware('permission:students.edit');
    Route::delete('/{student}/remove-photo', [StudentController::class, 'removePhoto'])->middleware('permission:students.edit');
    Route::get('/{student}/print-card', [StudentController::class, 'printStudentCard'])->middleware('permission:students.view');
    Route::get('/{student}/print-transcript', [StudentController::class, 'printTranscript'])->middleware('permission:students.view');

    // Reporting
    Route::get('/reports/data', [StudentController::class, 'getReportData'])->middleware('permission:students.view');
    Route::get('/reports/academic', [StudentController::class, 'getAcademicReport'])->middleware('permission:students.view');
    Route::get('/reports/attendance', [StudentController::class, 'getAttendanceReport'])->middleware('permission:students.view');
    Route::get('/reports/performance', [StudentController::class, 'getPerformanceReport'])->middleware('permission:students.view');

    // Export selected students
    Route::post('/export-selected', [StudentController::class, 'exportSelected'])->middleware('permission:students.view');
});

// Class management routes
Route::middleware(['auth:sanctum'])->prefix('classes')->group(function () {
    // Basic CRUD
    Route::get('/', [ClassController::class, 'index'])->middleware('permission:classes.view');
    Route::post('/', [ClassController::class, 'store'])->middleware('permission:classes.create');
    Route::get('/statistics', [ClassController::class, 'statistics'])->middleware('permission:classes.view');
    Route::get('/available', [ClassController::class, 'available'])->middleware('permission:classes.view');

    // Bulk operations
    Route::post('/bulk-update', [ClassController::class, 'bulkUpdate'])->middleware('permission:classes.edit');
    Route::post('/bulk-delete', [ClassController::class, 'bulkDelete'])->middleware('permission:classes.delete');

    // Trash Management
    Route::get('/trashed', [ClassController::class, 'trashed'])->middleware('permission:classes.view');
    Route::post('/bulk-restore', [ClassController::class, 'bulkRestore'])->middleware('permission:classes.delete');
    Route::post('/bulk-force-delete', [ClassController::class, 'bulkForceDelete'])->middleware('permission:classes.delete');

    // Auto-generation and enrollment
    Route::post('/generate-codes', [ClassController::class, 'generateClassCodes'])->middleware('permission:classes.create');
    Route::post('/auto-enroll', [ClassController::class, 'autoEnrollStudents'])->middleware('permission:classes.edit');

    // Reporting
    Route::get('/enrollment-report', [ClassController::class, 'enrollmentReport'])->middleware('permission:classes.view');

    // Filtered routes
    Route::get('/program-study/{programStudy}', [ClassController::class, 'getByProgramStudy'])->middleware('permission:classes.view');
    Route::get('/batch-year/{batchYear}', [ClassController::class, 'getByBatchYear'])->middleware('permission:classes.view');
    Route::get('/academic-year/{academicYear}', [ClassController::class, 'getByAcademicYear'])->middleware('permission:classes.view');

    // Individual class routes
    Route::get('/{class}', [ClassController::class, 'show'])->middleware('permission:classes.view');
    Route::put('/{class}', [ClassController::class, 'update'])->middleware('permission:classes.edit');
    Route::delete('/{class}', [ClassController::class, 'destroy'])->middleware('permission:classes.delete');
    Route::post('/{class}/restore', [ClassController::class, 'restore'])->middleware('permission:classes.delete');
    Route::delete('/{class}/force-delete', [ClassController::class, 'forceDelete'])->middleware('permission:classes.delete');

    // Student management in classes
    Route::get('/{class}/students', [ClassController::class, 'students'])->middleware('permission:classes.view');
    Route::post('/{class}/enroll-students', [ClassController::class, 'enrollStudents'])->middleware('permission:classes.edit');
    Route::post('/{class}/remove-student', [ClassController::class, 'removeStudent'])->middleware('permission:classes.edit');
    Route::post('/{class}/transfer-student', [ClassController::class, 'transferStudent'])->middleware('permission:classes.edit');
    Route::post('/{class}/update-student-status', [ClassController::class, 'updateStudentStatus'])->middleware('permission:classes.edit');
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
    Route::post('/bulk-delete', [LecturerController::class, 'bulkDelete'])->middleware('permission:lecturers.delete');
    Route::post('/bulk-restore', [LecturerController::class, 'bulkRestore'])->middleware('permission:lecturers.delete');
    Route::post('/bulk-force-delete', [LecturerController::class, 'bulkForceDelete'])->middleware('permission:lecturers.delete');
    Route::post('/import', [LecturerController::class, 'import'])->middleware('permission:lecturers.create');
    Route::get('/export', [LecturerController::class, 'export'])->middleware('permission:lecturers.view');
    Route::get('/trash', [LecturerController::class, 'trash'])->middleware('permission:lecturers.view');
    Route::post('/{id}/restore', [LecturerController::class, 'restore'])->middleware('permission:lecturers.delete');
    Route::delete('/force-delete/{id}', [LecturerController::class, 'forceDelete'])->middleware('permission:lecturers.delete');

    Route::get('/program-study/{programStudyId}', [LecturerController::class, 'getByProgramStudy'])->middleware('permission:lecturers.view');
    Route::get('/faculty/{faculty}', [LecturerController::class, 'getByFaculty'])->middleware('permission:lecturers.view');
    Route::get('/employment-type/{type}', [LecturerController::class, 'getByEmploymentType'])->middleware('permission:lecturers.view');
    Route::get('/available-for-course/{course}', [LecturerController::class, 'availableForCourse'])->middleware('permission:lecturers.view');

    Route::get('/{lecturer}', [LecturerController::class, 'show'])->middleware('permission:lecturers.view');
    Route::put('/{lecturer}', [LecturerController::class, 'update'])->middleware('permission:lecturers.edit');
    Route::post('/{lecturer}/duplicate', [LecturerController::class, 'duplicate'])->middleware('permission:lecturers.create');
    Route::post('/{lecturer}/create-user-account', [LecturerController::class, 'createUserAccount'])->middleware('permission:lecturers.edit');
    Route::post('/bulk-create-user-accounts', [LecturerController::class, 'bulkCreateUserAccounts'])->middleware('permission:lecturers.edit');
    Route::delete('/{lecturer}', [LecturerController::class, 'destroy'])->middleware('permission:lecturers.delete');
    Route::get('/{lecturer}/teaching-load', [LecturerController::class, 'teachingLoad'])->middleware('permission:lecturers.view');
    Route::put('/{lecturer}/status', [LecturerController::class, 'updateStatus'])->middleware('permission:lecturers.edit');
    Route::get('/{lecturer}/attendance-summary', [LecturerController::class, 'attendanceSummary'])->middleware('permission:lecturers.view');
    Route::post('/{lecturer}/assign-course/{course}', [LecturerController::class, 'assignCourse'])->middleware('permission:lecturers.edit');

    // Import/Export routes
    Route::prefix('import')->group(function () {
        Route::get('/template', [LecturerImportController::class, 'downloadTemplate'])->middleware('permission:lecturers.create');
        Route::post('/validate', [LecturerImportController::class, 'validateFile'])->middleware('permission:lecturers.create');
        Route::post('/revalidate', [LecturerImportController::class, 'revalidateData'])->middleware('permission:lecturers.create');
        Route::post('/process', [LecturerImportController::class, 'processImport'])->middleware('permission:lecturers.create');
        Route::get('/program-studies', [LecturerImportController::class, 'getProgramStudies'])->middleware('permission:lecturers.create');
        Route::post('/check-duplicates', [LecturerImportController::class, 'checkDuplicates'])->middleware('permission:lecturers.create');
    });
});

// Building management routes
Route::middleware(['auth:sanctum'])->prefix('buildings')->group(function () {
    Route::get('/', [BuildingController::class, 'index']);
    Route::post('/', [BuildingController::class, 'store']);
    Route::get('/statistics', [BuildingController::class, 'statistics']);
    Route::get('/with-room-count', [BuildingController::class, 'getWithRoomCount']);
    Route::get('/type/{type}', [BuildingController::class, 'getByType']);
    Route::get('/trash', [BuildingController::class, 'trash']);
    Route::get('/search-suggestions', [BuildingController::class, 'searchSuggestions']);

    // Bulk operations
    Route::post('/bulk-update', [BuildingController::class, 'bulkUpdate']);
    Route::post('/bulk-delete', [BuildingController::class, 'bulkDelete']);
    Route::post('/bulk-toggle-status', [BuildingController::class, 'bulkToggleStatus']);
    Route::post('/import', [BuildingController::class, 'import']);
    Route::get('/export', [BuildingController::class, 'export']);
    Route::post('/restore/{id}', [BuildingController::class, 'restore']);
    Route::delete('/force-delete/{id}', [BuildingController::class, 'forceDelete']);

    Route::get('/{building}', [BuildingController::class, 'show']);
    Route::put('/{building}', [BuildingController::class, 'update']);
    Route::delete('/{building}', [BuildingController::class, 'destroy']);
    Route::post('/{building}/toggle-status', [BuildingController::class, 'toggleStatus']);
    Route::post('/{building}/duplicate', [BuildingController::class, 'duplicate']);
});

// Room management routes
Route::middleware(['auth:sanctum'])->prefix('rooms')->group(function () {
    Route::get('/', [RoomController::class, 'index'])->middleware('permission:rooms.view');
    Route::post('/', [RoomController::class, 'store'])->middleware('permission:rooms.create');
    Route::post('/check-duplicates', [RoomController::class, 'checkDuplicates'])->middleware('permission:rooms.create');
    Route::get('/statistics', [RoomController::class, 'statistics'])->middleware('permission:rooms.view');
    Route::get('/available-for-schedule', [RoomController::class, 'getAvailableForSchedule'])->middleware('permission:rooms.view');
    Route::get('/available', [RoomController::class, 'getAvailable'])->middleware('permission:rooms.view');
    Route::get('/needing-maintenance', [RoomController::class, 'getNeedingMaintenance'])->middleware('permission:rooms.view');
    Route::get('/utilization-report', [RoomController::class, 'getUtilizationReport'])->middleware('permission:rooms.view');
    Route::get('/search-suggestions', [RoomController::class, 'searchSuggestions'])->middleware('permission:rooms.view');
    Route::get('/by-capacity', [RoomController::class, 'getByCapacity'])->middleware('permission:rooms.view');
    Route::get('/trash', [RoomController::class, 'trash'])->middleware('permission:rooms.view');
    Route::post('/bulk-update', [RoomController::class, 'bulkUpdate'])->middleware('permission:rooms.edit');
    Route::post('/import', [RoomController::class, 'import'])->middleware('permission:rooms.create');
    Route::get('/export', [RoomController::class, 'export'])->middleware('permission:rooms.view');
    Route::put('/{room}/toggle-status', [RoomController::class, 'toggleStatus'])->middleware('permission:rooms.edit');
    Route::post('/{room}/duplicate', [RoomController::class, 'duplicate'])->middleware('permission:rooms.create');
    Route::post('/bulk-delete', [RoomController::class, 'bulkDelete'])->middleware('permission:rooms.delete');
    Route::post('/bulk-force-delete', [RoomController::class, 'bulkForceDelete'])->middleware('permission:rooms.delete');
    Route::post('/bulk-toggle-status', [RoomController::class, 'bulkToggleStatus'])->middleware('permission:rooms.edit');
    Route::get('/download/{filename}', [RoomController::class, 'download'])->middleware('permission:rooms.view');
    Route::get('/building/{building}', [RoomController::class, 'getByBuilding'])->middleware('permission:rooms.view');
    Route::get('/type/{type}', [RoomController::class, 'getByType'])->middleware('permission:rooms.view');

    Route::get('/{room}', [RoomController::class, 'show'])->middleware('permission:rooms.view');
    Route::put('/{room}', [RoomController::class, 'update'])->middleware('permission:rooms.edit');
    Route::delete('/{room}', [RoomController::class, 'destroy'])->middleware('permission:rooms.delete');
    Route::delete('/{room}/force-delete', [RoomController::class, 'forceDelete'])->middleware('permission:rooms.delete');
    Route::post('/{room}/restore', [RoomController::class, 'restore'])->middleware('permission:rooms.delete');
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

// Conflict Detection Management routes
Route::middleware(['auth:sanctum'])->prefix('conflict-detections')->group(function () {
    Route::get('/', [ConflictDetectionController::class, 'index'])->middleware('permission:conflicts.view');
    Route::get('/statistics', [ConflictDetectionController::class, 'statistics'])->middleware('permission:conflicts.view');
    Route::get('/high-priority', [ConflictDetectionController::class, 'highPriority'])->middleware('permission:conflicts.view');
    Route::get('/analytics', [ConflictDetectionController::class, 'analytics'])->middleware('permission:conflicts.view');
    Route::get('/conflict-types', [ConflictDetectionController::class, 'getConflictTypes'])->middleware('permission:conflicts.view');
    Route::get('/severity-levels', [ConflictDetectionController::class, 'getSeverityLevels'])->middleware('permission:conflicts.view');
    Route::get('/resolution-strategies', [ConflictDetectionController::class, 'getResolutionStrategies'])->middleware('permission:conflicts.view');

    // Detection endpoints
    Route::post('/detect-for-schedule/{schedule}', [ConflictDetectionController::class, 'detectForSchedule'])->middleware('permission:conflicts.view');
    Route::post('/detect-for-multiple', [ConflictDetectionController::class, 'detectForMultiple'])->middleware('permission:conflicts.view');
    Route::post('/detect-all', [ConflictDetectionController::class, 'detectAll'])->middleware('permission:conflicts.view');
    Route::post('/bulk-detect', [ConflictDetectionController::class, 'bulkDetect'])->middleware('permission:conflicts.view');

    // Resolution endpoints
    Route::post('/bulk-resolve', [ConflictDetectionController::class, 'bulkResolve'])->middleware('permission:conflicts.resolve');

    Route::get('/{conflict}', [ConflictDetectionController::class, 'show'])->middleware('permission:conflicts.view');
    Route::post('/{conflict}/resolve', [ConflictDetectionController::class, 'resolve'])->middleware('permission:conflicts.resolve');
    Route::post('/{conflict}/ignore', [ConflictDetectionController::class, 'ignore'])->middleware('permission:conflicts.resolve');
    Route::post('/{conflict}/escalate', [ConflictDetectionController::class, 'escalate'])->middleware('permission:conflicts.escalate');
});

// Dashboard route
Route::get('/dashboard', function () {
    return response()->json([
        'message' => 'Welcome to Academic Scheduling System Dashboard',
        'version' => '1.0.0',
        'status' => 'success'
    ]);
})->middleware('auth:sanctum');


// Public template download route
Route::get('/lecturers/import/template-download', [LecturerImportController::class, 'downloadTemplate']);

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
            'classes' => '/api/classes',
            'lecturers' => '/api/lecturers',
            'rooms' => '/api/rooms',
            'dashboard' => '/api/dashboard'
        ]
    ]);
});