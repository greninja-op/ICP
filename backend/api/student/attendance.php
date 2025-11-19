<?php
/**
 * Get Student Attendance API
 * Fetches attendance history for the logged-in student
 * Method: GET
 * Auth: Required (student)
 */

require_once '../../config/database.php';
require_once '../../includes/cors.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Verify authentication
$user = verifyAuth();
if (!$user) {
    sendError('Unauthorized', 'unauthorized', 401);
}

// Check role
if ($user['role'] !== 'student') {
    sendError('Forbidden', 'forbidden', 403);
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get student ID from user ID
    $stmt = $db->prepare("SELECT id FROM students WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        sendError('Student profile not found', 'not_found', 404);
    }
    
    $studentId = $student['id'];
    
    // Get query params
    $month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
    $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
    
    // Fetch attendance
    $query = "SELECT a.attendance_date, a.status, s.subject_name, s.subject_code 
              FROM attendance a
              JOIN subjects s ON a.subject_id = s.id
              WHERE a.student_id = :student_id 
              AND MONTH(a.attendance_date) = :month 
              AND YEAR(a.attendance_date) = :year
              ORDER BY a.attendance_date DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':student_id', $studentId);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':year', $year);
    $stmt->execute();
    
    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate stats
    $statsQuery = "SELECT 
                    COUNT(*) as total_classes,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
                    SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_count
                   FROM attendance 
                   WHERE student_id = :student_id";
                   
    $statsStmt = $db->prepare($statsQuery);
    $statsStmt->bindParam(':student_id', $studentId);
    $statsStmt->execute();
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    $response = [
        'history' => $attendance,
        'stats' => $stats
    ];
    
    sendSuccess($response);
    
} catch (PDOException $e) {
    logError('Database error: ' . $e->getMessage());
    sendError('Database error', 'database_error', 500);
}
