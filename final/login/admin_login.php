<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';

if (isset($_SESSION['role'])) {
    $destination = $_SESSION['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard.php';
    header('Location: ' . APP_BASE_URL . $destination);
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $query = mysqli_prepare($conn, 'SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
        if (!$query) {
            http_response_code(500);
            exit('Gagal memproses login admin.');
        }
        mysqli_stmt_bind_param($query, 's', $username);
        mysqli_stmt_execute($query);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($query));
        $password_valid = $user && (
            password_verify($password, $user['password'])
            || hash_equals((string) $user['password'], $password)
        );

        if ($password_valid && $user['role'] === 'admin') {
            if (hash_equals((string) $user['password'], $password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $upgrade = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id = ?');
                if (!$upgrade) {
                    http_response_code(500);
                    exit('Gagal memperbarui keamanan password admin.');
                }
                mysqli_stmt_bind_param($upgrade, 'si', $hash, $user['id']);
                if (!mysqli_stmt_execute($upgrade)) {
                    http_response_code(500);
                    exit('Gagal memperbarui keamanan password admin.');
                }
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'admin';
            header('Location: ' . APP_BASE_URL . 'dashboard_admin.php');
            exit;
        }
        $error = 'Akun admin tidak ditemukan atau username dan password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
    <form method="post" class="card border-0 shadow-sm p-4 p-md-5 w-100" style="max-width: 440px">
        <h1 class="h3 mb-1">Login Admin</h1>
        <p class="text-secondary mb-4">Masuk ke panel administrator SIPN.</p>
        <?php if ($error !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
        <label class="form-label" for="username">Username admin</label>
        <input class="form-control mb-3" id="username" name="username" autocomplete="username" required>
        <label class="form-label" for="password">Password</label>
        <input class="form-control mb-4" id="password" name="password" type="password" autocomplete="current-password" required>
        <button class="btn btn-dark w-100" type="submit">Masuk sebagai Admin</button>
        <p class="text-center mt-3 mb-0"><a href="<?= APP_BASE_URL; ?>login/login.php">Login guru atau siswa</a></p>
    </form>
</main>
</body>
</html>