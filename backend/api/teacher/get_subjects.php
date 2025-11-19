<?php
/**
 * Get Subjects API
 * Fetches subjects based on department and semester
 * Method: GET
 * Auth: Required (teacher or admin)
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

$department = isset($_GET['department']) ? trim($_GET['department']) : null;
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : null;

if (!$department || !$semester) {
    sendError('Missing department or semester', 'validation_error', 400);
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, subject_code, subject_name, credit_hours 
              FROM subjects 
              WHERE department = :department AND semester = :semester AND is_active = 1
              ORDER BY subject_name ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':semester', $semester);
    $stmt->execute();
    
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    sendSuccess($subjects);
    
} catch (PDOException $e) {
    logError('Database error: ' . $e->getMessage());
    sendError('Database error', 'database_error', 500);
}
