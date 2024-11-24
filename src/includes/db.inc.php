<?php
// Load environment variables
$host = getenv('DB_HOST') ?: 'mariadb-container';
$db = getenv('MYSQL_DATABASE') ?: 'tasksdb';
$user = getenv('MYSQL_USER') ?: 'default_user';
$pass = getenv('MYSQL_PASSWORD') ?: 'default_password';
$charset = 'utf8mb4';

// Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options for security and performance
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enable exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Log error and display a generic message
    error_log($e->getMessage());
    exit('Database connection failed. Please try again later.');
}
