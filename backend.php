<?php
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// 1. FETCH TASKS (With JSON Response - Requirement #1)
if ($action == 'fetch') {
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY status ASC, id DESC");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($tasks);
    exit();
}

// 2. ADD TASK (With Server Validation - Requirement #3 & #9)
if ($action == 'add') {
    $task_name = trim($_POST['task_name'] ?? '');
    $priority = $_POST['priority'] ?? 'Medium';

    if (!empty($task_name)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (task_name, priority, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$task_name, $priority]);
        echo json_encode(["success" => true, "message" => "Task added successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Task name cannot be empty!"]);
    }
    exit();
}

// 3. TOGGLE STATUS (Pending <-> Completed)
if ($action == 'toggle') {
    $id = $_POST['id'] ?? 0;
    $current_status = $_POST['status'] ?? 'Pending';
    $new_status = ($current_status === 'Pending') ? 'Completed' : 'Pending';

    if ($id) {
        $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        echo json_encode(["success" => true]);
    }
    exit();
}

// 4. DELETE TASK
if ($action == 'delete') {
    $id = $_POST['id'] ?? 0;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["success" => true]);
    }
    exit();
}
?>