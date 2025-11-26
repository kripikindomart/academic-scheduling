# Academic Scheduling System - API Documentation

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Base URL](#base-url)
4. [Common Response Format](#common-response-format)
5. [Error Handling](#error-handling)
6. [API Endpoints](#api-endpoints)
   - [Authentication](#authentication-endpoints)
   - [User Management](#user-management-endpoints)
   - [Course Management](#course-management-endpoints)
   - [Program Study Management](#program-study-management-endpoints)
   - [Student Management](#student-management-endpoints)
   - [Lecturer Management](#lecturer-management-endpoints)
   - [Room Management](#room-management-endpoints)
7. [Data Models](#data-models)
8. [Permissions](#permissions)
9. [Rate Limiting](#rate-limiting)
10. [Examples](#examples)

## Overview

The Academic Scheduling System API provides comprehensive endpoints for managing university course scheduling, student records, lecturer information, and room allocation. This API follows RESTful principles and uses JSON for data exchange.

### Key Features

- 🎓 **Course Management**: Create and manage academic courses
- 📚 **Program Study Management**: Handle study programs and faculties
- 👥 **Student Management**: Complete student information system
- 👨‍🏫 **Lecturer Management**: Faculty and staff management
- 🏢 **Room Management**: Classroom and facility allocation
- 📅 **Scheduling**: Course scheduling with conflict detection
- 📊 **Analytics**: Utilization and performance reporting
- 🔐 **Security**: Role-based access control with permissions
- 📝 **Audit Trail**: Complete logging of all operations

## Authentication

### Getting Started

All API endpoints (except authentication endpoints) require a valid API token. Include the token in the Authorization header:

```http
Authorization: Bearer YOUR_API_TOKEN
```

### Login Endpoint

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@university.edu",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "1|abcdef123456...",
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@university.edu",
      "role": "admin"
    }
  },
  "message": "Login successful"
}
```

## Base URL

```
Production: https://api.university.edu/api
Development: http://127.0.0.1:8000/api
```

## Common Response Format

### Success Response

```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Operation successful",
  "meta": {
    // Pagination data for list endpoints
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### Error Response

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "errors": {
      "field_name": ["Error message for field"]
    }
  }
}
```

## Error Handling

The API uses standard HTTP status codes:

- `200` - Success
- `201` - Created
- `204` - No Content
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## API Endpoints

### Authentication Endpoints

#### Login
```http
POST /api/auth/login
```

#### Logout
```http
POST /api/auth/logout
```

#### Register
```http
POST /api/auth/register
```

#### Get User Info
```http
GET /api/auth/user
```

#### Refresh Token
```http
POST /api/auth/refresh
```

### User Management Endpoints

#### List Users
```http
GET /api/users?search=john&role=lecturer&per_page=20
```

#### Create User
```http
POST /api/users
```

#### Get User Details
```http
GET /api/users/{user}
```

#### Update User
```http
PUT /api/users/{user}
```

#### Delete User
```http
DELETE /api/users/{user}
```

### Course Management Endpoints

#### List Courses
```http
GET /api/courses?search=math&is_active=true&per_page=15
```

#### Create Course
```http
POST /api/courses
```

#### Get Course Details
```http
GET /api/courses/{course}
```

#### Update Course
```http
PUT /api/courses/{course}
```

#### Delete Course
```http
DELETE /api/courses/{course}
```

#### Get Available Courses
```http
GET /api/courses/available?semester=ganjil
```

#### Get Course Statistics
```http
GET /api/courses/statistics
```

#### Add Course Prerequisite
```http
POST /api/courses/{course}/prerequisites
```

### Program Study Management Endpoints

#### List Programs
```http
GET /api/program-studies?search=computer&faculty=FST&per_page=15
```

#### Create Program
```http
POST /api/program-studies
```

#### Get Program Details
```http
GET /api/program-studies/{program_study}
```

#### Update Program
```http
PUT /api/program-studies/{program_study}
```

#### Delete Program
```http
DELETE /api/program-studies/{program_study}
```

#### Get Faculties
```http
GET /api/program-studies/faculties
```

#### Get Program Statistics
```http
GET /api/program-studies/statistics
```

#### Assign Lecturer to Program
```http
POST /api/program-studies/{program_study}/lecturers
```

### Student Management Endpoints

#### List Students
```http
GET /api/students?search=ahmad&program_study_id=1&status=active&per_page=15
```

#### Create Student
```http
POST /api/students
```

#### Get Student Details
```http
GET /api/students/{student}
```

#### Update Student
```http
PUT /api/students/{student}
```

#### Delete Student
```http
DELETE /api/students/{student}
```

#### Get Student Statistics
```http
GET /api/students/statistics?program_study_id=1&batch_year=2023
```

#### Get Active Students
```http
GET /api/students/active?search=john
```

#### Get Students by Program Study
```http
GET /api/students/program-study/{programStudyId}?status=active
```

#### Get Students by Batch Year
```http
GET /api/students/batch-year/2023?status=active
```

#### Get Student Academic Progress
```http
GET /api/students/{student}/academic-progress
```

#### Get Student Attendance Summary
```http
GET /api/students/{student}/attendance-summary?semester=1&academic_year=2023
```

### Lecturer Management Endpoints

#### List Lecturers
```http
GET /api/lecturers?search=dr&faculty=FST&employment_type=permanent&per_page=15
```

#### Create Lecturer
```http
POST /api/lecturers
```

#### Get Lecturer Details
```http
GET /api/lecturers/{lecturer}
```

#### Update Lecturer
```http
PUT /api/lecturers/{lecturer}
```

#### Delete Lecturer
```http
DELETE /api/lecturers/{lecturer}
```

#### Get Lecturer Statistics
```http
GET /api/lecturers/statistics?program_study_id=1
```

#### Get Active Lecturers
```http
GET /api/lecturers/active?search=prof
```

#### Get Lecturer Teaching Load
```http
GET /api/lecturers/{lecturer}/teaching-load
```

#### Get Available Lecturers for Course
```http
GET /api/lecturers/available-for-course/{course}
```

#### Assign Course to Lecturer
```http
POST /api/lecturers/{lecturer}/assign-course/{course}
```

### Room Management Endpoints

#### List Rooms
```http
GET /api/rooms?search=lab&building=A&room_type=laboratory&per_page=15
```

#### Create Room
```http
POST /api/rooms
```

#### Get Room Details
```http
GET /api/rooms/{room}
```

#### Update Room
```http
PUT /api/rooms/{room}
```

#### Delete Room
```http
DELETE /api/rooms/{room}
```

#### Get Room Statistics
```http
GET /api/rooms/statistics
```

#### Get Available Rooms for Schedule
```http
GET /api/rooms/available-for-schedule?date=2024-01-15&start_time=08:00&end_time=10:00&capacity=50&room_type=classroom
```

#### Get Available Rooms
```http
GET /api/rooms/available?search=computer
```

#### Get Room Schedule
```http
GET /api/rooms/{room}/schedule?period=week
```

#### Get Rooms by Building
```http
GET /api/rooms/building/A
```

#### Get Rooms by Type
```http
GET /api/rooms/type/laboratory
```

#### Get Rooms Needing Maintenance
```http
GET /api/rooms/needing-maintenance
```

#### Update Room Availability
```http
PUT /api/rooms/{room}/availability
```

#### Schedule Room Maintenance
```http
POST /api/rooms/{room}/schedule-maintenance
```

### Schedule Management Endpoints

#### List Schedules
```http
GET /api/schedules?search=math&course_id=1&lecturer_id=1&per_page=15
```

#### Create Schedule
```http
POST /api/schedules
```

#### Get Schedule Details
```http
GET /api/schedules/{schedule}
```

#### Update Schedule
```http
PUT /api/schedules/{schedule}
```

#### Delete Schedule
```http
DELETE /api/schedules/{schedule}
```

#### Get Schedule Statistics
```http
GET /api/schedules/statistics
```

#### Check Schedule Conflicts
```http
POST /api/schedules/check-conflicts
```

#### Get Available Rooms for Scheduling
```http
GET /api/schedules/available-rooms?date=2024-01-15&start_time=08:00&end_time=10:00&min_capacity=50
```

#### Get Available Lecturers for Scheduling
```http
GET /api/schedules/available-lecturers?date=2024-01-15&start_time=08:00&end_time=10:00&program_study_id=1
```

#### Get Schedules by Date Range
```http
GET /api/schedules/date-range?start_date=2024-01-01&end_date=2024-01-31&course_id=1
```

#### Get Calendar View
```http
GET /api/schedules/calendar?year=2024&month=1
```

#### Get Schedules by Course
```http
GET /api/schedules/course/{courseId}?status=approved
```

#### Get Schedules by Lecturer
```http
GET /api/schedules/lecturer/{lecturerId}?semester=ganjil
```

#### Get Schedules by Room
```http
GET /api/schedules/room/{roomId}?date_from=2024-01-01&date_to=2024-01-31
```

#### Workflow Operations
```http
POST /api/schedules/{schedule}/approve
POST /api/schedules/{schedule}/reject
POST /api/schedules/{schedule}/cancel
```

#### Bulk Operations
```http
POST /api/schedules/bulk-update
POST /api/schedules/bulk-delete
GET /api/schedules/export?format=csv
POST /api/schedules/import
```

## Data Models

### User Model
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@university.edu",
  "role": "lecturer",
  "is_active": true,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Course Model
```json
{
  "id": 1,
  "course_code": "MATH101",
  "course_name": "Calculus I",
  "description": "Introduction to calculus",
  "credits": 3,
  "semester": "ganjil",
  "academic_year": "2023/2024",
  "course_type": "mandatory",
  "level": "undergraduate",
  "capacity": 50,
  "current_enrollment": 35,
  "is_active": true,
  "program_study_id": 1,
  "prerequisites": [],
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Program Study Model
```json
{
  "id": 1,
  "code": "TI",
  "name": "Teknik Informatika",
  "faculty": "Fakultas Sains dan Teknologi",
  "description": "Computer Science Program",
  "duration_years": 4,
  "degree": "S1",
  "head_of_program": "Prof. Dr. Computer Science",
  "accreditation": "A",
  "is_active": true,
  "lecturers_count": 15,
  "students_count": 250,
  "courses_count": 45,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Student Model
```json
{
  "id": 1,
  "student_number": "2023101001",
  "name": "Ahmad Rizki",
  "email": "ahmad.rizki@student.university.edu",
  "phone": "+62812345678",
  "gender": "male",
  "birth_date": "1998-05-15",
  "birth_place": "Jakarta",
  "address": "Jl. Student No. 123",
  "city": "Jakarta",
  "province": "DKI Jakarta",
  "postal_code": "12345",
  "status": "active",
  "current_semester": 3,
  "current_year": 2,
  "gpa": 3.75,
  "class": "3A",
  "batch_year": "2023",
  "program_study_id": 1,
  "enrollment_date": "2023-09-01",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Lecturer Model
```json
{
  "id": 1,
  "employee_number": "D001",
  "name": "Dr. Computer Science",
  "email": "lecturer@university.edu",
  "phone": "+628987654321",
  "gender": "male",
  "birth_date": "1975-03-10",
  "position": "Professor",
  "rank": "Professor",
  "specialization": ["Artificial Intelligence", "Machine Learning"],
  "department": "Computer Science",
  "faculty": "FST",
  "employment_type": "permanent",
  "hire_date": "2005-08-01",
  "academic_load": 12,
  "is_active": true,
  "program_study_id": 1,
  "current_courses": 3,
  "total_courses": 15,
  "service_years": 18,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

### Room Model
```json
{
  "id": 1,
  "room_code": "A101",
  "name": "Computer Lab 1",
  "building": "A",
  "floor": 1,
  "room_type": "laboratory",
  "capacity": 30,
  "current_occupancy": 25,
  "area": 60.50,
  "department": "Computer Science",
  "faculty": "FST",
  "availability_status": "available",
  "is_active": true,
  "facilities": ["projector", "whiteboard", "computer", "wifi", "ac"],
  "equipment": ["30 computers", "multimedia projector"],
  "maintenance_status": "good",
  "last_maintenance_date": "2023-12-01",
  "next_maintenance_date": "2024-03-01",
  "utilization_rate": 75.5,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

## Permissions

The API uses role-based access control (RBAC). Common permissions include:

### User Permissions
- `users.view` - View users
- `users.create` - Create users
- `users.edit` - Edit users
- `users.delete` - Delete users

### Course Permissions
- `courses.view` - View courses
- `courses.create` - Create courses
- `courses.edit` - Edit courses
- `courses.delete` - Delete courses

### Program Study Permissions
- `program_studies.view` - View programs
- `program_studies.create` - Create programs
- `program_studies.edit` - Edit programs
- `program_studies.delete` - Delete programs

### Student Permissions
- `students.view` - View students
- `students.create` - Create students
- `students.edit` - Edit students
- `students.delete` - Delete students

### Lecturer Permissions
- `lecturers.view` - View lecturers
- `lecturers.create` - Create lecturers
- `lecturers.edit` - Edit lecturers
- `lecturers.delete` - Delete lecturers

### Room Permissions
- `rooms.view` - View rooms
- `rooms.create` - Create rooms
- `rooms.edit` - Edit rooms
- `rooms.delete` - Delete rooms

## Rate Limiting

- API requests are limited to **60 requests per minute** per user
- Authentication endpoints have a limit of **5 requests per minute**
- File upload endpoints have a limit of **10 requests per minute**
- Exceeding limits will result in a `429 Too Many Requests` response

## Examples

### 1. Complete Authentication Flow

```bash
# Login
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@university.edu",
    "password": "password"
  }'

# Store the token for subsequent requests
TOKEN="1|abcdef123456..."
```

### 2. Create a New Student

```bash
curl -X POST http://127.0.0.1:8000/api/students \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "student_number": "2023101001",
    "name": "John Doe",
    "email": "john@student.university.edu",
    "phone": "+62812345678",
    "gender": "male",
    "birth_date": "1998-05-15",
    "birth_place": "Jakarta",
    "address": "Jl. Address 123",
    "city": "Jakarta",
    "province": "DKI Jakarta",
    "postal_code": "12345",
    "program_study_id": 1,
    "class": "1A",
    "batch_year": "2023",
    "enrollment_date": "2023-09-01"
  }'
```

### 3. Get Available Rooms for Scheduling

```bash
curl -X GET "http://127.0.0.1:8000/api/rooms/available-for-schedule?date=2024-01-15&start_time=08:00&end_time=10:00&capacity=50&room_type=classroom" \
  -H "Authorization: Bearer $TOKEN"
```

### 4. Bulk Update Student Status

```bash
curl -X POST http://127.0.0.1:8000/api/students/bulk-update \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "student_ids": [1, 2, 3, 4, 5],
    "updates": {
      "status": "active",
      "current_semester": 2
    }
  }'
```

### 5. Get Comprehensive Statistics

```bash
curl -X GET http://127.0.0.1:8000/api/dashboard \
  -H "Authorization: Bearer $TOKEN"
```

## Testing

### Postman Collection

A Postman collection with all API endpoints is available for testing. Import the collection and set the following environment variables:

- `BASE_URL`: http://127.0.0.1:8000/api
- `EMAIL`: your_email@university.edu
- `PASSWORD`: your_password

### Example Test Script

```javascript
// Example test using JavaScript Fetch API
const BASE_URL = 'http://127.0.0.1:8000/api';

async function testAPI() {
  try {
    // Login
    const loginResponse = await fetch(`${BASE_URL}/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: 'admin@university.edu',
        password: 'password'
      })
    });

    const loginData = await loginResponse.json();
    const token = loginData.data.token;

    console.log('Login successful!');

    // Get all students
    const studentsResponse = await fetch(`${BASE_URL}/students`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });

    const studentsData = await studentsResponse.json();
    console.log(`Found ${studentsData.meta.total} students`);

    // Create new course
    const courseResponse = await fetch(`${BASE_URL}/courses`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        course_code: 'TEST101',
        course_name: 'Test Course',
        credits: 3,
        semester: 'ganjil',
        academic_year: '2023/2024',
        course_type: 'mandatory',
        level: 'undergraduate',
        capacity: 30,
        program_study_id: 1
      })
    });

    console.log('Course created successfully!');

  } catch (error) {
    console.error('API Test Error:', error);
  }
}

// Run the test
testAPI();
```

## Support

For API support and questions:

- 📧 **Email**: api-support@university.edu
- 📚 **Documentation**: This file
- 🐛 **Issue Reporting**: GitHub Issues
- 📱 **Slack**: #api-support channel

## Version History

- **v1.0.0** - Initial release with core API endpoints
- **v1.1.0** - Added advanced filtering and search
- **v1.2.0** - Enhanced error handling and validation
- **v1.3.0** - Added bulk operations and import/export features

---

*Last updated: January 2024*