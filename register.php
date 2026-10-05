<?php
session_start();
require_once __DIR__ . "/config.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $usernameEsc = mysqli_real_escape_string($conn, $username);
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$usernameEsc'");

        if (mysqli_num_rows($check) > 0) {
            $error = 'Username sudah dipakai.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (username, password_hash) VALUES ('$usernameEsc', '$hash')");
            $userId = mysqli_insert_id($conn);

            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <title>Daftar — Weekly Planner</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/weeklyplanner/style.css"/>
</head>
<body>
  <div class="auth-container">
    <div class="auth-card">
      <h1>Buat Akun</h1>
      <p class="auth-subtitle">Mulai kelola rencana mingguan kamu</p>

      <?php if ($error): ?>
        <div class="auth-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" class="auth-form">
        <input type="text" name="username" placeholder="Username" required autofocus>
        <input type="password" name="password" placeholder="Password (min. 6 karakter)" required>
        <button type="submit">Daftar</button>
      </form>

      <p class="auth-switch">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
    </div>
  </div>
</body>
</html>
