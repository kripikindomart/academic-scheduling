<?php

/**
 * Simple API Testing Script
 * Run this file to test all API endpoints
 */

// Base URL
$baseUrl = 'http://127.0.0.1:8000';

// Colors for output
$colors = [
    'success' => "\033[32m",
    'error' => "\033[31m",
    'info' => "\033[34m",
    'warning' => "\033[33m",
    'reset' => "\033[0m"
];

function colorOutput($text, $color = 'info') {
    global $colors;
    return $colors[$color] . $text . $colors['reset'];
}

function makeRequest($method, $url, $data = null, $token = null) {
    global $baseUrl;

    $ch = curl_init($baseUrl . $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    $headers = ['Content-Type: application/json', 'Accept: application/json'];

    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status_code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

echo colorOutput("=== Academic Scheduling System API Test ===\n\n", 'info');

// 1. Test Root Endpoint
echo colorOutput("1. Testing API Root Endpoint\n", 'info');
$result = makeRequest('GET', '/api/');
echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n\n";

// 2. Test Login
echo colorOutput("2. Testing Login\n", 'info');
$loginData = [
    'email' => 'admin@jadwal-app.com',
    'password' => 'password'
];

$result = makeRequest('POST', '/api/auth/login', $loginData);
echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";

if ($result['status_code'] == 200 && isset($result['response']['token'])) {
    $token = $result['response']['token'];
    echo "Token: " . substr($token, 0, 20) . "...\n";
    echo "User: " . $result['response']['user']['name'] . " (" . $result['response']['user']['email'] . ")\n";
} else {
    echo "Error: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
    exit(1);
}
echo "\n";

// 3. Test Get Current User
echo colorOutput("3. Testing Get Current User\n", 'info');
$result = makeRequest('GET', '/api/auth/user', null, $token);
echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";
echo "User: " . $result['response']['user']['name'] . "\n";
echo "Permissions: " . implode(', ', array_slice($result['response']['permissions'], 0, 3)) . "...\n\n";

// 4. Test Get All Users
echo colorOutput("4. Testing Get All Users\n", 'info');
$result = makeRequest('GET', '/api/users', null, $token);
echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";
echo "Total Users: " . $result['response']['pagination']['total'] . "\n";
echo "Page: " . $result['response']['pagination']['current_page'] . " of " . $result['response']['pagination']['last_page'] . "\n\n";

// 5. Test Get Roles
echo colorOutput("5. Testing Get Roles\n", 'info');
$result = makeRequest('GET', '/api/users/roles', null, $token);
echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";
echo "Available Roles:\n";
foreach ($result['response']['roles'] as $role) {
    echo "  - " . $role['name'] . "\n";
}
echo "\n";

// 6. Test Get Permissions
echo colorOutput("6. Testing Get Permissions\n", 'info');
$result = makeRequest('GET', '/api/users/permissions', null, $token);
echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";
echo "Total Permissions: " . count($result['response']['permissions']) . "\n\n";

// 7. Test Create New User
echo colorOutput("7. Testing Create New User\n", 'info');
$newUserData = [
    'name' => 'Test Dosen',
    'email' => 'dosen.test' . time() . '@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'roles' => ['Dosen']
];

$result = makeRequest('POST', '/api/users', $newUserData, $token);
echo "Status: " . ($result['status_code'] == 201 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";

if ($result['status_code'] == 201) {
    $newUserId = $result['response']['user']['id'];
    echo "New User ID: " . $newUserId . "\n";
    echo "New User: " . $result['response']['user']['name'] . " (" . $result['response']['user']['email'] . ")\n";

    // 8. Test Get Specific User
    echo colorOutput("\n8. Testing Get Specific User\n", 'info');
    $result = makeRequest('GET', '/api/users/' . $newUserId, null, $token);
    echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";

    // 9. Test Update User
    echo colorOutput("\n9. Testing Update User\n", 'info');
    $updateData = [
        'name' => 'Updated Test Dosen',
        'roles' => ['Dosen', 'Staff']
    ];
    $result = makeRequest('PUT', '/api/users/' . $newUserId, $updateData, $token);
    echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";

    // 10. Test Delete User
    echo colorOutput("\n10. Testing Delete User\n", 'info');
    $result = makeRequest('DELETE', '/api/users/' . $newUserId, null, $token);
    echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";
}

// 11. Test Dashboard
echo colorOutput("\n11. Testing Dashboard\n", 'info');
$result = makeRequest('GET', '/api/dashboard', null, $token);
echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";
echo "Message: " . $result['response']['message'] . "\n\n";

// 12. Test Refresh Token
echo colorOutput("12. Testing Refresh Token\n", 'info');
$result = makeRequest('POST', '/api/auth/refresh', null, $token);
echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";

if ($result['status_code'] == 200) {
    $newToken = $result['response']['token'];
    echo "New Token Generated\n";

    // 13. Test Logout
    echo colorOutput("\n13. Testing Logout\n", 'info');
    $result = makeRequest('POST', '/api/auth/logout', null, $newToken);
    echo "Status: " . ($result['status_code'] == 200 ? colorOutput("✓", 'success') : colorOutput("✗", 'error')) . " ({$result['status_code']})\n";
}

echo colorOutput("\n=== API Test Complete ===\n", 'success');

echo colorOutput("\nAvailable Routes:\n", 'warning');
echo "• GET  /api/                 - API Info\n";
echo "• POST /api/auth/login       - Login\n";
echo "• POST /api/auth/register    - Register\n";
echo "• GET  /api/auth/user        - Get Current User\n";
echo "• POST /api/auth/logout      - Logout\n";
echo "• POST /api/auth/refresh     - Refresh Token\n";
echo "• GET  /api/dashboard        - Dashboard\n";
echo "• GET  /api/users            - Get All Users\n";
echo "• POST /api/users            - Create User\n";
echo "• GET  /api/users/{id}       - Get User\n";
echo "• PUT  /api/users/{id}       - Update User\n";
echo "• DELETE /api/users/{id}     - Delete User\n";
echo "• GET  /api/users/roles      - Get Roles\n";
echo "• GET  /api/users/permissions - Get Permissions\n";

echo colorOutput("\nDefault Users:\n", 'warning');
echo "• admin@jadwal-app.com / password (Super Admin)\n";
echo "• admin2@jadwal-app.com / password (Admin)\n";
echo "• kaprodi@jadwal-app.com / password (Kaprodi)\n";
echo "• dosen@jadwal-app.com / password (Dosen)\n";
echo "• staff@jadwal-app.com / password (Staff)\n";
echo "• student@jadwal-app.com / password (Student)\n";

?>