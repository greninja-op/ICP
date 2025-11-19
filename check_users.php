<?php
require_once __DIR__ . '/backend/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Checking users in database...\n\n";
    
    $stmt = $db->query("SELECT id, username, email, role, status FROM users ORDER BY role, username LIMIT 20");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($users) . " users:\n";
    echo str_repeat("=", 80) . "\n";
    printf("%-5s %-20s %-30s %-10s %-10s\n", "ID", "Username", "Email", "Role", "Status");
    echo str_repeat("=", 80) . "\n";
    
    foreach ($users as $user) {
        printf("%-5s %-20s %-30s %-10s %-10s\n", 
            $user['id'], 
            $user['username'], 
            $user['email'], 
            $user['role'], 
            $user['status']
        );
    }
    
    echo "\n\nTest Login Credentials:\n";
    echo str_repeat("=", 80) . "\n";
    echo "Admin: admin / admin123\n";
    echo "Teacher: advik3810 / teacher123\n";
    echo "Student: (check output above)\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
