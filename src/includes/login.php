<?php
session_start();
require 'db.inc.php';

// Retrieve credentials from POST
$username = $_POST['username'];
$password = $_POST['password'];

// Fetch user data from the database
$sql = 'SELECT id, password_hash, profile_picture FROM users WHERE username = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    // Set session variables upon successful login
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $username;
    $_SESSION['profile_picture'] = $user['profile_picture'];

    echo json_encode(['success' => true, 'profile_picture' => $user['profile_picture']]);
} else {
    // Invalid credentials
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
}
