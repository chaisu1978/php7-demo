<?php
session_start();
require 'db.inc.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$taskId = $_POST['task_id'];

// Delete the task from the database
$sql = 'DELETE FROM tasks WHERE id = ? AND user_id = ?';
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([$taskId, $userId]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Task not found or permission denied.']);
    }
} catch (PDOException $e) {
    // Log and handle any errors
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the task.']);
}
