<?php
require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getConnection();

$password = '123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Generated hash: $hash\n\n";

// Update admin
$query = "UPDATE users SET password = :password WHERE username = 'admin'";
$stmt = $db->prepare($query);
$stmt->bindParam(':password', $hash);
$stmt->execute();
echo "✅ Updated admin password\n";

// Update teacher
$query = "UPDATE users SET password = :password WHERE username = 'teacher'";
$stmt = $db->prepare($query);
$stmt->bindParam(':password', $hash);
$stmt->execute();
echo "✅ Updated teacher password\n";

// Update student
$query = "UPDATE users SET password = :password WHERE username = 'student'";
$stmt = $db->prepare($query);
$stmt->bindParam(':password', $hash);
$stmt->execute();
echo "✅ Updated student password\n";

echo "\nAll passwords updated to: 123\n";
?>
