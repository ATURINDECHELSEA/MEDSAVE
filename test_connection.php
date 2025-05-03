<?php
// Display all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>MEDSAVE Database Connection Test</h1>";

// Check if database.php exists
if (!file_exists('config/database.php')) {
    die("<h2>❌ Error: config/database.php file not found!</h2>");
}

require_once 'config/database.php';

try {
    // Test database connection
    $conn = getDBConnection();
    echo "<h2>✅ Database Connection Successful!</h2>";
    
    // Display database information
    echo "<h3>Database Information:</h3>";
    echo "<ul>";
    echo "<li>Database Name: " . DB_NAME . "</li>";
    echo "<li>Host: " . DB_HOST . "</li>";
    echo "<li>User: " . DB_USER . "</li>";
    echo "</ul>";

    // Test tables
    $tables = [
        'users',
        'doctor_profiles',
        'patient_profiles',
        'appointments',
        'ratings',
        'services',
        'doctor_services'
    ];

    echo "<h3>Checking Database Tables:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            // Get table structure
            $columns = $conn->query("SHOW COLUMNS FROM $table");
            echo "<li>✅ Table '$table' exists with " . $columns->rowCount() . " columns</li>";
        } else {
            echo "<li>❌ Table '$table' is missing</li>";
        }
    }
    echo "</ul>";

    // Test sample data insertion
    echo "<h3>Testing Data Insertion:</h3>";
    
    // Insert a test user
    $stmt = $conn->prepare("
        INSERT INTO users (first_name, last_name, email, password, phone, user_type)
        VALUES (:first_name, :last_name, :email, :password, :phone, :user_type)
    ");

    $testData = [
        ':first_name' => 'Test',
        ':last_name' => 'User',
        ':email' => 'test@example.com',
        ':password' => hashPassword('test123'),
        ':phone' => '1234567890',
        ':user_type' => 'patient'
    ];

    $stmt->execute($testData);
    $userId = $conn->lastInsertId();
    echo "<p>✅ Successfully inserted test user (ID: $userId)</p>";

    // Insert corresponding patient profile
    $stmt = $conn->prepare("
        INSERT INTO patient_profiles (patient_id, date_of_birth, gender)
        VALUES (:patient_id, :date_of_birth, :gender)
    ");

    $stmt->execute([
        ':patient_id' => $userId,
        ':date_of_birth' => '1990-01-01',
        ':gender' => 'male'
    ]);
    echo "<p>✅ Successfully inserted test patient profile</p>";

    // Clean up test data
    $conn->exec("DELETE FROM patient_profiles WHERE patient_id = $userId");
    $conn->exec("DELETE FROM users WHERE user_id = $userId");
    echo "<p>✅ Successfully cleaned up test data</p>";

    echo "<h3>Database is working correctly! 🎉</h3>";

} catch (PDOException $e) {
    echo "<h2>❌ Error: " . $e->getMessage() . "</h2>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>Is WAMP running? (Check for green icon in system tray)</li>";
    echo "<li>Is MySQL service running?</li>";
    echo "<li>Are the database credentials correct in config/database.php?</li>";
    echo "<li>Does the database 'medsave_database' exist?</li>";
    echo "</ul>";
}
?> 