<?php
require 'db.inc.php';

// Retrieve data from POST
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// Input Validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// Check if username or email already exists
$sql = 'SELECT id FROM users WHERE username = ? OR email = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$username, $email]);
$existingUser = $stmt->fetch();

if ($existingUser) {
    echo json_encode(['success' => false, 'message' => 'Username or email already exists.']);
    exit;
}

// Hash the password securely
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert new user into the database
$sql = 'INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)';
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([$username, $email, $passwordHash]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Log and handle any errors
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred during signup.']);
}
