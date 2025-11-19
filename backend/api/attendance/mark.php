<?php
/**
 * Mark Attendance API
 * Records attendance for multiple students
 * Method: POST
 * Auth: Required (teacher or admin)
 */

require_once '../../config/database.php';
require_once '../../includes/cors.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';
require_once '../../includes/validation.php';

// Verify authentication
$user = verifyAuth();
if (!$user) {
    sendError('Unauthorized', 'unauthorized', 401);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    sendError('Invalid JSON data', 'invalid_json', 400);
}

// Validate required fields
$required = ['subject_id', 'date', 'attendance_data'];
$missing = validateRequired($required, $data);

if (!empty($missing)) {
    sendError('Missing required fields: ' . implode(', ', $missing), 'validation_error', 400);
}

$subjectId = (int)$data['subject_id'];
$date = trim($data['date']);
$attendanceData = $data['attendance_data']; // Array of {student_id, status}

// Get active session
try {
    $database = new Database();
    $db = $database->getConnection();
    
    $session = getActiveSession($db);
    if (!$session) {
        sendError('No active academic session found', 'no_active_session', 404);
    }
    $sessionId = $session['id'];
    
    // Start transaction
    $db->beginTransaction();
    
    $query = "INSERT INTO attendance (student_id, subject_id, session_id, attendance_date, status, marked_by) 
              VALUES (:student_id, :subject_id, :session_id, :attendance_date, :status, :marked_by)
              ON DUPLICATE KEY UPDATE status = :status_update, marked_by = :marked_by_update, marked_at = CURRENT_TIMESTAMP";
    
    $stmt = $db->prepare($query);
    
    foreach ($attendanceData as $record) {
        if (!isset($record['student_id']) || !isset($record['status'])) {
            continue;
        }
        
        $studentId = (int)$record['student_id'];
        $status = strtolower(trim($record['status']));
        
        // Validate status
        if (!in_array($status, ['present', 'absent', 'late', 'excused'])) {
            $status = 'present';
        }
        
        $stmt->bindParam(':student_id', $studentId);
        $stmt->bindParam(':subject_id', $subjectId);
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->bindParam(':attendance_date', $date);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':marked_by', $user['user_id']);
        
        // For ON DUPLICATE KEY UPDATE
        $stmt->bindParam(':status_update', $status);
        $stmt->bindParam(':marked_by_update', $user['user_id']);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to mark attendance for student ID: ' . $studentId);
        }
    }
    
    $db->commit();
    sendSuccess(['message' => 'Attendance marked successfully'], 200);
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    logError('Error marking attendance: ' . $e->getMessage());
    sendError('Server error', 'server_error', 500);
}
