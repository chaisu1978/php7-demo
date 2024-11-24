<?php
session_start();
require 'db.inc.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$currentPassword = $_POST['currentPassword'];
$newPassword = $_POST['newPassword'];
$confirmNewPassword = $_POST['confirmNewPassword'];

// Validate new password
if ($newPassword !== $confirmNewPassword) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
    exit;
}

// Verify current password
$sql = 'SELECT password_hash FROM users WHERE id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
    exit;
}

// Update password
$newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
$sql = 'UPDATE users SET password_hash = ? WHERE id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$newPasswordHash, $userId]);

echo json_encode(['success' => true]);
