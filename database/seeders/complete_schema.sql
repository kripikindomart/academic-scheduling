-- ========================================
-- ACADEMIC SCHEDULING SYSTEM DATABASE SCHEMA
-- Complete Database Schema for University Academic Scheduling System
-- ========================================

-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS jadwal_app
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE jadwal_app;

-- ========================================
-- 1. USER MANAGEMENT TABLES
-- ========================================

-- Users table (extends Laravel default users table)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    two_factor_secret VARCHAR(255) NULL,
    two_factor_recovery_codes VARCHAR(255) NULL,
    two_factor_confirmed_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    current_team_id BIGINT UNSIGNED NULL,
    profile_photo_path VARCHAR(2048) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_users_email (email),
    INDEX idx_users_verified (email_verified_at)
);

-- User roles table
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    permissions JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY roles_name_guard_unique (name, guard_name)
);

-- Model_has_roles table (many-to-many relationship between users and roles)
CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (role_id, model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,

    INDEX model_has_roles_model_id_model_type_index (model_id, model_type)
);

-- User profiles table
CREATE TABLE user_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    date_of_birth DATE NULL,
    gender ENUM('male', 'female', 'other') NULL,
    photo VARCHAR(255) NULL,
    bio TEXT NULL,
    social_links JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY user_profiles_user_id_unique (user_id)
);

-- ========================================
-- 2. ACADEMIC STRUCTURE TABLES
-- ========================================

-- Faculties table
CREATE TABLE faculties (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT NULL,
    dean_id BIGINT UNSIGNED NULL,
    vice_dean_academic_id BIGINT UNSIGNED NULL,
    vice_dean_general_id BIGINT UNSIGNED NULL,
    establishment_year YEAR NULL,
    accreditation VARCHAR(10) NULL,
    address TEXT NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_faculties_code (code),
    INDEX idx_faculties_active (is_active),
    FOREIGN KEY (dean_id) REFERENCES user_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (vice_dean_academic_id) REFERENCES user_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (vice_dean_general_id) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Study programs table
CREATE TABLE program_studies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    faculty_id BIGINT UNSIGNED NOT NULL,
    head_of_program_id BIGINT UNSIGNED NULL,
    secretary_id BIGINT UNSIGNED NULL,
    degree_level ENUM('diploma', 'bachelor', 'master', 'doctoral', 'professional') NOT NULL,
    accreditation_status ENUM('A', 'B', 'C', 'Unggul', 'Baik Sekali', 'Baik') NULL,
    accreditation_date DATE NULL,
    establishment_year YEAR NULL,
    total_students INT DEFAULT 0,
    total_lecturers INT DEFAULT 0,
    duration_years TINYINT NOT NULL,
    description TEXT NULL,
    vision TEXT NULL,
    mission JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_program_studies_code (code),
    INDEX idx_program_studies_faculty (faculty_id),
    INDEX idx_program_studies_active (is_active),
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE,
    FOREIGN KEY (head_of_program_id) REFERENCES user_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (secretary_id) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Departments table
CREATE TABLE departments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NULL,
    program_study_id BIGINT UNSIGNED NULL,
    faculty_id BIGINT UNSIGNED NULL,
    head_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_departments_program_study (program_study_id),
    INDEX idx_departments_faculty (faculty_id),
    INDEX idx_departments_active (is_active),
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE,
    FOREIGN KEY (head_id) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Semesters table
CREATE TABLE semesters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    semester_type ENUM('ganjil', 'genap', 'pendek') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    registration_start_date DATE NULL,
    registration_end_date DATE NULL,
    is_active BOOLEAN DEFAULT FALSE,
    is_current BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY semesters_academic_year_type (academic_year, semester_type),
    INDEX idx_semesters_active (is_active),
    INDEX idx_semesters_current (is_current),
    INDEX idx_semesters_dates (start_date, end_date)
);

-- Academic calendar events table
CREATE TABLE academic_calendars (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    semester_id BIGINT UNSIGNED NOT NULL,
    event_type ENUM('academic', 'holiday', 'exam', 'registration', 'graduation', 'other') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_holiday BOOLEAN DEFAULT FALSE,
    affects_schedule BOOLEAN DEFAULT FALSE,
    program_study_ids JSON NULL, -- If null, affects all programs
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_academic_calendars_semester (semester_id),
    INDEX idx_academic_calendars_type (event_type),
    INDEX idx_academic_calendars_dates (start_date, end_date),
    INDEX idx_academic_calendars_holiday (is_holiday),
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
);

-- ========================================
-- 3. CURRICULUM MANAGEMENT TABLES
-- ========================================

-- Curriculums table
CREATE TABLE curriculums (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    program_study_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    year VARCHAR(10) NOT NULL, -- e.g., "2024"
    version VARCHAR(50) NOT NULL, -- e.g., "Rev. 1", "2024.1"
    total_credits DECIMAL(5,2) NOT NULL,
    total_theory_hours INT NOT NULL,
    total_practice_hours INT NOT NULL,
    description TEXT NULL,
    learning_outcomes JSON NULL,
    is_active BOOLEAN DEFAULT FALSE,
    effective_from_date DATE NULL,
    effective_to_date DATE NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_curriculums_program_study (program_study_id),
    INDEX idx_curriculums_year (year),
    INDEX idx_curriculums_active (is_active),
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE CASCADE
);

-- Subjects/Courses table
CREATE TABLE subjects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    alias VARCHAR(100) NULL, -- Short name/acronym
    english_name VARCHAR(255) NULL,
    curriculum_id BIGINT UNSIGNED NULL,
    program_study_id BIGINT UNSIGNED NULL,
    subject_type ENUM('wajib', 'pilihan', 'mk_wajib_universitas', 'mk_wajib_fakultas', 'mk_peminatan') NOT NULL,
    level ENUM('dasar', 'menengah', 'lanjut', 'peminatan') NOT NULL,
    credits DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    theory_hours DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    practice_hours DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    lab_hours DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    field_work_hours DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    semester_offered TINYINT NULL, -- Which semester this subject is typically offered
    prerequisite_subjects JSON NULL, -- Array of subject IDs
    prerequisite_description TEXT NULL,
    syllabus_url VARCHAR(255) NULL,
    description TEXT NULL,
    learning_outcomes JSON NULL,
    min_capacity INT DEFAULT 1,
    max_capacity INT DEFAULT 100,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_subjects_code (code),
    INDEX idx_subjects_program_study (program_study_id),
    INDEX idx_subjects_curriculum (curriculum_id),
    INDEX idx_subjects_type (subject_type),
    INDEX idx_subjects_active (is_active),
    FOREIGN KEY (curriculum_id) REFERENCES curriculums(id) ON DELETE SET NULL,
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE SET NULL
);

-- Subject prerequisites table (detailed prerequisite relationships)
CREATE TABLE prerequisites (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    prerequisite_subject_id BIGINT UNSIGNED NOT NULL,
    prerequisite_type ENUM('wajib', 'disarankan', 'paralel') NOT NULL DEFAULT 'wajib',
    minimum_grade DECIMAL(3,2) NULL, -- Minimum grade required
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY prerequisites_unique (subject_id, prerequisite_subject_id),
    INDEX idx_prerequisites_subject (subject_id),
    INDEX idx_prerequisites_prerequisite (prerequisite_subject_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (prerequisite_subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Learning outcomes table
CREATE TABLE learning_outcomes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    outcome_code VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    outcome_type ENUM('pengetahuan', 'keterampilan', 'sikap', 'kompetensi_umum', 'kompetensi_khusus') NOT NULL,
    assessment_method ENUM('test', 'assignment', 'presentation', 'project', 'practicum', 'observation', 'portfolio') NULL,
    level ENUM('dasar', 'menengah', 'lanjut', 'ahli') NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY learning_outcomes_subject_code (subject_id, outcome_code),
    INDEX idx_learning_outcomes_subject (subject_id),
    INDEX idx_learning_outcomes_type (outcome_type),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- ========================================
-- 4. LECTURER MANAGEMENT TABLES
-- ========================================

-- Lecturers table
CREATE TABLE lecturers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    nip VARCHAR(20) UNIQUE NOT NULL,
    nidn VARCHAR(10) UNIQUE NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NULL,
    phone VARCHAR(20) NULL,
    program_study_id BIGINT UNSIGNED NULL,
    homebase_study_id BIGINT UNSIGNED NULL,
    department_id BIGINT UNSIGNED NULL,
    education_level ENUM('s1', 's2', 's3') NULL,
    specialization VARCHAR(255) NULL,
    expertise JSON NULL, -- Array of expertise areas
    employment_type ENUM('dosen_tetap', 'dosen_tidak_tetap', 'dosen_tamu', 'honorer') NOT NULL,
    functional_rank ENUM('asisten_ahli', 'lektor', 'lektor_kepala', 'guru_besar') NULL,
    structural_position VARCHAR(255) NULL,
    employment_status ENUM('aktif', 'cuti', 'pensiun', 'keluar') DEFAULT 'aktif',
    max_teaching_load DECIMAL(5,2) DEFAULT 16.00, -- Maximum SKS per semester
    current_load DECIMAL(5,2) DEFAULT 0.00,
    research_interests JSON NULL,
    certifications JSON NULL,
    publications JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_lecturers_nip (nip),
    INDEX idx_lecturers_nidn (nidn),
    INDEX idx_lecturers_program_study (program_study_id),
    INDEX idx_lecturers_homebase (homebase_study_id),
    INDEX idx_lecturers_department (department_id),
    INDEX idx_lecturers_employment_type (employment_type),
    INDEX idx_lecturers_active (is_active),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE SET NULL,
    FOREIGN KEY (homebase_study_id) REFERENCES program_studies(id) ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Subject lecturers table (which lecturers can teach which subjects)
CREATE TABLE subject_lecturers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    lecturer_id BIGINT UNSIGNED NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE, -- Primary lecturer vs assistant/associate
    teaching_load_percentage DECIMAL(5,2) DEFAULT 100.00, -- Percentage of teaching load
    competency_level ENUM('pemula', 'kompeten', 'ahli', 'mahir') NULL,
    years_of_experience TINYINT NULL,
    certification_date DATE NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY subject_lecturers_unique (subject_id, lecturer_id),
    INDEX idx_subject_lecturers_subject (subject_id),
    INDEX idx_subject_lecturers_lecturer (lecturer_id),
    INDEX idx_subject_lecturers_primary (is_primary),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE
);

-- Lecturer availabilities table
CREATE TABLE lecturer_availabilities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lecturer_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    day_of_week TINYINT NOT NULL, -- 1 = Monday, 7 = Sunday
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    availability_type ENUM('preferred', 'available', 'unavailable', 'reserved') DEFAULT 'available',
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_lecturer_availabilities_lecturer_semester (lecturer_id, semester_id),
    INDEX idx_lecturer_availabilities_day_time (day_of_week, start_time, end_time),
    INDEX idx_lecturer_availabilities_available (is_available),
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
);

-- Lecturer preferences table
CREATE TABLE lecturer_preferences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lecturer_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    preference_type ENUM('days', 'times', 'rooms', 'subjects') NOT NULL,
    preference_data JSON NOT NULL, -- Flexible JSON data for different preference types
    priority_level ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    is_flexible BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_lecturer_preferences_lecturer_semester (lecturer_id, semester_id),
    INDEX idx_lecturer_preferences_type (preference_type),
    INDEX idx_lecturer_preferences_priority (priority_level),
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
);

-- Teaching loads table
CREATE TABLE teaching_loads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lecturer_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    total_credits DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    total_theory_hours DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    total_practice_hours DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    total_classes INT NOT NULL DEFAULT 0,
    total_different_subjects INT NOT NULL DEFAULT 0,
    consultation_hours DECIMAL(5,2) NOT NULL DEFAULT 2.00,
    research_hours DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    community_service_hours DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    additional_duties JSON NULL,
    load_status ENUM('normal', 'underload', 'overload', 'exempt') DEFAULT 'normal',
    notes TEXT NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY teaching_loads_unique (lecturer_id, semester_id),
    INDEX idx_teaching_loads_lecturer (lecturer_id),
    INDEX idx_teaching_loads_semester (semester_id),
    INDEX idx_teaching_loads_status (load_status),
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- ========================================
-- 5. STUDENT MANAGEMENT TABLES
-- ========================================

-- Students table
CREATE TABLE students (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    nim VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NULL,
    phone VARCHAR(20) NULL,
    program_study_id BIGINT UNSIGNED NOT NULL,
    current_semester TINYINT NOT NULL DEFAULT 1,
    enrollment_status ENUM('aktif', 'cuti', 'nonaktif', 'lulus', 'dropout', 'alih_jenjang', 'pindah_program') DEFAULT 'aktif',
    admission_year YEAR NOT NULL,
    admission_type ENUM('reguler', 'transfer', 'aliyah_jenjang', 'mandiri', 'beasiswa') NOT NULL,
    class_type ENUM('reguler', 'non_reguler', 'eksekutif', 'karyawan') NULL,
    scholarship_status BOOLEAN DEFAULT FALSE,
    scholarship_type VARCHAR(255) NULL,
    gpa DECIMAL(3,2) DEFAULT 0.00,
    total_credits DECIMAL(6,2) DEFAULT 0.00,
    address TEXT NULL,
    parents_name VARCHAR(255) NULL,
    parents_phone VARCHAR(20) NULL,
    emergency_contact JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_students_nim (nim),
    INDEX idx_students_program_study (program_study_id),
    INDEX idx_students_semester (current_semester),
    INDEX idx_students_status (enrollment_status),
    INDEX idx_students_admission_year (admission_year),
    INDEX idx_students_active (is_active),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE CASCADE
);

