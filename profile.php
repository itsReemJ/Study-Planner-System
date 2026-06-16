<?php
require_once 'config.php';
require_login();

$userId = current_user_id();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['full_name'] ?? '');
    $major = trim($_POST['major'] ?? '');
    $year  = trim($_POST['academic_year'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $check = $conn->prepare('SELECT profile_image FROM users WHERE id = ?');
    $check->bind_param('i', $userId);
    $check->execute();
    $current = $check->get_result()->fetch_assoc();
    $check->close();
    $imagePath = $current['profile_image'] ?? '';

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['profile_image']['tmp_name'];
        $ext     = strtolower(pathinfo(basename($_FILES['profile_image']['name']), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $mime    = mime_content_type($tmpName);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($ext, $allowed, true) || !in_array($mime, $allowedMimes, true)) {
            $message = 'Invalid image type';
        } elseif ($_FILES['profile_image']['size'] > 2 * 1024 * 1024) {
            $message = 'Image must be under 2MB';
        } else {
            $uploadDir = __DIR__ . '/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $newName = 'uploads/user_' . $userId . '_' . time() . '.' . $ext;
            if (!empty($current['profile_image']) && file_exists($current['profile_image'])) {
                unlink($current['profile_image']);
            }
            if (move_uploaded_file($tmpName, $newName)) {
                $imagePath = $newName;
            }
        }
    }

    $stmt = $conn->prepare('UPDATE users SET full_name=?, major=?, academic_year=?, phone=?, email=?, profile_image=? WHERE id=?');
    $stmt->bind_param('ssssssi', $name, $major, $year, $phone, $email, $imagePath, $userId);
    if ($stmt->execute()) {
        $_SESSION['user_name']  = $name;
        $_SESSION['user_email'] = $email;
        if (empty($message)) {
            $message = 'Profile updated successfully';
        }
    } else {
        $message = 'Could not update profile';
    }
    $stmt->close();
}

$stmt = $conn->prepare('SELECT full_name, major, academic_year, phone, email, profile_image FROM users WHERE id=?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$imageSrc = !empty($user['profile_image'])
    ? $user['profile_image']
    : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
?>
<!DOCTYPE html>
<html>
<head>
<title>Profile</title>
<link rel="stylesheet" href="style.css">
<style>
.profile-img { width:150px; height:150px; border-radius:50%; background-color:#ddd; display:flex; align-items:center; justify-content:center; overflow:hidden; margin-right:30px; position:relative; }
.profile-img img { width:100%; height:100%; object-fit:cover; }
.profile-img input { position:absolute; bottom:0; width:100%; opacity:0.7; cursor:pointer; }
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
  <div class="profile-box">
    <form method="POST" enctype="multipart/form-data" style="display:flex; gap:40px; width:100%; align-items:flex-start;">
      <div class="profile-img">
        <img id="previewImage" src="<?= e($imageSrc) ?>" alt="Profile Image">
        <input type="file" name="profile_image" accept="image/*" onchange="loadImage(event)">
      </div>
      <div style="flex:1;">
        <h2>Student Information</h2>
        <p class="<?= str_contains($message, 'success') ? '' : 'error' ?>"
           style="color:<?= str_contains($message, 'success') ? 'green' : 'red' ?>">
          <?= e($message) ?>
        </p>
        <input name="full_name" placeholder="Student Name" value="<?= e($user['full_name'] ?? '') ?>"><br><br>
        <input name="major" placeholder="Major" value="<?= e($user['major'] ?? '') ?>"><br><br>
        <input name="academic_year" placeholder="Year" value="<?= e($user['academic_year'] ?? '') ?>"><br><br>
        <input name="phone" placeholder="Phone Number" value="<?= e($user['phone'] ?? '') ?>"><br><br>
        <input name="email" placeholder="Email" value="<?= e($user['email'] ?? '') ?>"><br><br>
        <button type="submit">Save Info</button>
      </div>
    </form>
  </div>
</div>
<script>
function loadImage(event) {
  document.getElementById('previewImage').src = URL.createObjectURL(event.target.files[0]);
}
</script>
</body>
</html>