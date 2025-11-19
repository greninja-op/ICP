<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Connected to database.\n";
    
    $sql = file_get_contents(__DIR__ . '/../database/migrations/01_add_notices_and_notifications.sql');
    
    if (!$sql) {
        die("Error: Could not read migration file.\n");
    }
    
    // Split by semicolon to run multiple queries
    // This is a simple split and might fail on complex SQL with semicolons in strings, 
    // but for this specific migration file it should be fine.
    $queries = explode(';', $sql);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            $db->exec($query);
            echo "Executed query.\n";
        }
    }
    
    echo "Migration completed successfully.\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
