<?php
session_start();
require 'db.inc.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['tasks' => []]);
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch tasks from the database
$sql = 'SELECT id, task_name, category, due_date, is_completed FROM tasks WHERE user_id = ? ORDER BY created_at ASC';
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$tasks = $stmt->fetchAll();

echo json_encode(['tasks' => $tasks]);
