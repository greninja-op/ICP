<?php
/**
 * Endpoint Testing Script
 * Run this from browser: http://localhost/university_portal/test_endpoints.php
 */

// Set headers
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal API Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .test-section { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f9f9f9; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .status { font-weight: bold; padding: 5px 10px; border-radius: 4px; display: inline-block; }
        .status.ok { background: #d4edda; color: #155724; }
        .status.fail { background: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🔍 Student Portal API Test</h1>
    
<?php

// Include database config
require_once __DIR__ . '/backend/config/database.php';

echo '<div class="test-section">';
echo '<h2>1. Database Connection Test</h2>';
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo '<p><span class="status ok">✓ SUCCESS</span> Database connection successful</p>';
    } else {
        echo '<p><span class="status fail">✗ FAILED</span> Failed to connect to database</p>';
        echo '</div></body></html>';
        exit;
    }
} catch (Exception $e) {
    echo '<p><span class="status fail">✗ ERROR</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div></body></html>';
    exit;
}
echo '</div>';

// Test Tables
$tables = ['users', 'students', 'teachers', 'subjects', 'notices', 'marks', 'attendance', 'fees', 'payments'];

echo '<div class="test-section">';
echo '<h2>2. Database Tables Check</h2>';
echo '<table>';
echo '<tr><th>Table</th><th>Record Count</th><th>Status</th></tr>';

foreach ($tables as $table) {
    try {
        $query = "SELECT COUNT(*) as total FROM $table";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch();
        $count = $row['total'];
        
        $status = $count > 0 ? '<span class="status ok">✓ Has Data</span>' : '<span class="status fail">⚠ Empty</span>';
        echo "<tr><td>$table</td><td>$count</td><td>$status</td></tr>";
    } catch (Exception $e) {
        echo "<tr><td>$table</td><td colspan='2'><span class='status fail'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</span></td></tr>";
    }
}
echo '</table>';
echo '</div>';

// Test Notices in Detail
echo '<div class="test-section">';
echo '<h2>3. Notices Data Detail</h2>';
try {
    $query = "SELECT id, title, content, target_role, is_active, expiry_date, created_at FROM notices ORDER BY created_at DESC LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($notices) > 0) {
        echo '<p><span class="status ok">✓ Found ' . count($notices) . ' notices</span></p>';
        echo '<table>';
        echo '<tr><th>ID</th><th>Title</th><th>Target Role</th><th>Active</th><th>Expiry</th><th>Created</th></tr>';
        foreach ($notices as $notice) {
            $active = $notice['is_active'] ? '✓' : '✗';
            echo "<tr>";
            echo "<td>{$notice['id']}</td>";
            echo "<td>" . htmlspecialchars($notice['title']) . "</td>";
            echo "<td>{$notice['target_role']}</td>";
            echo "<td>$active</td>";
            echo "<td>" . ($notice['expiry_date'] ?? 'None') . "</td>";
            echo "<td>{$notice['created_at']}</td>";
            echo "</tr>";
        }
        echo '</table>';
        
        // Show first notice content
        echo '<h3>Sample Notice Content:</h3>';
        echo '<pre>' . htmlspecialchars($notices[0]['content']) . '</pre>';
    } else {
        echo '<p><span class="status fail">⚠ No notices found in database</span></p>';
    }
} catch (Exception $e) {
    echo '<p><span class="status fail">✗ ERROR</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
}
echo '</div>';

// Test Subject Distribution
echo '<div class="test-section">';
echo '<h2>4. Subject Distribution (5 per dept check)</h2>';
try {
    $query = "SELECT department, semester, COUNT(*) as subject_count 
              FROM subjects 
              GROUP BY department, semester 
              ORDER BY department, semester";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($distribution) > 0) {
        echo '<table>';
        echo '<tr><th>Department</th><th>Semester</th><th>Subject Count</th><th>Status</th></tr>';
        foreach ($distribution as $row) {
            $count = $row['subject_count'];
            $status = ($count >= 5 && $count <= 6) 
                ? '<span class="success">✓ Good (5-6)</span>' 
                : '<span class="warning">⚠ ' . $count . ' subjects</span>';
            echo "<tr>";
            echo "<td>{$row['department']}</td>";
            echo "<td>{$row['semester']}</td>";
            echo "<td>{$count}</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }
        echo '</table>';
    } else {
        echo '<p><span class="status fail">⚠ No subjects found</span></p>';
    }
} catch (Exception $e) {
    echo '<p><span class="status fail">✗ ERROR</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
}
echo '</div>';

// Test API URL Configuration
echo '<div class="test-section">';
echo '<h2>5. API Configuration Check</h2>';

$base_url_expected = 'http://localhost/university_portal/backend/api';
echo '<p><strong>Expected API Base URL:</strong> <code>' . htmlspecialchars($base_url_expected) . '</code></p>';

$test_endpoints = [
    '/backend/api/auth/login.php',
    '/backend/api/student/get_profile.php',
    '/backend/api/notices/get_all.php',
    '/backend/api/student/get_marks.php',
    '/backend/api/student/get_attendance.php'
];

echo '<table>';
echo '<tr><th>Endpoint</th><th>File Exists</th><th>File Path</th></tr>';
foreach ($test_endpoints as $endpoint) {
    $relative_path = str_replace('/backend/api/', '', $endpoint);
    $full_path = __DIR__ . '/backend/api/' . $relative_path;
    $exists = file_exists($full_path);
    $status = $exists ? '<span class="success">✓ Found</span>' : '<span class="error">✗ Missing</span>';
    echo "<tr>";
    echo "<td>" . htmlspecialchars($endpoint) . "</td>";
    echo "<td>$status</td>";
    echo "<td><small>" . htmlspecialchars($full_path) . "</small></td>";
    echo "</tr>";
}
echo '</table>';
echo '</div>';

// Test Student Data Sample
echo '<div class="test-section">';
echo '<h2>6. Sample Student Data</h2>';
try {
    $query = "SELECT s.student_id, s.first_name, s.last_name, s.department, s.semester, u.username 
              FROM students s 
              JOIN users u ON s.user_id = u.id 
              LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($students) > 0) {
        echo '<table>';
        echo '<tr><th>Student ID</th><th>Name</th><th>Username</th><th>Department</th><th>Semester</th></tr>';
        foreach ($students as $student) {
            echo "<tr>";
            echo "<td>{$student['student_id']}</td>";
            echo "<td>{$student['first_name']} {$student['last_name']}</td>";
            echo "<td>{$student['username']}</td>";
            echo "<td>{$student['department']}</td>";
            echo "<td>{$student['semester']}</td>";
            echo "</tr>";
        }
        echo '</table>';
    } else {
        echo '<p><span class="status fail">⚠ No students found</span></p>';
    }
} catch (Exception $e) {
    echo '<p><span class="status fail">✗ ERROR</span> ' . htmlspecialchars($e->getMessage()) . '</p>';
}
echo '</div>';

// Summary
echo '<div class="test-section">';
echo '<h2>📊 Summary & Recommendations</h2>';
echo '<ol>';
echo '<li>Verify XAMPP is running and accessible at <code>http://localhost/university_portal/</code></li>';
echo '<li>Check that the React app API base URL matches: <code>http://localhost/university_portal/backend/api</code></li>';
echo '<li>Ensure all API endpoints exist in the correct directory structure</li>';
echo '<li>Verify database has at least 5-6 subjects per department/semester</li>';
echo '<li>Check that notices table has active notices with proper data structure (including category and priority fields)</li>';
echo '</ol>';
echo '</div>';

?>
</body>
</html>
