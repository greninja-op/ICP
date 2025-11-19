<?php
require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getConnection();

$username = 'student';
$password = '123';

$query = "SELECT id, username, password, role FROM users WHERE username = :username";
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->execute();

$user = $stmt->fetch();

if ($user) {
    echo "User found: " . $user['username'] . " (Role: " . $user['role'] . ")\n";
    echo "Password hash: " . $user['password'] . "\n";
    
    if (password_verify($password, $user['password'])) {
        echo "✅ Password verification: SUCCESS\n";
    } else {
        echo "❌ Password verification: FAILED\n";
    }
} else {
    echo "User not found\n";
}
?>
