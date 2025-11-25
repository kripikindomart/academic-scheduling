# Testing Guide - Academic Scheduling System API

## Quick Start

1. **Start Server**
   ```bash
   cd academic-scheduling-system
   php artisan serve
   ```
   Server akan berjalan di `http://127.0.0.1:8000`

2. **Database Seeder** (jika belum dijalankan)
   ```bash
   php artisan db:seed --class=DatabaseSeeder
   ```

## Testing with Postman

### 1. Import Environment Variables
Buat file environment variables di Postman:

```
base_url = http://127.0.0.1:8000
token = {{login_response.token}}
```

### 2. Testing Authentication

#### Register New User
- **Method:** POST
- **URL:** `{{base_url}}/auth/register`
- **Body (raw JSON):**
```json
{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login (Get Token)
- **Method:** POST
- **URL:** `{{base_url}}/auth/login`
- **Body (raw JSON):**
```json
{
    "email": "admin@jadwal-app.com",
    "password": "password"
}
```
- **Tests:** (untuk menyimpan token)
```javascript
if (pm.response.code === 200) {
    const response = pm.response.json();
    pm.collectionVariables.set('token', response.token);
}
```

#### Get Current User
- **Method:** GET
- **URL:** `{{base_url}}/auth/user`
- **Headers:** `Authorization: Bearer {{token}}`

#### Logout
- **Method:** POST
- **URL:** `{{base_url}}/auth/logout`
- **Headers:** `Authorization: Bearer {{token}}`

### 3. Testing User Management

#### Get All Users
- **Method:** GET
- **URL:** `{{base_url}}/users`
- **Headers:** `Authorization: Bearer {{token}}`

#### Create New User
- **Method:** POST
- **URL:** `{{base_url}}/users`
- **Headers:** `Authorization: Bearer {{token}}`
- **Body (raw JSON):**
```json
{
    "name": "New Dosen",
    "email": "dosen.baru@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "roles": ["Dosen"]
}
```

#### Get Specific User
- **Method:** GET
- **URL:** `{{base_url}}/users/1`
- **Headers:** `Authorization: Bearer {{token}}`

#### Update User
- **Method:** PUT
- **URL:** `{{base_url}}/users/1`
- **Headers:** `Authorization: Bearer {{token}}`
- **Body (raw JSON):**
```json
{
    "name": "Updated Name",
    "roles": ["Admin", "Dosen"]
}
```

#### Delete User
- **Method:** DELETE
- **URL:** `{{base_url}}/users/{{user_id}}`
- **Headers:** `Authorization: Bearer {{token}}`

### 4. Get Roles & Permissions
- **Method:** GET
- **URL:** `{{base_url}}/users/roles`
- **Headers:** `Authorization: Bearer {{token}}`

- **Method:** GET
- **URL:** `{{base_url}}/users/permissions`
- **Headers:** `Authorization: Bearer {{token}}`

## Testing with curl Commands

### 1. Login dan dapatkan token
```bash
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@jadwal-app.com",
    "password": "password"
  }' | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

echo "Token: $TOKEN"
```

### 2. Test endpoints dengan token
```bash
# Get current user
curl -X GET http://127.0.0.1:8000/auth/user \
  -H "Authorization: Bearer $TOKEN"

# Get all users
curl -X GET http://127.0.0.1:8000/users \
  -H "Authorization: Bearer $TOKEN"

# Get dashboard
curl -X GET http://127.0.0.1:8000/dashboard \
  -H "Authorization: Bearer $TOKEN"
```

### 3. Create user baru
```bash
curl -X POST http://127.0.0.1:8000/users \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "name": "Test Dosen",
    "email": "dosen.test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "roles": ["Dosen"]
  }'
```

## Testing with JavaScript (Browser Console)

```javascript
// Base URL
const baseUrl = 'http://127.0.0.1:8000';

let authToken = '';

// Login function
async function login(email, password) {
    const response = await fetch(`${baseUrl}/auth/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, password })
    });

    const data = await response.json();
    if (response.ok) {
        authToken = data.token;
        console.log('Login successful, token saved');
        return data;
    } else {
        console.error('Login failed:', data);
    }
}

// Get users function
async function getUsers() {
    const response = await fetch(`${baseUrl}/users`, {
        headers: {
            'Authorization': `Bearer ${authToken}`
        }
    });

    const data = await response.json();
    console.log('Users:', data);
    return data;
}

// Usage example
// login('admin@jadwal-app.com', 'password').then(() => getUsers());
```

## Common Testing Scenarios

### 1. Test Authentication Flow
1. Register user baru
2. Login dengan user tersebut
3. Get current user info
4. Refresh token
5. Logout

### 2. Test User Management
1. Login sebagai admin
2. Get semua users
3. Create user baru
4. Update user
5. Delete user

### 3. Test Authorization
1. Login sebagai student
2. Coba akses GET /users (seharusnya forbidden)
3. Login sebagai admin
4. Akses GET /users (seharusnya berhasil)

### 4. Test Validation
1. Register dengan email yang sudah ada
2. Login dengan password salah
3. Create user tanpa required fields

## Postman Collection Export

Berikut Postman collection yang bisa diimport:

```json
{
  "info": {
    "name": "Academic Scheduling System API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "variable": [
    {
      "key": "base_url",
      "value": "http://127.0.0.1:8000"
    },
    {
      "key": "token",
      "value": ""
    }
  ],
  "item": [
    {
      "name": "Auth",
      "item": [
        {
          "name": "Login",
          "request": {
            "method": "POST",
            "header": [
              {
                "key": "Content-Type",
                "value": "application/json"
              }
            ],
            "body": {
              "mode": "raw",
              "raw": "{\n    \"email\": \"admin@jadwal-app.com\",\n    \"password\": \"password\"\n}"
            },
            "url": {
              "raw": "{{base_url}}/auth/login",
              "host": ["{{base_url}}"],
              "path": ["auth","login"]
            }
          },
          "event": [
            {
              "listen": "test",
              "script": {
                "exec": [
                  "if (pm.response.code === 200) {",
                  "    const response = pm.response.json();",
                  "    pm.collectionVariables.set('token', response.token);",
                  "}"
                ]
              }
            }
          ]
        }
      ]
    }
  ]
}
```

## Troubleshooting

### 1. Token Not Working
- Pastikan token sudah benar (copy tanpa spasi tambahan)
- Cek format header: `Authorization: Bearer {token}`

### 2. 403 Forbidden
- Login sebagai user yang memiliki permission yang cukup
- Cek role dan permission user di database

### 3. 422 Validation Error
- Periksa request body format
- Pastikan semua required fields terisi

### 4. 500 Server Error
- Check Laravel logs: `storage/logs/laravel.log`
- Pastikan database connection benar
- Run: `php artisan config:cache`

### 5. CORS Issues
- Jika testing dari browser, pastikan API CORS sudah di-setup
- Untuk development, bisa menggunakan browser extension seperti CORS Toggle

## Database Status Check

```bash
# Check database connection
php artisan tinker
> DB::connection()->getPdo();
> \q

# Check migrated tables
php artisan migrate:status

# Re-run seeder if needed
php artisan db:seed --class=DatabaseSeeder
```