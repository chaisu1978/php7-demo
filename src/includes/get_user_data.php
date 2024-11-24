<?php
session_start();
require 'db.inc.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user data from the database
$sql = 'SELECT email, profile_picture FROM users WHERE id = ?';
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if ($user) {
        echo json_encode([
            'success' => true,
            'email' => $user['email'],
            'profilePictureUrl' => $user['profile_picture'] ?: 'placeholder.jpg'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User data not found.']);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching user data.']);
}
