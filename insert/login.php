<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['login'])) {
    header("Location: daftar_terpadu.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: daftar_terpadu.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIACAD SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style1.css">
</head>
<body class="auth-page">
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-brand">SI</div>
            <h3>SIACAD SMK</h3>
            <p class="auth-subtitle">Sistem Informasi Akademik</p>
        </div>
        <div class="auth-body">
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger py-2 mb-3"><?= $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="auth-login-btn">Login</button>
            </form>
            <div class="auth-helper">Selamat datang di portal akademik</div>
        </div>
    </div>
</div>
</body>
</html>