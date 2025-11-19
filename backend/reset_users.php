<?php
require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection failed.\n");
}

echo "Starting user reset process...\n";

// 1. Delete existing users
$usernames = ['admin', 'teacher', 'student'];
$placeholders = implode(',', array_fill(0, count($usernames), '?'));

try {
    $stmt = $db->prepare("DELETE FROM users WHERE username IN ($placeholders)");
    $stmt->execute($usernames);
    echo "Deleted existing users: " . implode(', ', $usernames) . "\n";
} catch (PDOException $e) {
    die("Error deleting users: " . $e->getMessage() . "\n");
}

// 2. Ensure a session exists
$session_id = 1;
try {
    $stmt = $db->query("SELECT id FROM sessions LIMIT 1");
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $session_id = $row['id'];
        echo "Using existing session ID: $session_id\n";
    } else {
        $stmt = $db->prepare("INSERT INTO sessions (session_name, start_year, end_year, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['2025-2026', 2025, 2026, '2025-01-01', '2026-12-31', 1]);
        $session_id = $db->lastInsertId();
        echo "Created new session ID: $session_id\n";
    }
} catch (PDOException $e) {
    die("Error checking/creating session: " . $e->getMessage() . "\n");
}

// Password hash for "123"
$password_hash = password_hash("123", PASSWORD_DEFAULT);

// 3. Create Admin
try {
    // Insert User
    $stmt = $db->prepare("INSERT INTO users (username, password, email, role, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin', $password_hash, 'admin@example.com', 'admin', 'active']);
    $user_id = $db->lastInsertId();

    // Insert Admin Details
    $stmt = $db->prepare("INSERT INTO admins (user_id, admin_id, first_name, last_name, designation) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, 'ADM001', 'System', 'Admin', 'Super Admin']);
    echo "Created Admin user.\n";
} catch (PDOException $e) {
    echo "Error creating Admin: " . $e->getMessage() . "\n";
}

// 4. Create Teacher
try {
    // Insert User
    $stmt = $db->prepare("INSERT INTO users (username, password, email, role, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['teacher', $password_hash, 'teacher@example.com', 'teacher', 'active']);
    $user_id = $db->lastInsertId();

    // Insert Teacher Details
    $stmt = $db->prepare("INSERT INTO teachers (user_id, teacher_id, first_name, last_name, date_of_birth, gender, joining_date, department, designation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, 'TCH001', 'John', 'Doe', '1980-01-01', 'male', '2020-01-01', 'Computer Science', 'Senior Lecturer']);
    echo "Created Teacher user.\n";
} catch (PDOException $e) {
    echo "Error creating Teacher: " . $e->getMessage() . "\n";
}

// 5. Create Student
try {
    // Insert User
    $stmt = $db->prepare("INSERT INTO users (username, password, email, role, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['student', $password_hash, 'student@example.com', 'student', 'active']);
    $user_id = $db->lastInsertId();

    // Insert Student Details
    $stmt = $db->prepare("INSERT INTO students (user_id, student_id, first_name, last_name, date_of_birth, gender, enrollment_date, session_id, semester, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, 'STD001', 'Jane', 'Smith', '2000-01-01', 'female', '2025-01-01', $session_id, 1, 'Computer Science']);
    echo "Created Student user.\n";
} catch (PDOException $e) {
    echo "Error creating Student: " . $e->getMessage() . "\n";
}

echo "User reset complete.\n";
?>
