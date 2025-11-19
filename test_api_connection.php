<?php
/**
 * API Connection and Database Test Script
 * Tests database connectivity and verifies data presence
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Include database config
require_once __DIR__ . '/backend/config/database.php';

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => []
];

// Test 1: Database Connection
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $results['tests']['database_connection'] = [
            'status' => 'SUCCESS',
            'message' => 'Database connection successful'
        ];
    } else {
        $results['tests']['database_connection'] = [
            'status' => 'FAILED',
            'message' => 'Failed to connect to database'
        ];
        echo json_encode($results, JSON_PRETTY_PRINT);
        exit;
    }
} catch (Exception $e) {
    $results['tests']['database_connection'] = [
        'status' => 'ERROR',
        'message' => $e->getMessage()
    ];
    echo json_encode($results, JSON_PRETTY_PRINT);
    exit;
}

// Test 2: Check Students Table
try {
    $query = "SELECT COUNT(*) as total FROM students";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch();
    
    $results['tests']['students_table'] = [
        'status' => 'SUCCESS',
        'total_students' => $row['total']
    ];
    
    // Get sample student data
    $query = "SELECT student_id, username, first_name, last_name, department, semester FROM students LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $students = $stmt->fetchAll();
    $results['tests']['students_table']['sample_data'] = $students;
} catch (Exception $e) {
    $results['tests']['students_table'] = [
        'status' => 'ERROR',
        'message' => $e->getMessage()
    ];
}

// Test 3: Check Notices Table
try {
    $query = "SELECT COUNT(*) as total FROM notices";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch();
    
    $results['tests']['notices_table'] = [
        'status' => 'SUCCESS',
        'total_notices' => $row['total']
    ];
    
    // Get sample notice data
    $query = "SELECT notice_id, title, category, priority, created_at FROM notices ORDER BY created_at DESC LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $notices = $stmt->fetchAll();
    $results['tests']['notices_table']['sample_data'] = $notices;
} catch (Exception $e) {
    $results['tests']['notices_table'] = [
        'status' => 'ERROR',
        'message' => $e->getMessage()
    ];
}

// Test 4: Check Subjects Table
try {
    $query = "SELECT COUNT(*) as total FROM subjects";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch();
    
    $results['tests']['subjects_table'] = [
        'status' => 'SUCCESS',
        'total_subjects' => $row['total']
    ];
    
    // Get subjects per department
    $query = "SELECT department, semester, COUNT(*) as subject_count FROM subjects GROUP BY department, semester ORDER BY department, semester";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dept_subjects = $stmt->fetchAll();
    $results['tests']['subjects_table']['subjects_per_dept_sem'] = $dept_subjects;
} catch (Exception $e) {
    $results['tests']['subjects_table'] = [
        'status' => 'ERROR',
        'message' => $e->getMessage()
    ];
}

// Test 5: Check Marks Table
try {
    $query = "SELECT COUNT(*) as total FROM marks";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch();
    
    $results['tests']['marks_table'] = [
        'status' => 'SUCCESS',
        'total_marks_records' => $row['total']
    ];
} catch (Exception $e) {
    $results['tests']['marks_table'] = [
        'status' => 'ERROR',
        'message' => $e->getMessage()
    ];
}

// Test 6: Check Attendance Table
try {
    $query = "SELECT COUNT(*) as total FROM attendance";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch();
    
    $results['tests']['attendance_table'] = [
        'status' => 'SUCCESS',
        'total_attendance_records' => $row['total']
    ];
} catch (Exception $e) {
    $results['tests']['attendance_table'] = [
        'status' => 'ERROR',
        'message' => $e->getMessage()
    ];
}

// Test 7: API Endpoint Accessibility
$api_endpoints = [
    '/backend/api/auth/login.php',
    '/backend/api/student/get_profile.php',
    '/backend/api/notices/get_all.php'
];

$results['tests']['api_endpoints'] = [];
foreach ($api_endpoints as $endpoint) {
    $full_path = __DIR__ . $endpoint;
    $results['tests']['api_endpoints'][$endpoint] = [
        'exists' => file_exists($full_path),
        'path' => $full_path
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT);
?>
