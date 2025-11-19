<?php
/**
 * Get Assigned Subjects API
 * Returns list of subjects assigned to the logged-in teacher
 * Method: GET
 * Auth: Required (teacher role)
 */

require_once '../../config/database.php';
require_once '../../includes/cors.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Verify authentication
$user = verifyAuth();
if (!$user) {
    sendError('Unauthorized - Invalid or missing token', 'unauthorized', 401);
}

// Check role
if ($user['role'] !== 'teacher') {
    sendError('Forbidden - This endpoint is only accessible to teachers', 'forbidden', 403);
}

try {
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Get teacher ID
    $teacherId = getTeacherIdFromUserId($user['user_id'], $db);
    if (!$teacherId) {
        sendError('Teacher profile not found', 'not_found', 404);
    }
    
    // Query assigned subjects
    $query = "SELECT 
                s.id,
                s.subject_code,
                s.subject_name,
                s.credit_hours,
                s.semester,
                s.department,
                ts.assigned_at
              FROM subjects s
              JOIN teacher_subjects ts ON s.id = ts.subject_id
              WHERE ts.teacher_id = :teacher_id
              ORDER BY s.semester, s.subject_code";
              
    $stmt = $db->prepare($query);
    $stmt->bindParam(':teacher_id', $teacherId, PDO::PARAM_INT);
    $stmt->execute();
    
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format response
    $response = [
        'count' => count($subjects),
        'subjects' => $subjects
    ];
    
    sendSuccess($response);
    
} catch (PDOException $e) {
    logError('Database error in get_assigned_subjects.php: ' . $e->getMessage(), [
        'user_id' => $user['user_id']
    ]);
    sendError('An error occurred while fetching assigned subjects', 'database_error', 500);
} catch (Exception $e) {
    sendError('An unexpected error occurred', 'server_error', 500);
}
