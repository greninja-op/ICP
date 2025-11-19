<?php
require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getConnection();

$testPasswords = ['123', 'password', 'student123', 'teacher123', 'admin123'];

// Test a few key users
$testUsers = ['admin', 'teacher', 'student', 'BCA Teacher 1', 'BCA Student 1'];

foreach ($testUsers as $username) {
    $query = "SELECT username, password FROM users WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        echo "\n=== Testing: {$user['username']} ===\n";
        foreach ($testPasswords as $pass) {
            if (password_verify($pass, $user['password'])) {
                echo "✅ Password: '$pass' WORKS!\n";
                break;
            }
        }
    }
}
?>
