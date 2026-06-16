<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill all fields';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email';
    } else {
        $check = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->bind_param('s', $email);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();
        $check->close();

        if ($exists) {
            $error = 'This email is already registered';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $name, $email, $hashed);

            if ($stmt->execute()) {
                $success = 'Account created successfully. You can login now.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Create Account</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="bg">
<div class="card-box">
<h2>Create Student Account</h2>
<form method="POST" action="">
<input name="name" placeholder="Full Name" value="<?= e($_POST['name'] ?? '') ?>">
<input name="email" type="email" placeholder="University Email" value="<?= e($_POST['email'] ?? '') ?>">
<input name="password" type="password" placeholder="Password">
<button type="submit">Register</button>
</form>
<p class="<?= $success ? 'success' : 'error' ?>"><?= e($error ?: $success) ?></p>
<p>
Already registered?
<a href="index.php">Login</a>
</p>
</div>
</body>
</html>
