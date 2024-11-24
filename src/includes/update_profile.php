<?php
session_start();
require 'db.inc.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$newEmail = $_POST['email'];
$profilePicture = $_FILES['profilePicture'];

// Update email
if (!empty($newEmail)) {
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }
    $sql = 'UPDATE users SET email = ? WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$newEmail, $userId]);
}

// Update profile picture
if (!empty($profilePicture['name'])) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($profilePicture['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
        exit;
    }

    $extension = pathinfo($profilePicture['name'], PATHINFO_EXTENSION);
    $newFileName = 'profile_' . $userId . '.' . $extension;
    $uploadDir = '../uploads/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

    $destination = $uploadDir . $newFileName;
    if (move_uploaded_file($profilePicture['tmp_name'], $destination)) {
        $profilePicturePath = 'uploads/' . $newFileName;
        $sql = 'UPDATE users SET profile_picture = ? WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$profilePicturePath, $userId]);
    }
}

echo json_encode(['success' => true, 'profilePictureUrl' => $profilePicturePath ?? null]);
