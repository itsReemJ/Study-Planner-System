<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please fill all fields';
    } else {
        $stmt = $conn->prepare('SELECT id, full_name, email, password FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            redirect('dashboard.php');
        } else {
            $error = 'Wrong email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Study Planner - Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="bg">
<div class="card-box">
<h2>Welcome Back</h2>
<form method="POST" action="">
<input name="email" type="email" placeholder="University Email" value="<?= e($_POST['email'] ?? '') ?>">
<input name="password" type="password" placeholder="Password">
<button type="submit">Login</button>
</form>
<p class="error"><?= e($error) ?></p>
<p>
Don't have account?
<a href="register.php">Create one</a>
</p>
</div>
</body>
</html>
