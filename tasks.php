<?php
require_once 'config.php';
require_login();

$userId = current_user_id();
$error = '';

$stmt = $conn->prepare('SELECT * FROM tasks WHERE user_id = ? ORDER BY is_completed ASC, task_date ASC, id DESC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$completed = 0;
foreach ($tasks as $task) {
    if ((int)$task['is_completed'] === 1) {
        $completed++;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Tasks</title>
<link rel="stylesheet" href="style.css">
<style>
.container { width: 80%; margin: auto; }
input, select { padding: 10px; margin: 5px; }
button { padding: 10px 15px; cursor: pointer; }
table { width: 100%; margin-top: 20px; border-collapse: collapse; background:white; }
th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
tr:hover { background-color: #f5f5f5; transform: scale(1.01); }
.priority-high { color: red; font-weight: bold; }
.priority-medium { color: orange; font-weight: bold; }
.priority-low { color: green; font-weight: bold; }
.counter { margin-top: 20px; font-size: 20px; font-weight: bold; }
.form-row { display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
.form-row input, .form-row select { flex:1 1 220px; max-width:none; }
</style>
</head>
<body>
<div class="navbar">
<a href="dashboard.php">Dashboard</a>
<a href="subjects.php">Subjects</a>
<a href="tasks.php">Tasks</a>
<a href="profile.php">Profile</a>
<a href="logout.php">Logout</a>
</div>
<div class="container">
<h1>Tasks Manager</h1>
<p class="error"><?= e($error) ?></p>
<form method="POST" action="" id="taskForm">
<div class="form-row">
<input name="task_name" placeholder="Task Name">
<input type="date" name="task_date">
<select name="task_priority">
<option value="High">High Priority</option>
<option value="Medium">Medium Priority</option>
<option value="Low">Low Priority</option>
</select>
<button type="submit" name="add_task">Add Task</button>
</div>
</form>
<table>
<thead><tr><th>Done ⬜</th><th>Task</th><th>Date</th><th>Priority</th><th>Delete</th></tr></thead>
<tbody>
<?php foreach ($tasks as $task): ?>
<tr>
<td>
  <button type="button" class="ajax-toggle-task" data-id="<?= (int)$task['id'] ?>" style="font-size:22px;">
    <?= (int)$task['is_completed'] === 1 ? '✅' : '⬜' ?>
  </button>
</td>
<td><?= e($task['task_name']) ?></td>
<td><?= e($task['task_date'] ?: '-') ?></td>
<td class="priority-<?= strtolower(e($task['priority_level'])) ?>"><?= e($task['priority_level']) ?></td>
<td><button type="button" class="ajax-delete-task" data-id="<?= (int)$task['id'] ?>">Delete</button></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<div class="counter">Completed Tasks: <span><?= $completed ?></span></div>
</div>
<script src="ajax.js"></script>
</body>
</html>