-- Enrollments table
CREATE TABLE enrollments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    subject_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    class_section_id BIGINT UNSIGNED NULL,
    enrollment_date DATE NOT NULL,
    status ENUM('registered', 'approved', 'rejected', 'withdrawn', 'completed', 'failed') DEFAULT 'registered',
    final_grade DECIMAL(5,2) NULL,
    grade_letter VARCHAR(5) NULL,
    gpa_points DECIMAL(3,2) NULL,
    is_pass BOOLEAN NULL,
    credits_earned DECIMAL(5,2) NULL,
    notes TEXT NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY enrollments_unique (student_id, subject_id, semester_id),
    INDEX idx_enrollments_student (student_id),
    INDEX idx_enrollments_subject (subject_id),
    INDEX idx_enrollments_semester (semester_id),
    INDEX idx_enrollments_class_section (class_section_id),
    INDEX idx_enrollments_status (status),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (class_section_id) REFERENCES class_sections(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Class sections table
CREATE TABLE class_sections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    section_name VARCHAR(50) NOT NULL, -- e.g., "A", "B", "Reguler", "Karyawan"
    lecturer_id BIGINT UNSIGNED NULL,
    schedule_id BIGINT UNSIGNED NULL, -- Main schedule reference
    max_capacity INT NOT NULL DEFAULT 40,
    current_enrollment INT NOT NULL DEFAULT 0,
    min_capacity INT NOT NULL DEFAULT 5,
    class_type ENUM('regular', 'lab', 'tutorial', 'seminar', 'practicum') DEFAULT 'regular',
    meeting_days JSON NULL, -- Days when this class meets
    room_preference VARCHAR(255) NULL,
    status ENUM('planned', 'open', 'closed', 'cancelled') DEFAULT 'planned',
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_class_sections_subject (subject_id),
    INDEX idx_class_sections_semester (semester_id),
    INDEX idx_class_sections_lecturer (lecturer_id),
    INDEX idx_class_sections_schedule (schedule_id),
    INDEX idx_class_sections_status (status),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE SET NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL
);

-- ========================================
-- 6. ROOM AND FACILITY MANAGEMENT TABLES
-- ========================================

-- Buildings table
CREATE TABLE buildings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    campus_location VARCHAR(255) NULL, -- e.g., "Kampus A", "Kampus B"
    address TEXT NULL,
    total_floors TINYINT DEFAULT 1,
    total_rooms INT DEFAULT 0,
    coordinates JSON NULL, -- Latitude and longitude
    accessibility_features JSON NULL, -- wheelchair access, elevator, etc.
    building_type ENUM('academic', 'laboratory', 'library', 'administrative', 'support') DEFAULT 'academic',
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_buildings_code (code),
    INDEX idx_buildings_campus (campus_location),
    INDEX idx_buildings_type (building_type),
    INDEX idx_buildings_active (is_active)
);

-- Rooms table
CREATE TABLE rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    building_id BIGINT UNSIGNED NOT NULL,
    room_type ENUM('theater', 'classroom', 'lab', 'studio', 'seminar', 'computer_lab', 'language_lab', 'workshop', 'auditorium', 'meeting_room') NOT NULL,
    capacity INT NOT NULL DEFAULT 30,
    exam_capacity INT NULL, -- May be different for exams
    floor_number TINYINT DEFAULT 1,
    area_square_meters DECIMAL(8,2) NULL,
    facilities JSON NULL, -- Array of available facilities
    equipment JSON NULL, -- Available equipment
    room_conditions JSON NULL, -- AC status, lighting, etc.
    accessibility_features JSON NULL,
    scheduling_priority ENUM('high', 'medium', 'low') DEFAULT 'medium',
    restrictions JSON NULL, -- Usage restrictions
    maintenance_schedule JSON NULL,
    last_maintenance DATE NULL,
    next_maintenance DATE NULL,
    status ENUM('available', 'maintenance', 'renovation', 'unavailable') DEFAULT 'available',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_rooms_code (code),
    INDEX idx_rooms_building (building_id),
    INDEX idx_rooms_type (room_type),
    INDEX idx_rooms_capacity (capacity),
    INDEX idx_rooms_status (status),
    INDEX idx_rooms_active (is_active),
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE
);

-- Room facilities table
CREATE TABLE room_facilities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    facility_type ENUM('projector', 'whiteboard', 'computer', 'internet', 'ac', 'sound_system', 'microphone', 'camera', 'lab_equipment', 'special_equipment') NOT NULL,
    facility_name VARCHAR(255) NULL,
    quantity INT NOT NULL DEFAULT 1,
    condition_status ENUM('excellent', 'good', 'fair', 'poor', 'broken') DEFAULT 'good',
    last_maintenance DATE NULL,
    next_maintenance DATE NULL,
    specifications JSON NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_room_facilities_room (room_id),
    INDEX idx_room_facilities_type (facility_type),
    INDEX idx_room_facilities_condition (condition_status),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Room time slots table ( predefined available/blocked time slots )
CREATE TABLE room_time_slots (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    day_of_week TINYINT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    booking_type ENUM('available', 'blocked', 'reserved', 'maintenance') DEFAULT 'available',
    reserved_for VARCHAR(255) NULL, -- What this slot is reserved for
    priority_level TINYINT DEFAULT 1, -- 1 = highest priority
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_room_time_slots_room_semester (room_id, semester_id),
    INDEX idx_room_time_slots_day_time (day_of_week, start_time, end_time),
    INDEX idx_room_time_slots_available (is_available),
    INDEX idx_room_time_slots_booking_type (booking_type),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE
);

-- ========================================
-- 7. CORE SCHEDULING TABLES
-- ========================================

-- Schedules table (main schedule information)
CREATE TABLE schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    lecturer_id BIGINT UNSIGNED NOT NULL,
    room_id BIGINT UNSIGNED NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    class_section_id BIGINT UNSIGNED NULL,
    schedule_type ENUM('regular', 'lab', 'tutorial', 'seminar', 'makeup', 'additional', 'special') DEFAULT 'regular',
    day_of_week TINYINT NOT NULL, -- 1 = Monday, 7 = Sunday
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_minutes INT NOT NULL,
    is_recurring BOOLEAN DEFAULT TRUE,
    recurring_pattern ENUM('weekly', 'biweekly', 'monthly', 'custom') DEFAULT 'weekly',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    class_section VARCHAR(50) NULL, -- e.g., "A", "B", "Reguler"
    parallel_session INT NULL, -- For parallel classes
    co_lecturers JSON NULL, -- Additional lecturers
    teaching_assistants JSON NULL,
    special_requirements JSON NULL,
    created_by BIGINT UNSIGNED NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    status ENUM('draft', 'pending_approval', 'approved', 'rejected', 'published', 'cancelled', 'completed') DEFAULT 'draft',
    conflict_score INT DEFAULT 0, -- Number of conflicts detected
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_schedules_subject (subject_id),
    INDEX idx_schedules_lecturer (lecturer_id),
    INDEX idx_schedules_room (room_id),
    INDEX idx_schedules_semester (semester_id),
    INDEX idx_schedules_class_section (class_section_id),
    INDEX idx_schedules_day_time (day_of_week, start_time, end_time),
    INDEX idx_schedules_dates (start_date, end_date),
    INDEX idx_schedules_status (status),
    INDEX idx_schedules_active (is_active),
    INDEX idx_schedules_conflict_score (conflict_score),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (class_section_id) REFERENCES class_sections(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Schedule instances table (actual occurrences of scheduled classes)
CREATE TABLE schedule_instances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    day_of_week TINYINT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    actual_start_time TIME NULL,
    actual_end_time TIME NULL,
    duration_minutes INT NOT NULL,
    room_id BIGINT UNSIGNED NULL, -- Can be different from main schedule room
    substitute_lecturer_id BIGINT UNSIGNED NULL,
    teaching_assistants JSON NULL,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled', 'postponed', 'makeup_required') DEFAULT 'scheduled',
    cancellation_reason TEXT NULL,
    rescheduled_to_instance_id BIGINT UNSIGNED NULL,
    makeup_for_instance_id BIGINT UNSIGNED NULL,
    attendance_taken BOOLEAN DEFAULT FALSE,
    journal_completed BOOLEAN DEFAULT FALSE,
    materials_uploaded BOOLEAN DEFAULT FALSE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_schedule_instances_schedule (schedule_id),
    INDEX idx_schedule_instances_date (date),
    INDEX idx_schedule_instances_room (room_id),
    INDEX idx_schedule_instances_substitute (substitute_lecturer_id),
    INDEX idx_schedule_instances_status (status),
    UNIQUE KEY schedule_instances_unique (schedule_id, date),
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (substitute_lecturer_id) REFERENCES lecturers(id) ON DELETE SET NULL,
    FOREIGN KEY (rescheduled_to_instance_id) REFERENCES schedule_instances(id) ON DELETE SET NULL,
    FOREIGN KEY (makeup_for_instance_id) REFERENCES schedule_instances(id) ON DELETE SET NULL
);

-- Room bookings table (tracks all room bookings)
CREATE TABLE room_bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    schedule_id BIGINT UNSIGNED NULL, -- If null, it's a general booking
    schedule_instance_id BIGINT UNSIGNED NULL,
    booking_reference VARCHAR(100) UNIQUE NULL, -- Reference number
    booking_type ENUM('class', 'exam', 'meeting', 'event', 'maintenance', 'block') NOT NULL,
    booked_by BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    expected_attendees INT NULL,
    setup_requirements JSON NULL,
    cleanup_time_minutes INT DEFAULT 0,
    status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'completed', 'cancelled') DEFAULT 'pending',
    priority_level TINYINT DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_room_bookings_room (room_id),
    INDEX idx_room_bookings_schedule (schedule_id),
    INDEX idx_room_bookings_instance (schedule_instance_id),
    INDEX idx_room_bookings_date_time (date, start_time, end_time),
    INDEX idx_room_bookings_type (booking_type),
    INDEX idx_room_bookings_status (status),
    INDEX idx_room_bookings_booked_by (booked_by),
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL,
    FOREIGN KEY (schedule_instance_id) REFERENCES schedule_instances(id) ON DELETE SET NULL,
    FOREIGN KEY (booked_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- ========================================
-- 8. DRAFT SCHEDULE MANAGEMENT TABLES
-- ========================================

-- Draft schedules table
CREATE TABLE draft_schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(36) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    status ENUM('draft', 'under_review', 'conflict_resolution', 'pending_approval', 'submitted', 'rejected', 'approved', 'archived') DEFAULT 'draft',
    total_schedule_items INT DEFAULT 0,
    total_conflicts INT DEFAULT 0,
    resolved_conflicts INT DEFAULT 0,
    conflict_score INT DEFAULT 0,
    is_submitted_for_approval BOOLEAN DEFAULT FALSE,
    submitted_at TIMESTAMP NULL,
    approval_workflow_id BIGINT UNSIGNED NULL,
    current_approval_level INT DEFAULT 0,
    total_sks DECIMAL(6,2) DEFAULT 0.00,
    total_classes INT DEFAULT 0,
    estimated_students INT DEFAULT 0,
    notes TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_draft_schedules_uuid (uuid),
    INDEX idx_draft_schedules_semester (semester_id),
    INDEX idx_draft_schedules_created_by (created_by),
    INDEX idx_draft_schedules_status (status),
    INDEX idx_draft_schedules_submitted (is_submitted_for_approval),
    INDEX idx_draft_schedules_workflow (approval_workflow_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (approval_workflow_id) REFERENCES approval_workflows(id) ON DELETE SET NULL
);

-- Draft schedule items table
CREATE TABLE draft_schedule_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    draft_schedule_id BIGINT UNSIGNED NOT NULL,
    subject_id BIGINT UNSIGNED NOT NULL,
    lecturer_id BIGINT UNSIGNED NOT NULL,
    room_id BIGINT UNSIGNED NULL,
    class_section_id BIGINT UNSIGNED NULL,
    day_of_week TINYINT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_minutes INT NOT NULL,
    class_section VARCHAR(50) NULL,
    parallel_session INT NULL,
    co_lecturers JSON NULL,
    teaching_assistants JSON NULL,
    special_requirements JSON NULL,
    priority_level TINYINT DEFAULT 1,
    conflict_count INT DEFAULT 0,
    resolved_conflicts INT DEFAULT 0,
    notes TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_draft_schedule_items_draft (draft_schedule_id),
    INDEX idx_draft_schedule_items_subject (subject_id),
    INDEX idx_draft_schedule_items_lecturer (lecturer_id),
    INDEX idx_draft_schedule_items_room (room_id),
    INDEX idx_draft_schedule_items_day_time (day_of_week, start_time, end_time),
    INDEX idx_draft_schedule_items_conflict_count (conflict_count),
    FOREIGN KEY (draft_schedule_id) REFERENCES draft_schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (class_section_id) REFERENCES class_sections(id) ON DELETE SET NULL
);

