<?php
// Config
$baseUrl = 'http://localhost:8000';
$adminUser = 'admin';
$adminPass = '123';

// Helper function for requests
function makeRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $headers = ['Content-Type: application/json'];
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
    
    return ['code' => $httpCode, 'body' => json_decode($response, true)];
}

echo "1. Login as Admin...\n";
$loginRes = makeRequest($baseUrl . '/api/auth/login.php', 'POST', [
    'username' => $adminUser,
    'password' => $adminPass
]);

if ($loginRes['code'] !== 200) {
    die("Admin login failed: " . print_r($loginRes, true));
}

$adminToken = $loginRes['body']['data']['token'];
echo "Admin Token: " . substr($adminToken, 0, 20) . "...\n\n";

// --- Test Student Creation ---
$newStudentUser = 'teststud_' . time();
$newStudentPass = 'password123';
$studentData = [
    'username' => $newStudentUser,
    'password' => $newStudentPass,
    'email' => $newStudentUser . '@example.com',
    'first_name' => 'Test',
    // 'last_name' => 'Student', // Optional now
    'date_of_birth' => '2000-01-01',
    'gender' => 'male',
    // 'phone' => '1234567890', // Optional now
    'enrollment_date' => '2023-01-01',
    'department' => 'Computer Science',
    'program' => 'B.Tech',
    'semester' => 1,
    'batch_year' => 2023,
    'student_id' => 'ST_' . time() // Manual ID
];

echo "2. Creating Student ($newStudentUser)...\n";
$createStudRes = makeRequest($baseUrl . '/api/admin/students/create.php', 'POST', $studentData, $adminToken);

if ($createStudRes['code'] !== 201) {
    echo "Student creation failed:\n";
    print_r($createStudRes);
} else {
    echo "Student created successfully.\n";
    
    echo "3. Testing Student Login...\n";
    $studLoginRes = makeRequest($baseUrl . '/api/auth/login.php', 'POST', [
        'username' => $newStudentUser,
        'password' => $newStudentPass
    ]);
    
    if ($studLoginRes['code'] === 200) {
        echo "✅ Student Login SUCCESS!\n";
    } else {
        echo "❌ Student Login FAILED:\n";
        print_r($studLoginRes);
    }
}
echo "\n";

// --- Test Teacher Creation ---
$newTeacherUser = 'testteach_' . time();
$newTeacherPass = 'password123';
$teacherData = [
    'username' => $newTeacherUser,
    'password' => $newTeacherPass,
    'email' => $newTeacherUser . '@example.com',
    'first_name' => 'Test',
    // 'last_name' => 'Teacher', // Optional
    'date_of_birth' => '1980-01-01',
    'gender' => 'female',
    // 'phone' => '0987654321', // Optional
    'joining_date' => '2020-01-01',
    'department' => 'Physics',
    'designation' => 'Professor',
    'teacher_id' => 'TC_' . time() // Manual ID
];

echo "4. Creating Teacher ($newTeacherUser)...\n";
$createTeachRes = makeRequest($baseUrl . '/api/admin/teachers/create.php', 'POST', $teacherData, $adminToken);

if ($createTeachRes['code'] !== 201) {
    echo "Teacher creation failed:\n";
    print_r($createTeachRes);
} else {
    echo "Teacher created successfully.\n";
    
    echo "5. Testing Teacher Login...\n";
    $teachLoginRes = makeRequest($baseUrl . '/api/auth/login.php', 'POST', [
        'username' => $newTeacherUser,
        'password' => $newTeacherPass
    ]);
    
    if ($teachLoginRes['code'] === 200) {
        echo "✅ Teacher Login SUCCESS!\n";
    } else {
        echo "❌ Teacher Login FAILED:\n";
        print_r($teachLoginRes);
    }
}

?>