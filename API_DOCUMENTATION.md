# API Documentation - Academic Scheduling System

## Base URL
```
http://127.0.0.1:8000
```

## Authentication Routes

### 1. Register
**POST** `/auth/register`

Mendaftarkan user baru dengan role default "Student".

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
    "message": "Registration successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "avatar": null,
        "email_verified_at": "2024-01-01T00:00:00.000000Z",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z",
        "roles": [
            {
                "id": 6,
                "name": "Student",
                "guard_name": "web"
            }
        ]
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
}
```

### 2. Login
**POST** `/auth/login`

Login untuk mendapatkan token.

**Request Body:**
```json
{
    "email": "admin@jadwal-app.com",
    "password": "password"
}
```

**Response (200):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@jadwal-app.com",
        "roles": [
            {
                "id": 1,
                "name": "Super Admin",
                "guard_name": "web"
            }
        ],
        "permissions": [...]
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
}
```

### 3. Get Current User
**GET** `/auth/user`

Mendapatkan data user yang sedang login.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@jadwal-app.com",
        "roles": [...],
        "permissions": [...]
    },
    "permissions": ["users.view", "users.create", ...]
}
```

### 4. Logout
**POST** `/auth/logout`

Logout dari sistem.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Successfully logged out"
}
```

### 5. Refresh Token
**POST** `/auth/refresh`

Refresh token authentication.

**Headers:**
```
Authorization: Bearer {old_token}
```

**Response (200):**
```json
{
    "user": {...},
    "token": "2|xyz789...",
    "token_type": "Bearer"
}
```

## User Management Routes

### 6. Get All Users
**GET** `/users`

Mendapatkan semua user dengan pagination.

**Headers:**
```
Authorization: Bearer {token}
Permission: users.view
```

**Response (200):**
```json
{
    "message": "Users retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Admin User",
            "email": "admin@jadwal-app.com",
            "avatar": null,
            "email_verified_at": "2024-01-01T00:00:00.000000Z",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z",
            "roles": [...]
        }
    ],
    "pagination": {
        "total": 10,
        "per_page": 20,
        "current_page": 1,
        "last_page": 1
    }
}
```

### 7. Create User
**POST** `/users`

Membuat user baru.

**Headers:**
```
Authorization: Bearer {token}
Permission: users.create
```

**Request Body:**
```json
{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "roles": ["Dosen"],
    "avatar": "https://example.com/avatar.jpg"
}
```

**Response (201):**
```json
{
    "message": "User created successfully",
    "user": {
        "id": 11,
        "name": "New User",
        "email": "newuser@example.com",
        "roles": [
            {
                "id": 4,
                "name": "Dosen",
                "guard_name": "web"
            }
        ]
    }
}
```

### 8. Get User by ID
**GET** `/users/{id}`

Mendapatkan user berdasarkan ID.

**Headers:**
```
Authorization: Bearer {token}
Permission: users.view
```

**Response (200):**
```json
{
    "message": "User retrieved successfully",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@jadwal-app.com",
        "roles": [...],
        "permissions": [...]
    }
}
```

### 9. Update User
**PUT** `/users/{id}`

Update data user.

**Headers:**
```
Authorization: Bearer {token}
Permission: users.edit
```

**Request Body:**
```json
{
    "name": "Updated Name",
    "email": "updated@example.com",
    "roles": ["Admin"],
    "avatar": "https://example.com/new-avatar.jpg"
}
```

**Response (200):**
```json
{
    "message": "User updated successfully",
    "user": {
        "id": 1,
        "name": "Updated Name",
        "email": "updated@example.com",
        "roles": [...]
    }
}
```

### 10. Delete User
**DELETE** `/users/{id}`

Menghapus user.

**Headers:**
```
Authorization: Bearer {token}
Permission: users.delete
```

**Response (200):**
```json
{
    "message": "User deleted successfully"
}
```

## Utility Routes

### 11. Get All Roles
**GET** `/users/roles`

Mendapatkan semua roles yang tersedia.

**Headers:**
```
Authorization: Bearer {token}
Permission: users.view
```

**Response (200):**
```json
{
    "message": "Roles retrieved successfully",
    "roles": [
        {
            "id": 1,
            "name": "Super Admin",
            "guard_name": "web"
        },
        {
            "id": 2,
            "name": "Admin",
            "guard_name": "web"
        },
        ...
    ]
}
```

### 12. Get All Permissions
**GET** `/users/permissions`

Mendapatkan semua permissions yang tersedia.

**Headers:**
```
Authorization: Bearer {token}
Permission: users.view
```

**Response (200):**
```json
{
    "message": "Permissions retrieved successfully",
    "permissions": [
        {
            "id": 1,
            "name": "users.view",
            "guard_name": "web"
        },
        {
            "id": 2,
            "name": "users.create",
            "guard_name": "web"
        },
        ...
    ]
}
```

## Dashboard & Info Routes

### 13. Dashboard
**GET** `/dashboard`

Dashboard endpoint.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Welcome to Academic Scheduling System Dashboard",
    "version": "1.0.0",
    "status": "success"
}
```

### 14. Root/Info
**GET** `/`

Informasi API yang tersedia.

**Response (200):**
```json
{
    "message": "Academic Scheduling System API",
    "version": "1.0.0",
    "status": "success",
    "endpoints": {
        "auth": {
            "login": "/auth/login",
            "logout": "/auth/logout",
            "register": "/auth/register",
            "user": "/auth/user",
            "refresh": "/auth/refresh"
        },
        "users": "/users",
        "dashboard": "/dashboard"
    }
}
```

## Default Users

Berikut adalah default users yang tersedia setelah database seeding:

### Super Admin
- **Email:** admin@jadwal-app.com
- **Password:** password
- **Permissions:** Semua permissions

### Admin
- **Email:** admin2@jadwal-app.com
- **Password:** password
- **Permissions:** User management dan view permissions

### Kaprodi
- **Email:** kaprodi@jadwal-app.com
- **Password:** password
- **Permissions:** Scheduling view permissions

### Dosen
- **Email:** dosen@jadwal-app.com
- **Password:** password
- **Permissions:** Basic view permissions

### Staff
- **Email:** staff@jadwal-app.com
- **Password:** password
- **Permissions:** Basic view permissions

### Student
- **Email:** student@jadwal-app.com
- **Password:** password
- **Permissions:** Basic view permissions

## Testing dengan Postman/curl

### Login Example dengan curl:
```bash
curl -X POST http://127.0.0.1:8000/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@jadwal-app.com",
    "password": "password"
  }'
```

### Get Users Example dengan curl:
```bash
curl -X GET http://127.0.0.1:8000/users \
  -H "Authorization: Bearer {token_from_login}"
```

## Error Responses

### Validation Error (422):
```json
{
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Unauthorized (401):
```json
{
    "message": "Invalid credentials"
}
```

### Forbidden (403):
```json
{
    "message": "User does not have the right roles"
}
```

### Not Found (404):
```json
{
    "message": "No query results for model [App\\Models\\User] 999"
}
```

### Server Error (500):
```json
{
    "message": "Failed to create user",
    "error": "Detailed error message"
}
```