-- Draft collaborators table
CREATE TABLE draft_collaborators (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    draft_schedule_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    permission_level ENUM('view', 'comment', 'edit', 'admin') NOT NULL DEFAULT 'view',
    invited_by BIGINT UNSIGNED NOT NULL,
    invitation_token VARCHAR(255) NULL,
    invited_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    joined_at TIMESTAMP NULL,
    last_access_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY draft_collaborators_unique (draft_schedule_id, user_id),
    INDEX idx_draft_collaborators_draft (draft_schedule_id),
    INDEX idx_draft_collaborators_user (user_id),
    INDEX idx_draft_collaborators_permission (permission_level),
    INDEX idx_draft_collaborators_active (is_active),
    FOREIGN KEY (draft_schedule_id) REFERENCES draft_schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Draft comments table
CREATE TABLE draft_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    draft_schedule_id BIGINT UNSIGNED NOT NULL,
    draft_schedule_item_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL, -- For threaded comments
    comment_type ENUM('general', 'conflict', 'suggestion', 'question', 'resolution') NOT NULL DEFAULT 'general',
    comment TEXT NOT NULL,
    metadata JSON NULL, -- Additional structured data
    resolved_at TIMESTAMP NULL,
    resolved_by BIGINT UNSIGNED NULL,
    resolution_notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_draft_comments_draft (draft_schedule_id),
    INDEX idx_draft_comments_item (draft_schedule_item_id),
    INDEX idx_draft_comments_user (user_id),
    INDEX idx_draft_comments_parent (parent_id),
    INDEX idx_draft_comments_type (comment_type),
    INDEX idx_draft_comments_resolved (resolved_at),
    FOREIGN KEY (draft_schedule_id) REFERENCES draft_schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (draft_schedule_item_id) REFERENCES draft_schedule_items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES draft_comments(id) ON DELETE NULL,
    FOREIGN KEY (resolved_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Draft versions table (version control for drafts)
CREATE TABLE draft_versions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    draft_schedule_id BIGINT UNSIGNED NOT NULL,
    version_number INT NOT NULL,
    version_name VARCHAR(100) NULL,
    changes_summary TEXT NULL,
    total_changes INT DEFAULT 0,
    changes_by BIGINT UNSIGNED NOT NULL,
    snapshot_data JSON NULL, -- Complete snapshot of draft data
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY draft_versions_unique (draft_schedule_id, version_number),
    INDEX idx_draft_versions_draft (draft_schedule_id),
    INDEX idx_draft_versions_created_by (changes_by),
    FOREIGN KEY (draft_schedule_id) REFERENCES draft_schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (changes_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- ========================================
-- 9. CONFLICT DETECTION TABLES
-- ========================================

-- Schedule conflicts table
CREATE TABLE schedule_conflicts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_draft_id BIGINT UNSIGNED NULL,
    schedule_id BIGINT UNSIGNED NULL,
    conflict_type ENUM('lecturer', 'room', 'student', 'prerequisite', 'capacity', 'accreditation', 'time_slot', 'curriculum') NOT NULL,
    conflict_with_id BIGINT UNSIGNED NULL, -- ID of conflicting entity
    conflict_with_type ENUM('schedule', 'draft_schedule_item', 'room_booking', 'lecturer_availability') NULL,
    severity_level ENUM('critical', 'high', 'medium', 'low', 'info') NOT NULL,
    conflict_score INT NOT NULL DEFAULT 1, -- 1-10 score
    description TEXT NOT NULL,
    affected_entities JSON NULL, -- List of entities affected by this conflict
    resolution_suggestion TEXT NULL,
    auto_resolution_possible BOOLEAN DEFAULT FALSE,
    is_resolved BOOLEAN DEFAULT FALSE,
    resolved_by BIGINT UNSIGNED NULL,
    resolved_at TIMESTAMP NULL,
    resolution_method VARCHAR(255) NULL,
    resolution_notes TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_schedule_conflicts_draft (schedule_draft_id),
    INDEX idx_schedule_conflicts_schedule (schedule_id),
    INDEX idx_schedule_conflicts_type (conflict_type),
    INDEX idx_schedule_conflicts_severity (severity_level),
    INDEX idx_schedule_conflicts_resolved (is_resolved),
    INDEX idx_schedule_conflicts_score (conflict_score),
    FOREIGN KEY (schedule_draft_id) REFERENCES draft_schedule_items(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Conflict rules table (defines how to detect conflicts)
CREATE TABLE conflict_rules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rule_name VARCHAR(255) NOT NULL,
    conflict_type ENUM('lecturer', 'room', 'student', 'prerequisite', 'capacity', 'accreditation', 'time_slot', 'curriculum') NOT NULL,
    entity_type ENUM('schedule', 'draft', 'booking') NOT NULL,
    condition_logic JSON NOT NULL, -- Complex condition logic in JSON format
    severity_level ENUM('critical', 'high', 'medium', 'low', 'info') NOT NULL,
    auto_resolution JSON NULL, -- Auto-resolution logic if available
    is_active BOOLEAN DEFAULT TRUE,
    priority INT DEFAULT 1,
    applies_to_program_studies JSON NULL, -- Null = applies to all
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_conflict_rules_type (conflict_type),
    INDEX idx_conflict_rules_entity (entity_type),
    INDEX idx_conflict_rules_severity (severity_level),
    INDEX idx_conflict_rules_active (is_active)
);

-- Conflict analytics table (tracks conflict statistics)
CREATE TABLE conflict_analytics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    analysis_date DATE NOT NULL,
    total_conflicts INT NOT NULL DEFAULT 0,
    conflicts_by_type JSON NOT NULL, -- Breakdown by conflict type
    conflicts_by_severity JSON NOT NULL, -- Breakdown by severity
    resolution_rate DECIMAL(5,2) DEFAULT 0.00, -- Percentage of resolved conflicts
    average_resolution_time DECIMAL(8,2) DEFAULT 0.00, -- In hours
    auto_resolution_rate DECIMAL(5,2) DEFAULT 0.00,
    most_common_conflicts JSON NULL,
    resolution_trends JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY conflict_analytics_date (analysis_date)
);

-- Schedule audit logs table (tracks all changes)
CREATE TABLE schedule_audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_draft_id BIGINT UNSIGNED NULL,
    schedule_id BIGINT UNSIGNED NULL,
    action ENUM('created', 'updated', 'deleted', 'approved', 'rejected', 'published', 'cancelled') NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    changed_fields JSON NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    session_id VARCHAR(255) NULL,
    request_id VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_schedule_audit_logs_draft (schedule_draft_id),
    INDEX idx_schedule_audit_logs_schedule (schedule_id),
    INDEX idx_schedule_audit_logs_action (action),
    INDEX idx_schedule_audit_logs_user (user_id),
    INDEX idx_schedule_audit_logs_created (created_at),
    FOREIGN KEY (schedule_draft_id) REFERENCES draft_schedule_items(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- ========================================
-- 10. SK PENGAJUAN & APPROVAL WORKFLOW TABLES
-- ========================================

-- SK Pengajuan table (Surat Keputusan Pengajuan)
CREATE TABLE sk_pengajuan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    draft_schedule_id BIGINT UNSIGNED NOT NULL,
    pengajuan_number VARCHAR(100) UNIQUE NOT NULL,
    lecturer_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    program_study_id BIGINT UNSIGNED NOT NULL,
    faculty_id BIGINT UNSIGNED NOT NULL,
    total_sks DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    total_theory_hours DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    total_practice_hours DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    total_classes INT NOT NULL DEFAULT 0,
    total_different_subjects INT NOT NULL DEFAULT 0,
    estimated_students INT NOT NULL DEFAULT 0,
    sk_type ENUM('mengajar', 'pembimbing', 'penguji', 'penelitian', 'pengabdian') DEFAULT 'mengajar',
    priority_level ENUM('rutin', 'prioritas', 'darurat') DEFAULT 'rutin',
    status ENUM('draft', 'submitted', 'pending_level_1', 'pending_level_2', 'pending_level_3', 'pending_level_4', 'approved', 'rejected', 'cancelled', 'completed') DEFAULT 'draft',
    current_approval_level INT DEFAULT 0,
    max_approval_level INT DEFAULT 4,
    submitted_by BIGINT UNSIGNED NOT NULL,
    submitted_at TIMESTAMP NULL,
    cover_letter TEXT NULL,
    attachments JSON NULL,
    rejection_reason TEXT NULL,
    notes TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_sk_pengajuan_number (pengajuan_number),
    INDEX idx_sk_pengajuan_draft (draft_schedule_id),
    INDEX idx_sk_pengajuan_lecturer (lecturer_id),
    INDEX idx_sk_pengajuan_semester (semester_id),
    INDEX idx_sk_pengajuan_program_study (program_study_id),
    INDEX idx_sk_pengajuan_faculty (faculty_id),
    INDEX idx_sk_pengajuan_status (status),
    INDEX idx_sk_pengajuan_submitted_by (submitted_by),
    INDEX idx_sk_pengajuan_submitted_at (submitted_at),
    FOREIGN KEY (draft_schedule_id) REFERENCES draft_schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE,
    FOREIGN KEY (submitted_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- SK approvals table (tracks each approval level)
CREATE TABLE sk_approvals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sk_pengajuan_id BIGINT UNSIGNED NOT NULL,
    approval_level INT NOT NULL,
    approver_id BIGINT UNSIGNED NULL,
    approver_role VARCHAR(255) NOT NULL,
    approver_name VARCHAR(255) NULL,
    status ENUM('pending', 'approved', 'rejected', 'delegated', 'skipped') DEFAULT 'pending',
    decision ENUM('approve', 'reject', 'request_revision', 'delegate') NULL,
    comments TEXT NULL,
    revision_requests JSON NULL,
    delegation_note TEXT NULL,
    approved_at TIMESTAMP NULL,
    approval_duration_hours DECIMAL(8,2) NULL,
    digital_signature JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY sk_approvals_unique (sk_pengajuan_id, approval_level),
    INDEX idx_sk_approvals_pengajuan (sk_pengajuan_id),
    INDEX idx_sk_approvals_approver (approver_id),
    INDEX idx_sk_approvals_level (approval_level),
    INDEX idx_sk_approvals_status (status),
    INDEX idx_sk_approvals_role (approver_role),
    INDEX idx_sk_approvals_approved_at (approved_at),
    FOREIGN KEY (sk_pengajuan_id) REFERENCES sk_pengajuan(id) ON DELETE CASCADE,
    FOREIGN KEY (approver_id) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- SK documents table (generated SK documents)
CREATE TABLE sk_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sk_pengajuan_id BIGINT UNSIGNED NOT NULL,
    document_number VARCHAR(100) UNIQUE NOT NULL,
    document_type ENUM('sk_mengajar', 'sk_pembimbing', 'sk_penguji', 'surat_tugas', 'addendum') NOT NULL,
    document_template_id BIGINT UNSIGNED NULL,
    file_path VARCHAR(255) NULL,
    file_name VARCHAR(255) NULL,
    file_size BIGINT NULL,
    mime_type VARCHAR(100) NULL,
    document_content JSON NULL, -- Template variables and their values
    digital_signature JSON NULL,
    watermark VARCHAR(255) NULL,
    security_features JSON NULL,
    generated_by BIGINT UNSIGNED NOT NULL,
    generated_at TIMESTAMP NULL,
    signed_by BIGINT UNSIGNED NULL,
    signed_at TIMESTAMP NULL,
    status ENUM('generating', 'generated', 'signed', 'distributed', 'archived') DEFAULT 'generating',
    distribution_list JSON NULL,
    download_count INT DEFAULT 0,
    last_downloaded_at TIMESTAMP NULL,
    archived_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_sk_documents_pengajuan (sk_pengajuan_id),
    INDEX idx_sk_documents_number (document_number),
    INDEX idx_sk_documents_type (document_type),
    INDEX idx_sk_documents_template (document_template_id),
    INDEX idx_sk_documents_status (status),
    INDEX idx_sk_documents_generated_by (generated_by),
    INDEX idx_sk_documents_signed_by (signed_by),
    FOREIGN KEY (sk_pengajuan_id) REFERENCES sk_pengajuan(id) ON DELETE CASCADE,
    FOREIGN KEY (document_template_id) REFERENCES sk_templates(id) ON DELETE SET NULL,
    FOREIGN KEY (generated_by) REFERENCES user_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (signed_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- SK templates table (document templates for SK generation)
CREATE TABLE sk_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(255) NOT NULL,
    template_type ENUM('sk_mengajar', 'sk_pembimbing', 'sk_penguji', 'surat_tugas', 'addendum') NOT NULL,
    faculty_id BIGINT UNSIGNED NULL, -- NULL = universal template
    program_study_id BIGINT UNSIGNED NULL, -- NULL = universal for faculty
    template_content LONGTEXT NOT NULL, -- HTML/Markdown template
    template_variables JSON NOT NULL, -- Available template variables
    css_styles LONGTEXT NULL,
    header_template LONGTEXT NULL,
    footer_template LONGTEXT NULL,
    signature_template LONGTEXT NULL,
    watermark_text VARCHAR(255) NULL,
    page_format ENUM('A4', 'A4_Landscape', 'Letter', 'Legal') DEFAULT 'A4',
    orientation ENUM('portrait', 'landscape') DEFAULT 'portrait',
    font_family VARCHAR(100) DEFAULT 'Times New Roman',
    font_size INT DEFAULT 12,
    line_height DECIMAL(3,1) DEFAULT 1.5,
    margin_top DECIMAL(5,2) DEFAULT 2.5,
    margin_bottom DECIMAL(5,2) DEFAULT 2.5,
    margin_left DECIMAL(5,2) DEFAULT 2.5,
    margin_right DECIMAL(5,2) DEFAULT 2.5,
    is_active BOOLEAN DEFAULT TRUE,
    version VARCHAR(20) DEFAULT '1.0',
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_sk_templates_type (template_type),
    INDEX idx_sk_templates_faculty (faculty_id),
    INDEX idx_sk_templates_program_study (program_study_id),
    INDEX idx_sk_templates_active (is_active),
    INDEX idx_sk_templates_created_by (created_by),
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE,
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Approval workflows table (defines approval workflows)
CREATE TABLE approval_workflows (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workflow_name VARCHAR(255) NOT NULL,
    workflow_code VARCHAR(50) UNIQUE NOT NULL,
    program_study_id BIGINT UNSIGNED NULL,
    faculty_id BIGINT UNSIGNED NULL,
    workflow_type ENUM('sk_pengajuan', 'schedule_approval', 'room_booking', 'grade_approval') NOT NULL,
    approval_levels JSON NOT NULL, -- Array of approval level configurations
    auto_advance_on_approval BOOLEAN DEFAULT TRUE,
    allow_parallel_approval BOOLEAN DEFAULT FALSE,
    timeout_days INT NULL,
    auto_reject_on_timeout BOOLEAN DEFAULT FALSE,
    notification_settings JSON NULL,
    delegation_rules JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,
    description TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_approval_workflows_code (workflow_code),
    INDEX idx_approval_workflows_type (workflow_type),
    INDEX idx_approval_workflows_program_study (program_study_id),
    INDEX idx_approval_workflows_faculty (faculty_id),
    INDEX idx_approval_workflows_active (is_active),
    INDEX idx_approval_workflows_default (is_default),
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Workflow configurations table (detailed configuration for each approval level)
CREATE TABLE workflow_configurations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    approval_workflow_id BIGINT UNSIGNED NOT NULL,
    level_number INT NOT NULL,
    level_name VARCHAR(255) NOT NULL,
    role_required VARCHAR(255) NOT NULL, -- Role that can approve at this level
    specific_users JSON NULL, -- Specific users that can approve (overrides role)
    time_limit_days INT DEFAULT 7, -- Days to approve before timeout
    auto_reject_on_timeout BOOLEAN DEFAULT FALSE,
    auto_approve_if_previous_rejected BOOLEAN DEFAULT FALSE,
    minimum_approvers INT DEFAULT 1,
    approval_type ENUM('any', 'all', 'majority') DEFAULT 'any',
    email_notifications BOOLEAN DEFAULT TRUE,
    sms_notifications BOOLEAN DEFAULT FALSE,
    push_notifications BOOLEAN DEFAULT FALSE,
    notification_recipients JSON NULL,
    escalation_rules JSON NULL,
    conditions JSON NULL, -- Conditions for this level
    actions JSON NULL, -- Actions to take after approval/rejection
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY workflow_configurations_unique (approval_workflow_id, level_number),
    INDEX idx_workflow_configurations_workflow (approval_workflow_id),
    INDEX idx_workflow_configurations_level (level_number),
    INDEX idx_workflow_configurations_role (role_required),
    INDEX idx_workflow_configurations_active (is_active),
    FOREIGN KEY (approval_workflow_id) REFERENCES approval_workflows(id) ON DELETE CASCADE
);

-- ========================================
-- 11. ATTENDANCE SYSTEM TABLES
-- ========================================

-- Attendances table
CREATE TABLE attendances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_instance_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    check_in_time TIMESTAMP NULL,
    check_out_time TIMESTAMP NULL,
    attendance_status ENUM('present', 'late', 'absent', 'excused', 'sick', 'leave', 'early_leave') NOT NULL DEFAULT 'absent',
    late_duration_minutes INT DEFAULT 0,
    early_leave_duration_minutes INT DEFAULT 0,
    verification_method ENUM('qr_code', 'face_recognition', 'gps', 'nfc', 'rfid', 'biometric', 'manual', 'auto') NULL,
    location_verified BOOLEAN DEFAULT FALSE,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    location_accuracy FLOAT NULL,
    device_info JSON NULL, -- Device information for check-in
    qr_code_hash VARCHAR(255) NULL, -- Hash of QR code used
    face_recognition_confidence DECIMAL(5,4) NULL,
    biometric_template VARCHAR(255) NULL,
    notes TEXT NULL,
    marked_by BIGINT UNSIGNED NULL, -- Who marked the attendance (manual override)
    override_reason TEXT NULL,
    is_verified BOOLEAN DEFAULT TRUE,
    verification_notes TEXT NULL,
    auto_checked_in BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY attendances_unique (schedule_instance_id, student_id),
    INDEX idx_attendances_instance (schedule_instance_id),
    INDEX idx_attendances_student (student_id),
    INDEX idx_attendances_status (attendance_status),
    INDEX idx_attendances_check_in_time (check_in_time),
    INDEX idx_attendances_verification_method (verification_method),
    INDEX idx_attendances_location_verified (location_verified),
    FOREIGN KEY (schedule_instance_id) REFERENCES schedule_instances(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (marked_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Attendance methods table (configures different attendance methods)
CREATE TABLE attendance_methods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    method_name VARCHAR(255) NOT NULL,
    method_type ENUM('qr_code', 'face_recognition', 'gps', 'nfc', 'rfid', 'biometric', 'manual', 'auto') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    config_json JSON NOT NULL, -- Configuration specific to each method
    security_features JSON NULL,
    supported_devices JSON NULL,
    accuracy_level ENUM('low', 'medium', 'high', 'very_high') DEFAULT 'medium',
    auto_approval_enabled BOOLEAN DEFAULT FALSE,
    timeout_minutes INT NULL,
    max_distance_meters INT NULL, -- For GPS verification
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_attendance_methods_type (method_type),
    INDEX idx_attendance_methods_active (is_active)
);

-- Attendance policies table
CREATE TABLE attendance_policies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    program_study_id BIGINT UNSIGNED NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    policy_name VARCHAR(255) NOT NULL,
    max_tolerance_minutes INT DEFAULT 15, -- Maximum allowed lateness
    late_threshold_minutes INT DEFAULT 30, -- When to mark as late vs absent
    min_attendance_percentage DECIMAL(5,2) NOT NULL DEFAULT 75.00, -- Minimum required attendance
    max_absences INT NULL, -- Maximum number of absences allowed
    grace_period_days INT DEFAULT 0, -- Grace period for marking attendance
    auto_mark_absent BOOLEAN DEFAULT FALSE,
    auto_mark_absent_after_minutes INT DEFAULT 60,
    allow_retroactive_marking BOOLEAN DEFAULT FALSE,
    retroactive_marking_limit_days INT DEFAULT 7,
    require_location_verification BOOLEAN DEFAULT FALSE,
    require_photo_verification BOOLEAN DEFAULT FALSE,
    attendance_methods JSON NULL, -- Allowed attendance methods
    penalty_rules JSON NULL, -- Penalties for violations
    notification_settings JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_attendance_policies_program_study (program_study_id),
    INDEX idx_attendance_policies_semester (semester_id),
    INDEX idx_attendance_policies_active (is_active),
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Leave requests table
CREATE TABLE leave_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    schedule_id BIGINT UNSIGNED NULL,
    schedule_instance_id BIGINT UNSIGNED NULL,
    leave_type ENUM('sick', 'personal', 'family', 'academic', 'religious', 'emergency', 'other') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME NULL,
    end_time TIME NULL,
    total_days DECIMAL(4,1) NOT NULL,
    reason TEXT NOT NULL,
    attachment_urls JSON NULL, -- Supporting documents
    supporting_documents JSON NULL,
    leave_status ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed') DEFAULT 'pending',
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    approval_notes TEXT NULL,
    verified_by BIGINT UNSIGNED NULL, -- Medical certificate verification, etc.
    verified_at TIMESTAMP NULL,
    verification_notes TEXT NULL,
    emergency_contact JSON NULL,
    is_emergency BOOLEAN DEFAULT FALSE,
    is_recurring BOOLEAN DEFAULT FALSE,
    recurring_pattern JSON NULL,
    notification_sent BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_leave_requests_student (student_id),
    INDEX idx_leave_requests_schedule (schedule_id),
    INDEX idx_leave_requests_instance (schedule_instance_id),
    INDEX idx_leave_requests_type (leave_type),
    INDEX idx_leave_requests_status (leave_status),
    INDEX idx_leave_requests_dates (start_date, end_date),
    INDEX idx_leave_requests_approved_by (approved_by),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL,
    FOREIGN KEY (schedule_instance_id) REFERENCES schedule_instances(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES user_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (verified_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Attendance exceptions table (special cases and overrides)
CREATE TABLE attendance_exceptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    schedule_instance_id BIGINT UNSIGNED NULL,
    exception_type ENUM('medical_emergency', 'family_emergency', 'technical_issue', 'system_error', 'special_circumstance', 'other') NOT NULL,
    original_attendance_status VARCHAR(50) NULL,
    corrected_attendance_status VARCHAR(50) NULL,
    reason TEXT NOT NULL,
    supporting_documents JSON NULL,
    approved_by BIGINT UNSIGNED NOT NULL,
    approval_level VARCHAR(255) NULL,
    override_reason TEXT NULL,
    is_permanent BOOLEAN DEFAULT FALSE,
    auto_apply_future BOOLEAN DEFAULT FALSE,
    valid_until DATE NULL,
    conditions JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_attendance_exceptions_student (student_id),
    INDEX idx_attendance_exceptions_instance (schedule_instance_id),
    INDEX idx_attendance_exceptions_type (exception_type),
    INDEX idx_attendance_exceptions_approved_by (approved_by),
    INDEX idx_attendance_exceptions_created (created_at),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_instance_id) REFERENCES schedule_instances(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Attendance analytics table (pre-calculated statistics)
CREATE TABLE attendance_analytics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    subject_id BIGINT UNSIGNED NULL, -- If null, overall for semester
    total_sessions INT NOT NULL DEFAULT 0,
    present_count INT NOT NULL DEFAULT 0,
    late_count INT NOT NULL DEFAULT 0,
    absent_count INT NOT NULL DEFAULT 0,
    excused_count INT NOT NULL DEFAULT 0,
    attendance_percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    average_late_duration DECIMAL(8,2) DEFAULT 0.00,
    consecutive_absences INT DEFAULT 0,
    longest_absence_streak INT DEFAULT 0,
    attendance_trend ENUM('improving', 'declining', 'stable') NULL,
    risk_level ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low',
    last_calculated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY attendance_analytics_unique (student_id, semester_id, subject_id),
    INDEX idx_attendance_analytics_student (student_id),
    INDEX idx_attendance_analytics_semester (semester_id),
    INDEX idx_attendance_analytics_subject (subject_id),
    INDEX idx_attendance_analytics_percentage (attendance_percentage),
    INDEX idx_attendance_analytics_risk_level (risk_level),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL
);

-- Location verification table (for GPS-based attendance)
CREATE TABLE location_verification (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attendance_id BIGINT UNSIGNED NOT NULL,
    room_id BIGINT UNSIGNED NULL,
    campus_location VARCHAR(255) NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    accuracy FLOAT NULL, -- GPS accuracy in meters
    altitude DECIMAL(8,2) NULL, -- Altitude in meters
    speed DECIMAL(6,2) NULL, -- Speed when check-in occurred
    heading DECIMAL(6,2) NULL, -- Direction of movement
    distance_from_room DECIMAL(8,2) NULL, -- Distance from assigned room
    is_within_allowed_distance BOOLEAN DEFAULT FALSE,
    max_allowed_distance_meters INT DEFAULT 100,
    verification_method ENUM('gps', 'wifi', 'bluetooth_beacon', 'nfc', 'qr_scanned') NOT NULL,
    wifi_networks JSON NULL, -- Available WiFi networks
    bluetooth_beacons JSON NULL, -- Nearby Bluetooth beacons
    verification_score DECIMAL(5,2) DEFAULT 0.00,
    is_verified BOOLEAN DEFAULT FALSE,
    verification_notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_location_verification_attendance (attendance_id),
    INDEX idx_location_verification_room (room_id),
    INDEX idx_location_verification_verified (is_verified),
    INDEX idx_location_verification_score (verification_score),
    FOREIGN KEY (attendance_id) REFERENCES attendances(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);

-- QR attendance codes table (temporary QR codes for attendance)
CREATE TABLE qr_attendance_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_instance_id BIGINT UNSIGNED NOT NULL,
    qr_code_data VARCHAR(1000) NOT NULL, -- Encoded QR data
    qr_code_hash VARCHAR(255) UNIQUE NOT NULL, -- Hash for verification
    expires_at TIMESTAMP NOT NULL,
    generated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    max_uses INT DEFAULT 100,
    current_uses INT DEFAULT 0,
    allowed_locations JSON NULL, -- GPS boundaries where QR can be used
    security_features JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    classroom_location JSON NULL,
    additional_verification JSON NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_qr_attendance_codes_instance (schedule_instance_id),
    INDEX idx_qr_attendance_codes_hash (qr_code_hash),
    INDEX idx_qr_attendance_codes_expires (expires_at),
    INDEX idx_qr_attendance_codes_active (is_active),
    FOREIGN KEY (schedule_instance_id) REFERENCES schedule_instances(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- ========================================
-- 12. GRADING AND ASSESSMENT TABLES
-- ========================================

-- Assessment types table
CREATE TABLE assessment_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT NULL,
    category ENUM('formative', 'summative', 'diagnostic', 'authentic') NOT NULL,
    assessment_method ENUM('test', 'quiz', 'assignment', 'project', 'presentation', 'practicum', 'lab', 'field_work', 'portfolio', 'observation', 'peer_assessment', 'self_assessment') NOT NULL,
    default_weight_percentage DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    default_max_score DECIMAL(8,2) NOT NULL DEFAULT 100.00,
    default_duration_minutes INT NULL,
    program_study_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_system_defined BOOLEAN DEFAULT FALSE, -- Cannot be deleted if true
    grading_criteria JSON NULL,
    example_questions JSON NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_assessment_types_code (code),
    INDEX idx_assessment_types_category (category),
    INDEX idx_assessment_types_method (assessment_method),
    INDEX idx_assessment_types_program_study (program_study_id),
    INDEX idx_assessment_types_active (is_active),
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Grading schemes table (defines grading systems)
CREATE TABLE grading_schemes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    program_study_id BIGINT UNSIGNED NULL,
    scale_type ENUM('percentage', 'gpa_4', 'gpa_5', 'letter_a_f', 'pass_fail', 'competency') NOT NULL,
    passing_grade DECIMAL(5,2) NOT NULL,
    max_grade DECIMAL(5,2) NOT NULL DEFAULT 100.00,
    min_grade DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    gpa_conversion JSON NULL, -- Conversion table to GPA
    grade_distribution JSON NULL, -- Expected grade distribution
    rounding_method ENUM('standard', 'up', 'down', 'bankers') DEFAULT 'standard',
    decimal_places INT DEFAULT 2,
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_grading_schemes_program_study (program_study_id),
    INDEX idx_grading_schemes_scale_type (scale_type),
    INDEX idx_grading_schemes_active (is_active),
    INDEX idx_grading_schemes_default (is_default),
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Grade scales table (specific grade definitions within schemes)
CREATE TABLE grade_scales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    grading_scheme_id BIGINT UNSIGNED NOT NULL,
    grade_label VARCHAR(10) NOT NULL, -- e.g., "A", "B+", "Pass"
    grade_description VARCHAR(255) NULL,
    min_score DECIMAL(8,2) NOT NULL,
    max_score DECIMAL(8,2) NOT NULL,
    gpa_value DECIMAL(3,2) NULL,
    quality_points DECIMAL(4,2) NULL,
    is_passing BOOLEAN DEFAULT TRUE,
    color_code VARCHAR(7) NULL, -- Hex color code for UI
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY grade_scales_unique (grading_scheme_id, grade_label),
    INDEX idx_grade_scales_scheme (grading_scheme_id),
    INDEX idx_grade_scales_score_range (min_score, max_score),
    INDEX idx_grade_scales_gpa (gpa_value),
    INDEX idx_grade_scales_passing (is_passing),
    FOREIGN KEY (grading_scheme_id) REFERENCES grading_schemes(id) ON DELETE CASCADE
);

-- Assessments table (individual assessment instances)
CREATE TABLE assessments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    assessment_type_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    instructions LONGTEXT NULL,
    max_score DECIMAL(8,2) NOT NULL DEFAULT 100.00,
    weight_percentage DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    duration_minutes INT NULL,
    attempts_allowed INT DEFAULT 1,
    passing_score DECIMAL(8,2) NULL,
    assessment_date DATE NULL,
    start_time TIME NULL,
    end_time TIME NULL,
    due_date TIMESTAMP NULL,
    late_submission_allowed BOOLEAN DEFAULT FALSE,
    late_submission_penalty_percentage DECIMAL(5,2) DEFAULT 0.00,
    submission_method ENUM('online', 'offline', 'hybrid', 'presentation', 'observation') DEFAULT 'online',
    assessment_format ENUM('objective', 'essay', 'practical', 'project', 'presentation', 'portfolio', 'mixed') NOT NULL,
    question_count INT NULL,
    randomize_questions BOOLEAN DEFAULT FALSE,
    randomize_options BOOLEAN DEFAULT FALSE,
    show_correct_answers BOOLEAN DEFAULT FALSE,
    show_score_immediately BOOLEAN DEFAULT FALSE,
    allow_review BOOLEAN DEFAULT TRUE,
    review_period_days INT DEFAULT 7,
    proctoring_required BOOLEAN DEFAULT FALSE,
    proctoring_method ENUM('none', 'auto', 'manual', 'live', 'ai') DEFAULT 'none',
    room_id BIGINT UNSIGNED NULL, -- For offline assessments
    required_materials JSON NULL,
    rubric_id BIGINT UNSIGNED NULL,
    is_published BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NOT NULL,
    published_by BIGINT UNSIGNED NULL,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_assessments_subject (subject_id),
    INDEX idx_assessments_type (assessment_type_id),
    INDEX idx_assessments_date (assessment_date),
    INDEX idx_assessments_due_date (due_date),
    INDEX idx_assessments_published (is_published),
    INDEX idx_assessments_active (is_active),
    INDEX idx_assessments_created_by (created_by),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_type_id) REFERENCES assessment_types(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
    FOREIGN KEY (rubric_id) REFERENCES rubrics(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (published_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Student grades table (individual student grades for assessments)
CREATE TABLE student_grades (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assessment_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(8,2) NULL,
    max_obtainable_score DECIMAL(8,2) NOT NULL,
    score_percentage DECIMAL(5,2) NULL,
    grade_letter VARCHAR(5) NULL,
    gpa_points DECIMAL(3,2) NULL,
    is_passed BOOLEAN NULL,
    graded_by BIGINT UNSIGNED NULL,
    graded_at TIMESTAMP NULL,
    grading_duration_minutes INT NULL,
    feedback TEXT NULL,
    detailed_feedback JSON NULL,
    rubric_scores JSON NULL, -- Individual rubric criteria scores
    attachment_urls JSON NULL,
    submission_urls JSON NULL,
    submission_data JSON NULL,
    submission_date TIMESTAMP NULL,
    late_submission BOOLEAN DEFAULT FALSE,
    late_submission_duration_minutes INT DEFAULT 0,
    late_submission_penalty DECIMAL(5,2) DEFAULT 0.00,
    final_score DECIMAL(8,2) NULL, -- After penalty calculation
    adjustment_points DECIMAL(5,2) DEFAULT 0.00,
    adjustment_reason TEXT NULL,
    academic_integrity_flag BOOLEAN DEFAULT FALSE,
    academic_integrity_notes TEXT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    review_status ENUM('pending', 'in_review', 'completed', 'escalated') DEFAULT 'pending',
    review_requested_by BIGINT UNSIGNED NULL,
    review_requested_at TIMESTAMP NULL,
    review_notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY student_grades_unique (assessment_id, student_id),
    INDEX idx_student_grades_assessment (assessment_id),
    INDEX idx_student_grades_student (student_id),
    INDEX idx_student_grades_score (score),
    INDEX idx_student_grades_grade_letter (grade_letter),
    INDEX idx_student_grades_graded_by (graded_by),
    INDEX idx_student_grades_graded_at (graded_at),
    INDEX idx_student_grades_verified (is_verified),
    INDEX idx_student_grades_review_status (review_status),
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (graded_by) REFERENCES user_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (verified_by) REFERENCES user_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (review_requested_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Grade books table (final grades for subjects)
CREATE TABLE grade_books (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    semester_id BIGINT UNSIGNED NOT NULL,
    class_section_id BIGINT UNSIGNED NULL,
    assessment_categories JSON NOT NULL, -- e.g., {"quiz": 20, "midterm": 30, "final": 40, "assignment": 10}
    category_scores JSON NULL, -- Individual category scores
    quiz_score DECIMAL(5,2) DEFAULT 0.00,
    assignment_score DECIMAL(5,2) DEFAULT 0.00,
    midterm_score DECIMAL(5,2) DEFAULT 0.00,
    final_score DECIMAL(5,2) DEFAULT 0.00,
    practicum_score DECIMAL(5,2) DEFAULT 0.00,
    project_score DECIMAL(5,2) DEFAULT 0.00,
    participation_score DECIMAL(5,2) DEFAULT 0.00,
    presentation_score DECIMAL(5,2) DEFAULT 0.00,
    portfolio_score DECIMAL(5,2) DEFAULT 0.00,
    attendance_score DECIMAL(5,2) DEFAULT 0.00,
    extra_credit_score DECIMAL(5,2) DEFAULT 0.00,
    total_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    total_weighted_score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    adjusted_score DECIMAL(5,2) DEFAULT 0.00,
    final_grade DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    grade_letter VARCHAR(5) NULL,
    gpa DECIMAL(3,2) NULL,
    quality_points DECIMAL(4,2) NULL,
    rank_in_class INT NULL,
    percentile DECIMAL(5,2) NULL,
    is_pass BOOLEAN NULL,
    credits_earned DECIMAL(5,2) NULL,
    repeat_count INT DEFAULT 0,
    first_attempt_score DECIMAL(5,2) NULL,
    previous_grade_letter VARCHAR(5) NULL,
    grade_status ENUM('provisional', 'final', 'appealed', 'corrected') DEFAULT 'provisional',
    grading_method ENUM('weighted_average', 'best_of', 'latest', 'cumulative') DEFAULT 'weighted_average',
    final_grading_method JSON NULL, -- Specific method used for final grade
    grade_distribution_position JSON NULL,
    statistical_info JSON NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    grade_published BOOLEAN DEFAULT FALSE,
    published_by BIGINT UNSIGNED NULL,
    published_at TIMESTAMP NULL,
    grade_report_generated BOOLEAN DEFAULT FALSE,
    grade_report_url VARCHAR(255) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY grade_books_unique (subject_id, student_id, semester_id),
    INDEX idx_grade_books_subject (subject_id),
    INDEX idx_grade_books_student (student_id),
    INDEX idx_grade_books_semester (semester_id),
    INDEX idx_grade_books_class_section (class_section_id),
    INDEX idx_grade_books_final_grade (final_grade),
    INDEX idx_grade_books_grade_letter (grade_letter),
    INDEX idx_grade_books_gpa (gpa),
    INDEX idx_grade_books_is_pass (is_pass),
    INDEX idx_grade_books_verified (is_verified),
    INDEX idx_grade_books_published (grade_published),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (class_section_id) REFERENCES class_sections(id) ON DELETE SET NULL,
    FOREIGN KEY (verified_by) REFERENCES user_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (published_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Grade appeals table
CREATE TABLE grade_appeals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_grade_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    assessment_id BIGINT UNSIGNED NULL, -- NULL if appealing final grade
    appeal_type ENUM('individual_assessment', 'final_grade', 'calculation_error', 'procedural_issue', 'academic_integrity') NOT NULL,
    original_score DECIMAL(8,2) NULL,
    original_grade_letter VARCHAR(5) NULL,
    requested_score DECIMAL(8,2) NULL,
    requested_grade_letter VARCHAR(5) NULL,
    appeal_reason TEXT NOT NULL,
    detailed_explanation TEXT NULL,
    supporting_documents JSON NULL,
    evidence_provided JSON NULL,
    witness_information JSON NULL,
    appeal_status ENUM('submitted', 'under_review', 'pending_response', 'approved', 'rejected', 'escalated', 'withdrawn') DEFAULT 'submitted',
    priority_level ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    submitted_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    deadline_date DATE NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    reviewed_at TIMESTAMP NULL,
    review_duration_hours DECIMAL(8,2) NULL,
    review_committee JSON NULL,
    review_outcome ENUM('upheld', 'reversed', 'partial', 'escalated') NULL,
    new_score DECIMAL(8,2) NULL,
    new_grade_letter VARCHAR(5) NULL,
    adjustment_points DECIMAL(5,2) NULL,
    decision_reason TEXT NULL,
    detailed_findings TEXT NULL,
    committee_recommendations JSON NULL,
    implementation_notes TEXT NULL,
    final_decision_date DATE NULL,
    appeal_response TEXT NULL,
    student_notified BOOLEAN DEFAULT FALSE,
    student_notified_at TIMESTAMP NULL,
    documentation_updated BOOLEAN DEFAULT FALSE,
    appeal_history JSON NULL,
    is_confidential BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_grade_appeals_student_grade (student_grade_id),
    INDEX idx_grade_appeals_student (student_id),
    INDEX idx_grade_appeals_assessment (assessment_id),
    INDEX idx_grade_appeals_type (appeal_type),
    INDEX idx_grade_appeals_status (appeal_status),
    INDEX idx_grade_appeals_submitted_at (submitted_at),
    INDEX idx_grade_appeals_reviewed_by (reviewed_by),
    INDEX idx_grade_appeals_priority (priority_level),
    FOREIGN KEY (student_grade_id) REFERENCES student_grades(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- ========================================
-- 13. CLASS JOURNAL AND TEACHING MATERIALS TABLES
-- ========================================

-- Class journals table
CREATE TABLE class_journals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_instance_id BIGINT UNSIGNED NOT NULL,
    lecturer_id BIGINT UNSIGNED NOT NULL,
    meeting_number INT NOT NULL,
    topic VARCHAR(255) NOT NULL,
    sub_topics JSON NULL, -- Array of sub-topics covered
    learning_objectives TEXT NOT NULL,
    learning_outcomes_addressed JSON NULL, -- IDs of learning outcomes covered
    teaching_method ENUM('lecture', 'discussion', 'demonstration', 'practice', 'collaborative', 'problem_based', 'project_based', 'flipped_classroom', 'mixed') NOT NULL,
    teaching_techniques JSON NULL, -- Specific techniques used
    start_time TIME NULL,
    end_time TIME NULL,
    actual_duration_minutes INT NULL,
    status ENUM('draft', 'in_progress', 'completed', 'reviewed', 'published') DEFAULT 'draft',
    class_environment ENUM('excellent', 'good', 'fair', 'needs_improvement') NULL,
    student_participation ENUM('very_high', 'high', 'moderate', 'low', 'very_low') NULL,
    attendance_summary JSON NULL,
    teaching_materials_used JSON NULL,
    activities_conducted JSON NULL,
    problems_encountered TEXT NULL,
    solutions_applied TEXT NULL,
    follow_up_actions TEXT NULL,
    adjustments_made TEXT NULL,
    reflections TEXT NULL,
    self_assessment JSON NULL, -- Lecturer's self-assessment
    future_improvements TEXT NULL,
    additional_notes TEXT NULL,
    observer_present BOOLEAN DEFAULT FALSE,
    reviewed_by BIGINT UNSIGNED NULL,
    reviewed_at TIMESTAMP NULL,
    review_comments TEXT NULL,
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    journal_template_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_class_journals_instance (schedule_instance_id),
    INDEX idx_class_journals_lecturer (lecturer_id),
    INDEX idx_class_journals_meeting (meeting_number),
    INDEX idx_class_journals_status (status),
    INDEX idx_class_journals_published (is_published),
    INDEX idx_class_journals_reviewed_by (reviewed_by),
    INDEX idx_class_journals_template (journal_template_id),
    FOREIGN KEY (schedule_instance_id) REFERENCES schedule_instances(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES user_profiles(id) ON DELETE SET NULL,
    FOREIGN KEY (journal_template_id) REFERENCES journal_templates(id) ON DELETE SET NULL
);

-- Journal materials table (materials used in specific journal entries)
CREATE TABLE journal_materials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    material_type ENUM('slide', 'handout', 'video', 'audio', 'link', 'document', 'image', 'software', 'other') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    file_url VARCHAR(255) NULL,
    file_name VARCHAR(255) NULL,
    file_size BIGINT NULL,
    mime_type VARCHAR(100) NULL,
    external_url VARCHAR(500) NULL,
    material_category ENUM('primary', 'supplementary', 'reference', 'resource', 'example') NOT NULL,
    is_required BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    download_count INT DEFAULT 0,
    access_level ENUM('public', 'students_only', 'lecturer_only', 'admin_only') DEFAULT 'students_only',
    tags JSON NULL,
    material_metadata JSON NULL,
    copyright_info TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    upload_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    last_accessed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_journal_materials_journal (journal_id),
    INDEX idx_journal_materials_type (material_type),
    INDEX idx_journal_materials_category (material_category),
    INDEX idx_journal_materials_required (is_required),
    INDEX idx_journal_materials_created_by (created_by),
    FOREIGN KEY (journal_id) REFERENCES class_journals(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Learning activities table (detailed activities in each journal)
CREATE TABLE learning_activities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    activity_name VARCHAR(255) NOT NULL,
    activity_type ENUM('lecture', 'discussion', 'group_work', 'presentation', 'demonstration', 'practice', 'quiz', 'game', 'simulation', 'experiment', 'field_trip', 'guest_lecture', 'other') NOT NULL,
    description TEXT NULL,
    duration_minutes INT NOT NULL,
    start_time TIME NULL,
    end_time TIME NULL,
    objectives JSON NULL,
    materials_needed JSON NULL,
    instructions TEXT NULL,
    student_participation_level ENUM('passive', 'low', 'moderate', 'high', 'active', 'collaborative') NOT NULL,
    participation_assessment_method ENUM('observation', 'quiz', 'presentation', 'report', 'peer_evaluation', 'self_evaluation') NULL,
    learning_outcomes JSON NULL, -- Outcomes addressed by this activity
    activity_outcomes JSON NULL, -- Results of the activity
    problems_encountered TEXT NULL,
    adaptations_made TEXT NULL,
    effectiveness_rating TINYINT NULL, -- 1-5 rating
    student_feedback JSON NULL,
    next_steps TEXT NULL,
    resources_used JSON NULL,
    technology_used JSON NULL,
    assessment_conducted BOOLEAN DEFAULT FALSE,
    assessment_data JSON NULL,
    notes TEXT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_learning_activities_journal (journal_id),
    INDEX idx_learning_activities_type (activity_type),
    INDEX idx_learning_activities_participation (student_participation_level),
    INDEX idx_learning_activities_duration (duration_minutes),
    INDEX idx_learning_activities_effectiveness (effectiveness_rating),
    FOREIGN KEY (journal_id) REFERENCES class_journals(id) ON DELETE CASCADE
);

-- Student participations table (track individual student participation)
CREATE TABLE student_participations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    participation_type ENUM('question', 'answer', 'comment', 'discussion', 'presentation', 'group_work', 'leadership', 'peer_help', 'other') NOT NULL,
    contribution_level ENUM('minimal', 'basic', 'moderate', 'significant', 'excellent', 'outstanding') NOT NULL,
    contribution_details TEXT NULL,
    quality_rating TINYINT NULL, -- 1-5 rating
    activity_id BIGINT UNSIGNED NULL, -- Related learning activity
    interaction_with VARCHAR(255) NULL, -- Who student interacted with
    timestamp TIME NULL,
    is_positive BOOLEAN DEFAULT TRUE,
    needs_follow_up BOOLEAN DEFAULT FALSE,
    follow_up_actions TEXT NULL,
    lecturer_notes TEXT NULL,
    participation_score DECIMAL(5,2) NULL,
    affects_grade BOOLEAN DEFAULT FALSE,
    grade_impact DECIMAL(5,2) NULL,
    recorded_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_student_participations_journal (journal_id),
    INDEX idx_student_participations_student (student_id),
    INDEX idx_student_participations_type (participation_type),
    INDEX idx_student_participations_contribution (contribution_level),
    INDEX idx_student_participations_activity (activity_id),
    INDEX idx_student_participations_quality (quality_rating),
    INDEX idx_student_participations_recorded_by (recorded_by),
    FOREIGN KEY (journal_id) REFERENCES class_journals(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES learning_activities(id) ON DELETE SET NULL,
    FOREIGN KEY (recorded_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Journal reflections table (lecturer reflections on teaching)
CREATE TABLE journal_reflections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    reflection_type ENUM('self_assessment', 'student_feedback', 'teaching_effectiveness', 'lesson_improvement', 'professional_development', 'research_insights', 'other') NOT NULL,
    content TEXT NOT NULL,
    reflection_questions JSON NULL, -- Questions being reflected upon
    insights_gained TEXT NULL,
    challenges_identified TEXT NULL,
    successes_celebrated TEXT NULL,
    improvement_areas JSON NULL,
    professional_development_needs JSON NULL,
    action_items JSON NULL,
    timeline_for_improvement DATE NULL,
    collaboration_opportunities TEXT NULL,
    research_observations TEXT NULL,
    teaching_philosophy_impact TEXT NULL,
    student_learning_insights JSON NULL,
    curriculum_alignment_notes TEXT NULL,
    technology_integration_reflection TEXT NULL,
    inclusivity_considerations TEXT NULL,
    assessment_insights TEXT NULL,
    future_planning TEXT NULL,
    follow_up_required BOOLEAN DEFAULT FALSE,
    follow_up_actions TEXT NULL,
    is_private BOOLEAN DEFAULT TRUE, -- Private reflections vs shareable
    share_with_colleagues BOOLEAN DEFAULT FALSE,
    mentor_feedback TEXT NULL,
    mentoring_notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_journal_reflections_journal (journal_id),
    INDEX idx_journal_reflections_type (reflection_type),
    INDEX idx_journal_reflections_private (is_private),
    INDEX idx_journal_reflections_shareable (share_with_colleagues),
    INDEX idx_journal_reflections_follow_up (follow_up_required),
    FOREIGN KEY (journal_id) REFERENCES class_journals(id) ON DELETE CASCADE
);

-- Journal observers table (observations by supervisors/peers)
CREATE TABLE journal_observers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    observer_id BIGINT UNSIGNED NOT NULL,
    observer_type ENUM('supervisor', 'peer', 'mentor', 'quality_assurance', 'department_head', 'external_evaluator') NOT NULL,
    observation_type ENUM('formal', 'informal', 'peer_review', 'quality_assurance', 'mentorship', 'evaluation') NOT NULL,
    observation_purpose TEXT NULL,
    observation_framework JSON NULL, -- Framework used for observation
    strengths_identified TEXT NULL,
    areas_for_improvement TEXT NULL,
    specific_observations JSON NULL,
    teaching_techniques_observed JSON NULL,
    student_engagement_notes TEXT NULL,
    classroom_management_notes TEXT NULL,
    content_delivery_rating TINYINT NULL, -- 1-5 rating
    student_engagement_rating TINYINT NULL,
    classroom_management_rating TINYINT NULL,
    overall_rating TINYINT NULL,
    recommendations TEXT NULL,
    follow_up_required BOOLEAN DEFAULT FALSE,
    follow_up_timeline DATE NULL,
    mentorship_suggestions TEXT NULL,
    professional_development_recommendations JSON NULL,
    observation_summary TEXT NULL,
    compliance_notes TEXT NULL, -- For accreditation/compliance purposes
    next_observation_date DATE NULL,
    is_positive BOOLEAN DEFAULT TRUE,
    needs_immediate_attention BOOLEAN DEFAULT FALSE,
    observation_status ENUM('scheduled', 'in_progress', 'completed', 'follow_up_required', 'escalated') DEFAULT 'completed',
    observation_duration_minutes INT NULL,
    preparation_notes TEXT NULL,
    post_observation_meeting BOOLEAN DEFAULT FALSE,
    meeting_notes TEXT NULL,
    agreed_action_items JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_journal_observers_journal (journal_id),
    INDEX idx_journal_observers_observer (observer_id),
    INDEX idx_journal_observers_type (observer_type),
    INDEX idx_journal_observers_observation_type (observation_type),
    INDEX idx_journal_observers_rating (overall_rating),
    INDEX idx_journal_observers_status (observation_status),
    INDEX idx_journal_observers_follow_up (follow_up_required),
    FOREIGN KEY (journal_id) REFERENCES class_journals(id) ON DELETE CASCADE,
    FOREIGN KEY (observer_id) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Journal outcomes assessment table (assessment of learning outcomes)
CREATE TABLE journal_outcomes_assessment (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    learning_outcome_id BIGINT UNSIGNED NOT NULL,
    assessment_result ENUM('not_addressed', 'partially_addressed', 'addressed', 'well_addressed', 'excellently_addressed') NOT NULL,
    achievement_level TINYINT NULL, -- 1-5 scale
    evidence_provided TEXT NULL,
    assessment_method VARCHAR(255) NULL,
    student_performance_data JSON NULL,
    alignment_with_objectives JSON NULL,
    curriculum_mapping_notes TEXT NULL,
    prerequisite_assessment JSON NULL,
    future_preparation_needed JSON NULL,
    cross_course_connections TEXT NULL,
    real_world_applications TEXT NULL,
    student_demonstrations JSON NULL,
    assessment_tools_used JSON NULL,
    evidence_files JSON NULL,
    observer_observations TEXT NULL,
    improvement_suggestions TEXT NULL,
    best_practices_identified TEXT NULL,
    challenges_faced TEXT NULL,
    adaptations_made TEXT NULL,
    technology_integration JSON NULL,
    inclusive_practices JSON NULL,
    assessment_feedback TEXT NULL,
    follow_up_actions JSON NULL,
    next_steps_planning TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY journal_outcomes_assessment_unique (journal_id, learning_outcome_id),
    INDEX idx_journal_outcomes_assessment_journal (journal_id),
    INDEX idx_journal_outcomes_assessment_outcome (learning_outcome_id),
    INDEX idx_journal_outcomes_assessment_result (assessment_result),
    INDEX idx_journal_outcomes_assessment_level (achievement_level),
    FOREIGN KEY (journal_id) REFERENCES class_journals(id) ON DELETE CASCADE,
    FOREIGN KEY (learning_outcome_id) REFERENCES learning_outcomes(id) ON DELETE CASCADE
);

-- Teaching materials table (general teaching materials repository)
CREATE TABLE teaching_materials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id BIGINT UNSIGNED NOT NULL,
    lecturer_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    material_type ENUM('slide', 'handout', 'video', 'audio', 'link', 'document', 'image', 'software', 'simulation', 'game', 'dataset', 'case_study', 'other') NOT NULL,
    material_category ENUM('lecture', 'tutorial', 'assignment', 'reference', 'example', 'assessment', 'resource', 'supplementary') NOT NULL,
    file_url VARCHAR(255) NULL,
    file_name VARCHAR(255) NULL,
    file_size BIGINT NULL,
    mime_type VARCHAR(100) NULL,
    external_url VARCHAR(500) NULL,
    embed_code TEXT NULL, -- For embedded content
    material_content LONGTEXT NULL, -- For text-based materials
    material_version VARCHAR(20) DEFAULT '1.0',
    is_template BOOLEAN DEFAULT FALSE,
    template_category VARCHAR(255) NULL,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced', 'expert') NULL,
    estimated_duration_minutes INT NULL,
    prerequisites JSON NULL,
    learning_objectives JSON NULL,
    target_audience JSON NULL,
    tags JSON NULL,
    keywords JSON NULL,
    authorship JSON NULL, -- Multiple authors
    copyright_license VARCHAR(100) NULL,
    usage_rights TEXT NULL,
    attribution_requirements TEXT NULL,
    adaptation_allowed BOOLEAN DEFAULT TRUE,
    commercial_use_allowed BOOLEAN DEFAULT FALSE,
    is_public BOOLEAN DEFAULT FALSE, -- Shareable with other lecturers
    sharing_permissions JSON NULL,
    download_count INT DEFAULT 0,
    view_count INT DEFAULT 0,
    rating DECIMAL(3,2) NULL,
    rating_count INT DEFAULT 0,
    reviews JSON NULL,
    usage_statistics JSON NULL,
    last_accessed_at TIMESTAMP NULL,
    quality_rating TINYINT NULL, -- Internal quality rating
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    archived_at TIMESTAMP NULL,
    archive_reason TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_teaching_materials_subject (subject_id),
    INDEX idx_teaching_materials_lecturer (lecturer_id),
    INDEX idx_teaching_materials_type (material_type),
    INDEX idx_teaching_materials_category (material_category),
    INDEX idx_teaching_materials_template (is_template),
    INDEX idx_teaching_materials_public (is_public),
    INDEX idx_teaching_materials_active (is_active),
    INDEX idx_teaching_materials_rating (rating),
    INDEX idx_teaching_materials_approved_by (approved_by),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- ========================================
-- 14. SYSTEM CONFIGURATION AND SUPPORTING TABLES
-- ========================================

-- System settings table
CREATE TABLE system_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value JSON NOT NULL,
    type ENUM('string', 'number', 'boolean', 'array', 'object') NOT NULL,
    group_name VARCHAR(100) NULL,
    description TEXT NULL,
    is_public BOOLEAN DEFAULT FALSE, -- Accessible via public API
    is_editable BOOLEAN DEFAULT TRUE,
    validation_rules JSON NULL,
    default_value JSON NULL,
    user_id BIGINT UNSIGNED NULL, -- Last modified by
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_system_settings_key (key),
    INDEX idx_system_settings_group (group_name),
    INDEX idx_system_settings_public (is_public),
    INDEX idx_system_settings_editable (is_editable),
    FOREIGN KEY (user_id) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Notification settings table
CREATE TABLE notification_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    notification_type ENUM('email', 'sms', 'push', 'web', 'whatsapp', 'telegram') NOT NULL,
    event_type VARCHAR(100) NOT NULL, -- e.g., 'schedule_conflict', 'grade_published'
    is_enabled BOOLEAN DEFAULT TRUE,
    channels JSON NOT NULL, -- Which channels to use for this notification type
    frequency ENUM('immediate', 'hourly', 'daily', 'weekly', 'never') DEFAULT 'immediate',
    quiet_hours JSON NULL, -- Quiet hours for notifications
    priority_threshold ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    custom_settings JSON NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY notification_settings_unique (user_id, notification_type, event_type),
    INDEX idx_notification_settings_user (user_id),
    INDEX idx_notification_settings_type (notification_type),
    INDEX idx_notification_settings_event (event_type),
    INDEX idx_notification_settings_enabled (is_enabled),
    FOREIGN KEY (user_id) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Backup configurations table
CREATE TABLE backup_configurations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    backup_type ENUM('full', 'incremental', 'differential') NOT NULL,
    frequency ENUM('hourly', 'daily', 'weekly', 'monthly') NOT NULL,
    retention_days INT NOT NULL DEFAULT 30,
    storage_location ENUM('local', 's3', 'google_drive', 'azure', 'ftp') NOT NULL,
    storage_path VARCHAR(500) NOT NULL,
    compression_enabled BOOLEAN DEFAULT TRUE,
    encryption_enabled BOOLEAN DEFAULT FALSE,
    encryption_key VARCHAR(255) NULL,
    include_tables JSON NULL, -- Specific tables to include
    exclude_tables JSON NULL, -- Tables to exclude
    backup_retention_policy JSON NULL,
    notification_on_completion BOOLEAN DEFAULT TRUE,
    notification_emails JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_backup_at TIMESTAMP NULL,
    last_backup_status ENUM('success', 'failed', 'in_progress') NULL,
    backup_size BIGINT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_backup_configurations_type (backup_type),
    INDEX idx_backup_configurations_frequency (frequency),
    INDEX idx_backup_configurations_active (is_active),
    INDEX idx_backup_configurations_last_backup (last_backup_at),
    INDEX idx_backup_configurations_created_by (created_by),
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Audit logs table (comprehensive audit trail)
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(255) NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id BIGINT UNSIGNED NULL,
    properties JSON NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    session_id VARCHAR(255) NULL,
    request_id VARCHAR(255) NULL,
    log_name VARCHAR(255) DEFAULT 'default',
    description TEXT NULL,
    batch_uuid VARCHAR(36) NULL, -- For batch operations
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_audit_logs_user (user_id),
    INDEX idx_audit_logs_action (action),
    INDEX idx_audit_logs_subject (subject_type, subject_id),
    INDEX idx_audit_logs_created (created_at),
    INDEX idx_audit_logs_log_name (log_name),
    INDEX idx_audit_logs_batch_uuid (batch_uuid),
    FOREIGN KEY (user_id) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- API keys table
CREATE TABLE api_keys (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    key VARCHAR(255) UNIQUE NOT NULL,
    secret VARCHAR(255) NULL,
    permissions JSON NOT NULL, -- Array of allowed actions
    rate_limit_per_hour INT DEFAULT 1000,
    rate_limit_per_day INT DEFAULT 10000,
    allowed_ips JSON NULL,
    allowed_origins JSON NULL,
    expires_at TIMESTAMP NULL,
    last_used_at TIMESTAMP NULL,
    usage_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    is_revoked BOOLEAN DEFAULT FALSE,
    revoked_at TIMESTAMP NULL,
    revoked_by BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_api_keys_user (user_id),
    INDEX idx_api_keys_key (key),
    INDEX idx_api_keys_active (is_active),
    INDEX idx_api_keys_revoked (is_revoked),
    INDEX idx_api_keys_expires (expires_at),
    INDEX idx_api_keys_last_used (last_used_at),
    FOREIGN KEY (user_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (revoked_by) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- ========================================
-- 15. INTEGRATION AND EXTERNAL SYSTEMS TABLES
-- ========================================

-- External integrations table
CREATE TABLE external_integrations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(255) NOT NULL,
    integration_type ENUM('calendar', 'email', 'sms', 'payment', 'lms', 'sis', 'authentication', 'storage', 'analytics', 'other') NOT NULL,
    api_endpoint VARCHAR(500) NULL,
    api_version VARCHAR(50) NULL,
    credentials JSON NOT NULL, -- Encrypted credentials
    configuration JSON NOT NULL,
    status ENUM('active', 'inactive', 'error', 'disabled') DEFAULT 'inactive',
    last_sync_at TIMESTAMP NULL,
    last_sync_status ENUM('success', 'failed', 'partial', 'error') NULL,
    sync_frequency ENUM('real_time', 'hourly', 'daily', 'weekly', 'manual') DEFAULT 'daily',
    error_message TEXT NULL,
    retry_count INT DEFAULT 0,
    max_retries INT DEFAULT 3,
    next_retry_at TIMESTAMP NULL,
    monitoring_enabled BOOLEAN DEFAULT TRUE,
    alert_on_failure BOOLEAN DEFAULT TRUE,
    alert_emails JSON NULL,
    log_level ENUM('debug', 'info', 'warning', 'error') DEFAULT 'info',
    custom_headers JSON NULL,
    rate_limits JSON NULL,
    timeout_seconds INT DEFAULT 30,
    test_connection BOOLEAN DEFAULT TRUE,
    last_test_at TIMESTAMP NULL,
    last_test_result BOOLEAN NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_external_integrations_service (service_name),
    INDEX idx_external_integrations_type (integration_type),
    INDEX idx_external_integrations_status (status),
    INDEX idx_external_integrations_last_sync (last_sync_at),
    INDEX idx_external_integrations_active (is_active),
    INDEX idx_external_integrations_created_by (created_by),
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Calendar syncs table
CREATE TABLE calendar_syncs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    calendar_type ENUM('google', 'outlook', 'apple', 'caldav', 'other') NOT NULL,
    external_calendar_id VARCHAR(255) NOT NULL,
    external_calendar_name VARCHAR(255) NULL,
    sync_direction ENUM('bidirectional', 'import_only', 'export_only') NOT NULL DEFAULT 'bidirectional',
    sync_token VARCHAR(500) NULL, -- For incremental sync
    sync_settings JSON NOT NULL,
    sync_filters JSON NULL, -- What to sync
    last_sync_at TIMESTAMP NULL,
    last_sync_status ENUM('success', 'failed', 'partial', 'error') NULL,
    sync_errors JSON NULL,
    total_events_synced INT DEFAULT 0,
    auto_sync BOOLEAN DEFAULT TRUE,
    sync_frequency ENUM('real_time', 'every_15_minutes', 'hourly', 'daily') DEFAULT 'hourly',
    calendar_permissions JSON NULL, -- Permissions granted
    webhook_url VARCHAR(500) NULL, -- For real-time sync
    webhook_secret VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY calendar_syncs_unique (user_id, calendar_type, external_calendar_id),
    INDEX idx_calendar_syncs_user (user_id),
    INDEX idx_calendar_syncs_type (calendar_type),
    INDEX idx_calendar_syncs_external_id (external_calendar_id),
    INDEX idx_calendar_syncs_direction (sync_direction),
    INDEX idx_calendar_syncs_last_sync (last_sync_at),
    INDEX idx_calendar_syncs_active (is_active),
    FOREIGN KEY (user_id) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- File attachments table (centralized file management)
CREATE TABLE file_attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attachable_type VARCHAR(255) NOT NULL, -- Model class name
    attachable_id BIGINT UNSIGNED NOT NULL, -- Model ID
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NULL,
    file_path VARCHAR(500) NOT NULL,
    file_url VARCHAR(500) NULL,
    file_size BIGINT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_extension VARCHAR(10) NOT NULL,
    file_hash VARCHAR(64) NULL, -- SHA-256 hash
    uploaded_by BIGINT UNSIGNED NOT NULL,
    upload_source ENUM('web', 'mobile', 'api', 'import', 'sync') DEFAULT 'web',
    storage_location ENUM('local', 's3', 'google_drive', 'azure', 'cdn') DEFAULT 'local',
    storage_path VARCHAR(500) NULL,
    is_public BOOLEAN DEFAULT FALSE,
    access_permissions JSON NULL,
    download_count INT DEFAULT 0,
    last_downloaded_at TIMESTAMP NULL,
    virus_scan_status ENUM('pending', 'scanning', 'clean', 'infected', 'error') DEFAULT 'pending',
    virus_scan_result TEXT NULL,
    is_encrypted BOOLEAN DEFAULT FALSE,
    encryption_key VARCHAR(255) NULL,
    backup_status ENUM('pending', 'backed_up', 'failed') DEFAULT 'pending',
    expires_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL, -- Soft delete
    metadata JSON NULL, -- Additional file metadata
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_file_attachments_attachable (attachable_type, attachable_id),
    INDEX idx_file_attachments_uploaded_by (uploaded_by),
    INDEX idx_file_attachments_mime_type (mime_type),
    INDEX idx_file_attachments_public (is_public),
    INDEX idx_file_attachments_scan_status (virus_scan_status),
    INDEX idx_file_attachments_expires (expires_at),
    INDEX idx_file_attachments_deleted (deleted_at),
    FOREIGN KEY (uploaded_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Notifications table (central notification system)
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(100) NOT NULL, -- e.g., 'schedule_conflict', 'grade_published'
    category ENUM('academic', 'administrative', 'system', 'personal', 'emergency') NOT NULL,
    priority ENUM('low', 'medium', 'high', 'critical', 'urgent') DEFAULT 'medium',
    channels JSON NOT NULL, -- Which channels to send through
    data JSON NULL, -- Additional data payload
    action_url VARCHAR(500) NULL, -- URL for user action
    action_text VARCHAR(255) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    is_archived BOOLEAN DEFAULT FALSE,
    archived_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    delivery_status JSON NULL, -- Delivery status per channel
    sent_at TIMESTAMP NULL,
    scheduled_at TIMESTAMP NULL,
    retry_count INT DEFAULT 0,
    max_retries INT DEFAULT 3,
    error_message TEXT NULL,
    template_id BIGINT UNSIGNED NULL,
    batch_id VARCHAR(36) NULL, -- For batch notifications
    parent_id BIGINT UNSIGNED NULL, -- For thread replies
    reply_to_id BIGINT UNSIGNED NULL, -- Reply notification
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_type (type),
    INDEX idx_notifications_category (category),
    INDEX idx_notifications_priority (priority),
    INDEX idx_notifications_read (is_read),
    INDEX idx_notifications_archived (is_archived),
    INDEX idx_notifications_expires (expires_at),
    INDEX idx_notifications_sent (sent_at),
    INDEX idx_notifications_scheduled (scheduled_at),
    INDEX idx_notifications_batch (batch_id),
    INDEX idx_notifications_parent (parent_id),
    FOREIGN KEY (user_id) REFERENCES user_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES notification_templates(id) ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES notifications(id) ON DELETE SET NULL
);

-- Notification channels table (device tokens for push notifications)
CREATE TABLE notification_channels (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    channel_type ENUM('web_push', 'mobile_push', 'sms', 'whatsapp', 'telegram', 'slack', 'email', 'webhook') NOT NULL,
    device_token VARCHAR(1000) NOT NULL, -- Token for push notifications
    device_info JSON NULL, -- Device information
    app_version VARCHAR(50) NULL,
    platform VARCHAR(50) NULL, -- iOS, Android, Web, etc.
    is_active BOOLEAN DEFAULT TRUE,
    is_verified BOOLEAN DEFAULT FALSE,
    verified_at TIMESTAMP NULL,
    last_used_at TIMESTAMP NULL,
    notification_preferences JSON NULL, -- Per-channel preferences
    rate_limit_per_hour INT DEFAULT 100,
    rate_limit_remaining INT DEFAULT 100,
    rate_limit_reset_at TIMESTAMP NULL,
    delivery_statistics JSON NULL,
    error_count INT DEFAULT 0,
    last_error_at TIMESTAMP NULL,
    last_error_message TEXT NULL,
    disabled_reason TEXT NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_notification_channels_user (user_id),
    INDEX idx_notification_channels_type (channel_type),
    INDEX idx_notification_channels_token (device_token),
    INDEX idx_notification_channels_active (is_active),
    INDEX idx_notification_channels_verified (is_verified),
    INDEX idx_notification_channels_last_used (last_used_at),
    INDEX idx_notification_channels_expires (expires_at),
    FOREIGN KEY (user_id) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- ========================================
-- 16. ADDITIONAL UTILITY TABLES
-- ========================================

-- Languages table (for multi-language support)
CREATE TABLE languages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) UNIQUE NOT NULL, -- e.g., 'en', 'id', 'fr'
    name VARCHAR(100) NOT NULL,
    native_name VARCHAR(100) NOT NULL,
    is_rtl BOOLEAN DEFAULT FALSE, -- Right-to-left language
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    flag_emoji VARCHAR(10) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_languages_code (code),
    INDEX idx_languages_active (is_active),
    INDEX idx_languages_default (is_default),
    INDEX idx_languages_sort (sort_order)
);

-- Translations table (for UI translations)
CREATE TABLE translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    language_id BIGINT UNSIGNED NOT NULL,
    group_name VARCHAR(100) NOT NULL, -- e.g., 'validation', 'menu', 'buttons'
    key_name VARCHAR(255) NOT NULL,
    value TEXT NOT NULL,
    is_plural BOOLEAN DEFAULT FALSE,
    plural_rules JSON NULL,
    context VARCHAR(255) NULL,
    namespace VARCHAR(100) DEFAULT '*',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY translations_unique (language_id, group_name, key_name),
    INDEX idx_translations_language (language_id),
    INDEX idx_translations_group (group_name),
    INDEX idx_translations_key (key_name),
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
);

-- Audit event types table (standardized audit events)
CREATE TABLE audit_event_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(255) UNIQUE NOT NULL,
    event_category VARCHAR(100) NOT NULL,
    description TEXT NULL,
    log_level ENUM('debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency') DEFAULT 'info',
    retention_days INT DEFAULT 365,
    is_active BOOLEAN DEFAULT TRUE,
    requires_approval BOOLEAN DEFAULT FALSE,
    alert_on_occurrence BOOLEAN DEFAULT FALSE,
    alert_threshold INT DEFAULT 1,
    alert_timeframe_minutes INT DEFAULT 60,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_audit_event_types_name (event_name),
    INDEX idx_audit_event_types_category (event_category),
    INDEX idx_audit_event_types_level (log_level),
    INDEX idx_audit_event_types_active (is_active)
);

-- ========================================
-- 17. ADDITIONAL TABLES NEEDED FOR COMPLETENESS
-- ========================================

-- Rubrics table (for assessment rubrics)
CREATE TABLE rubrics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    assessment_id BIGINT UNSIGNED NULL, -- If specific to one assessment
    subject_id BIGINT UNSIGNED NULL, -- If reusable for subject
    created_by BIGINT UNSIGNED NOT NULL,
    rubric_type ENUM('analytical', 'holistic', 'checklist', 'rating_scale') NOT NULL,
    total_points DECIMAL(8,2) NOT NULL DEFAULT 100.00,
    scoring_levels JSON NOT NULL, -- e.g., [{"name": "Excellent", "points": 4}, ...]
    instructions TEXT NULL,
    is_template BOOLEAN DEFAULT FALSE,
    is_public BOOLEAN DEFAULT FALSE, -- Shareable with other lecturers
    usage_count INT DEFAULT 0,
    average_score DECIMAL(5,2) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_rubrics_assessment (assessment_id),
    INDEX idx_rubrics_subject (subject_id),
    INDEX idx_rubrics_created_by (created_by),
    INDEX idx_rubrics_type (rubric_type),
    INDEX idx_rubrics_template (is_template),
    INDEX idx_rubrics_public (is_public),
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Rubric criteria table
CREATE TABLE rubric_criteria (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rubric_id BIGINT UNSIGNED NOT NULL,
    criteria_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    weight_percentage DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    max_points DECIMAL(8,2) NOT NULL,
    display_order INT DEFAULT 0,
    scoring_descriptions JSON NOT NULL, -- Descriptions for each score level
    is_required BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_rubric_criteria_rubric (rubric_id),
    INDEX idx_rubric_criteria_order (display_order),
    INDEX idx_rubric_criteria_weight (weight_percentage),
    FOREIGN KEY (rubric_id) REFERENCES rubrics(id) ON DELETE CASCADE
);

-- Rubric scores table
CREATE TABLE rubric_scores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rubric_id BIGINT UNSIGNED NOT NULL,
    criterion_id BIGINT UNSIGNED NOT NULL,
    student_grade_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(8,2) NOT NULL,
    max_score DECIMAL(8,2) NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    score_level VARCHAR(100) NULL, -- e.g., "Excellent", "Good", etc.
    feedback TEXT NULL,
    grader_id BIGINT UNSIGNED NULL,
    graded_at TIMESTAMP NULL,
    confidence_level ENUM('low', 'medium', 'high') NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY rubric_scores_unique (criterion_id, student_grade_id),
    INDEX idx_rubric_scores_rubric (rubric_id),
    INDEX idx_rubric_scores_criterion (criterion_id),
    INDEX idx_rubric_scores_student_grade (student_grade_id),
    INDEX idx_rubric_scores_score (score),
    INDEX idx_rubric_scores_grader (grader_id),
    FOREIGN KEY (rubric_id) REFERENCES rubrics(id) ON DELETE CASCADE,
    FOREIGN KEY (criterion_id) REFERENCES rubric_criteria(id) ON DELETE CASCADE,
    FOREIGN KEY (student_grade_id) REFERENCES student_grades(id) ON DELETE CASCADE,
    FOREIGN KEY (grader_id) REFERENCES user_profiles(id) ON DELETE SET NULL
);

-- Journal templates table
CREATE TABLE journal_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    template_type ENUM('class_journal', 'meeting_minutes', 'observation', 'reflection', 'assessment') NOT NULL,
    template_content LONGTEXT NOT NULL, -- Template with placeholders
    template_variables JSON NOT NULL, -- Available variables
    css_styles LONGTEXT NULL,
    program_study_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    is_default BOOLEAN DEFAULT FALSE,
    usage_count INT DEFAULT 0,
    version VARCHAR(20) DEFAULT '1.0',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_journal_templates_type (template_type),
    INDEX idx_journal_templates_program_study (program_study_id),
    INDEX idx_journal_templates_created_by (created_by),
    INDEX idx_journal_templates_public (is_public),
    INDEX idx_journal_templates_default (is_default),
    FOREIGN KEY (program_study_id) REFERENCES program_studies(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- Notification templates table
CREATE TABLE notification_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    template_type ENUM('email', 'sms', 'push', 'web') NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    subject_template VARCHAR(500) NULL, -- For email notifications
    body_template LONGTEXT NOT NULL,
    template_variables JSON NOT NULL,
    css_styles LONGTEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,
    usage_count INT DEFAULT 0,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_notification_templates_type (template_type),
    INDEX idx_notification_templates_event (event_type),
    INDEX idx_notification_templates_active (is_active),
    INDEX idx_notification_templates_default (is_default),
    FOREIGN KEY (created_by) REFERENCES user_profiles(id) ON DELETE CASCADE
);

-- ========================================
-- VIEWS FOR REPORTING AND ANALYTICS
-- ========================================

-- Student attendance summary view
CREATE VIEW student_attendance_summary AS
SELECT
    s.id as student_id,
    s.nim,
    s.name as student_name,
    ps.name as program_study,
    sem.name as semester,
    COUNT(a.id) as total_sessions,
    SUM(CASE WHEN a.attendance_status = 'present' THEN 1 ELSE 0 END) as present_count,
    SUM(CASE WHEN a.attendance_status = 'late' THEN 1 ELSE 0 END) as late_count,
    SUM(CASE WHEN a.attendance_status = 'absent' THEN 1 ELSE 0 END) as absent_count,
    SUM(CASE WHEN a.attendance_status IN ('excused', 'sick', 'leave') THEN 1 ELSE 0 END) as excused_count,
    ROUND(
        (SUM(CASE WHEN a.attendance_status = 'present' THEN 1 ELSE 0 END) * 100.0) /
        NULLIF(COUNT(a.id), 0), 2
    ) as attendance_percentage,
    ROUND(
        AVG(CASE WHEN a.attendance_status = 'late' THEN a.late_duration_minutes ELSE 0 END), 2
    ) as average_late_duration
FROM students s
JOIN program_studies ps ON s.program_study_id = ps.id
LEFT JOIN attendances a ON s.id = a.student_id
LEFT JOIN schedule_instances si ON a.schedule_instance_id = si.id
LEFT JOIN schedules sched ON si.schedule_id = sched.id
LEFT JOIN semesters sem ON sched.semester_id = sem.id
WHERE sem.is_current = TRUE OR sem.id IS NULL
GROUP BY s.id, s.nim, s.name, ps.name, sem.name;

-- Lecturer workload summary view
CREATE VIEW lecturer_workload_summary AS
SELECT
    l.id as lecturer_id,
    l.nip,
    l.name as lecturer_name,
    ps.name as program_study,
    sem.name as semester,
    COUNT(DISTINCT sched.id) as total_schedules,
    COUNT(DISTINCT sub.id) as total_subjects,
    SUM(sub.credits) as total_credits,
    SUM(CASE WHEN sub.theory_hours > 0 THEN sub.theory_hours ELSE 0 END) as total_theory_hours,
    SUM(CASE WHEN sub.practice_hours > 0 THEN sub.practice_hours ELSE 0 END) as total_practice_hours,
    COUNT(DISTINCT si.id) as total_classes,
    COUNT(DISTINCT si.date) as total_teaching_days,
    ROUND(AVG(sub.credits), 2) as average_credits_per_subject
FROM lecturers l
JOIN program_studies ps ON l.program_study_id = ps.id
LEFT JOIN schedules sched ON l.id = sched.lecturer_id
LEFT JOIN subjects sub ON sched.subject_id = sub.id
LEFT JOIN semesters sem ON sched.semester_id = sem.id
LEFT JOIN schedule_instances si ON sched.id = si.schedule_id
WHERE sem.is_current = TRUE OR sem.id IS NULL
GROUP BY l.id, l.nip, l.name, ps.name, sem.name;

-- Room utilization summary view
CREATE VIEW room_utilization_summary AS
SELECT
    r.id as room_id,
    r.code as room_code,
    r.name as room_name,
    r.capacity,
    r.room_type,
    b.name as building,
    COUNT(DISTINCT sched.id) as scheduled_classes,
    COUNT(DISTINCT si.id) as total_classes,
    COUNT(DISTINCT si.date) as days_used,
    SUM(
        TIMESTAMPDIFF(MINUTE, si.start_time, si.end_time)
    ) as total_minutes_used,
    ROUND(
        AVG(
            COUNT(si.id) OVER (
                PARTITION BY si.date, sched.room_id
            )
        ), 2
    ) as average_daily_classes,
    ROUND(
        (COUNT(DISTINCT si.id) * 100.0) /
        NULLIF(
            (
                SELECT COUNT(*)
                FROM schedule_instances si2
                JOIN schedules sched2 ON si2.schedule_id = sched2.id
                JOIN semesters sem ON sched2.semester_id = sem.id
                WHERE sem.is_current = TRUE
            ), 0
        ), 2
    ) as utilization_percentage
FROM rooms r
LEFT JOIN buildings b ON r.building_id = b.id
LEFT JOIN schedules sched ON r.id = sched.room_id
LEFT JOIN subjects sub ON sched.subject_id = sub.id
LEFT JOIN semesters sem ON sched.semester_id = sem.id
LEFT JOIN schedule_instances si ON sched.id = si.schedule_id
WHERE sem.is_current = TRUE OR sem.id IS NULL
GROUP BY r.id, r.code, r.name, r.capacity, r.room_type, b.name;

-- Grade distribution view
CREATE VIEW grade_distribution_summary AS
SELECT
    sub.id as subject_id,
    sub.code as subject_code,
    sub.name as subject_name,
    ps.name as program_study,
    sem.name as semester,
    COUNT(gb.id) as total_students,
    SUM(CASE WHEN gb.grade_letter = 'A' THEN 1 ELSE 0 END) as grade_a_count,
    SUM(CASE WHEN gb.grade_letter = 'B+' THEN 1 ELSE 0 END) as grade_b_plus_count,
    SUM(CASE WHEN gb.grade_letter = 'B' THEN 1 ELSE 0 END) as grade_b_count,
    SUM(CASE WHEN gb.grade_letter = 'C+' THEN 1 ELSE 0 END) as grade_c_plus_count,
    SUM(CASE WHEN gb.grade_letter = 'C' THEN 1 ELSE 0 END) as grade_c_count,
    SUM(CASE WHEN gb.grade_letter = 'D' THEN 1 ELSE 0 END) as grade_d_count,
    SUM(CASE WHEN gb.grade_letter = 'E' THEN 1 ELSE 0 END) as grade_e_count,
    ROUND(AVG(gb.final_grade), 2) as average_grade,
    ROUND(MAX(gb.final_grade), 2) as highest_grade,
    ROUND(MIN(gb.final_grade), 2) as lowest_grade,
    ROUND(STDDEV(gb.final_grade), 2) as grade_standard_deviation,
    SUM(CASE WHEN gb.is_pass = TRUE THEN 1 ELSE 0 END) as passed_count,
    ROUND((SUM(CASE WHEN gb.is_pass = TRUE THEN 1 ELSE 0 END) * 100.0) / COUNT(gb.id), 2) as pass_rate_percentage
FROM subjects sub
JOIN program_studies ps ON sub.program_study_id = ps.id
LEFT JOIN grade_books gb ON sub.id = gb.subject_id
LEFT JOIN semesters sem ON gb.semester_id = sem.id
WHERE sem.is_current = TRUE OR sem.id IS NULL
GROUP BY sub.id, sub.code, sub.name, ps.name, sem.name;

-- Conflict analytics view
CREATE VIEW conflict_analytics_summary AS
SELECT
    DATE(sc.created_at) as conflict_date,
    sc.conflict_type,
    sc.severity_level,
    COUNT(*) as total_conflicts,
    SUM(CASE WHEN sc.is_resolved = TRUE THEN 1 ELSE 0 END) as resolved_conflicts,
    ROUND(
        (SUM(CASE WHEN sc.is_resolved = TRUE THEN 1 ELSE 0 END) * 100.0) /
        COUNT(*), 2
    ) as resolution_rate,
    AVG(sc.conflict_score) as average_conflict_score,
    COUNT(DISTINCT sc.schedule_draft_id) as affected_schedules,
    COUNT(DISTINCT sc.conflict_with_id) as conflicting_entities
FROM schedule_conflicts sc
GROUP BY DATE(sc.created_at), sc.conflict_type, sc.severity_level
ORDER BY conflict_date DESC, total_conflicts DESC;

-- ========================================
-- FINAL SETUP COMMANDS
-- ========================================

-- Insert default system settings
INSERT INTO system_settings (key, value, type, group_name, description, is_public) VALUES
('system_name', '"Academic Scheduling System"', 'string', 'general', 'System name displayed in UI', true),
('system_version', '"1.0.0"', 'string', 'general', 'Current system version', true),
('max_schedule_conflicts', '10', 'number', 'scheduling', 'Maximum allowed conflicts per schedule', false),
('default_timezone', '"Asia/Jakarta"', 'string', 'general', 'Default system timezone', true),
('auto_conflict_detection', 'true', 'boolean', 'scheduling', 'Enable automatic conflict detection', false),
('max_upload_size', '10485760', 'number', 'files', 'Maximum file upload size in bytes', true),
('session_timeout', '120', 'number', 'security', 'Session timeout in minutes', false),
('password_min_length', '8', 'number', 'security', 'Minimum password length', false),
('enable_two_factor', 'true', 'boolean', 'security', 'Enable two-factor authentication', false),
('default_language', '"id"', 'string', 'localization', 'Default system language', true);

-- Insert default roles
INSERT INTO roles (name, guard_name, permissions, created_at, updated_at) VALUES
('Super Admin', 'web', '["*"]', NOW(), NOW()),
('Admin', 'web', '["schedules.*", "users.view", "reports.*"]', NOW(), NOW()),
('Kaprodi', 'web', '["schedules.manage", "sk.approve", "reports.academic"]', NOW(), NOW()),
('Dosen', 'web', '["schedules.own", "journals.own", "grades.own"]', NOW(), NOW()),
('Staff', 'web', '["schedules.view", "attendance.*", "basic"]', NOW(), NOW()),
('Student', 'web', '["schedules.view", "attendance.own", "grades.own"]', NOW(), NOW());

-- Insert default languages
INSERT INTO languages (code, name, native_name, is_rtl, is_active, is_default, sort_order, flag_emoji) VALUES
('id', 'Indonesian', 'Bahasa Indonesia', false, true, true, 1, '🇮🇩'),
('en', 'English', 'English', false, true, false, 2, '🇺🇸');

-- Insert conflict rules
INSERT INTO conflict_rules (rule_name, conflict_type, entity_type, condition_logic, severity_level, auto_resolution, is_active, priority) VALUES
('Lecturer Double Booking', 'lecturer', 'schedule', '{"same_lecturer": true, "overlapping_time": true, "same_semester": true}', 'critical', '{"suggest_alternative_time": true, "suggest_alternative_room": true}', true, 1),
('Room Double Booking', 'room', 'schedule', '{"same_room": true, "overlapping_time": true, "same_semester": true}', 'critical', '{"suggest_alternative_room": true, "suggest_alternative_time": true}', true, 1),
('Student Schedule Conflict', 'student', 'schedule', '{"same_students": true, "overlapping_time": true, "same_semester": true}', 'high', '{"suggest_alternative_time": true}', true, 2),
('Room Capacity Exceeded', 'capacity', 'schedule', '{"students_gt_capacity": true}', 'medium', '{"suggest_larger_room": true, "suggest_split_class": true}', true, 3),
('Missing Prerequisites', 'prerequisite', 'schedule', '{"prerequisites_not_met": true}', 'medium', '{"check_student_eligibility": true}', true, 4);

-- Insert notification templates
INSERT INTO notification_templates (name, template_type, event_type, subject_template, body_template, template_variables, is_active, created_by) VALUES
('Schedule Conflict Detected', 'email', 'schedule_conflict', 'Conflict Detected in Your Schedule', 'Dear {{user_name}},\n\nA conflict has been detected in your schedule:\n\n{{conflict_details}}\n\nPlease resolve this conflict as soon as possible.\n\nThank you,\nAcademic Scheduling Team', '{"user_name": "User Name", "conflict_details": "Conflict Description"}', true, 1),
('SK Approval Required', 'email', 'sk_approval_required', 'SK Approval Required', 'Dear {{approver_name}},\n\nA new SK application requires your approval:\n\nApplication: {{sk_number}}\nLecturer: {{lecturer_name}}\nSubject: {{subject_name}}\n\nPlease review and approve or reject this application.\n\nThank you,\nAcademic Scheduling Team', '{"approver_name": "Approver Name", "sk_number": "SK Number", "lecturer_name": "Lecturer Name", "subject_name": "Subject Name"}', true, 1),
('Grade Published', 'email', 'grade_published', 'Grades Published for {{subject_name}}', 'Dear {{student_name}},\n\nYour grades for {{subject_name}} have been published.\n\nGrade: {{grade}}\nScore: {{score}}\n\nYou can view detailed results in the student portal.\n\nThank you,\nAcademic Team', '{"student_name": "Student Name", "subject_name": "Subject Name", "grade": "Grade", "score": "Score"}', true, 1);

-- Create indexes for performance optimization
CREATE INDEX idx_schedules_composite ON schedules(semester_id, day_of_week, start_time);
CREATE INDEX idx_attendances_composite ON attendances(student_id, schedule_instance_id, created_at);
CREATE INDEX idx_student_grades_composite ON student_grades(assessment_id, student_id, graded_at);
CREATE INDEX idx_grade_books_composite ON grade_books(subject_id, student_id, semester_id);
CREATE INDEX idx_schedule_instances_composite ON schedule_instances(schedule_id, date, status);
CREATE INDEX idx_conflict_analytics_date ON conflict_analytics(analysis_date);

-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- Database setup complete
SELECT 'ACADEMIC SCHEDULING SYSTEM DATABASE SETUP COMPLETED' as status;