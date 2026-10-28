<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';

if (isset($_SESSION['role'])) {
    header('Location: ' . APP_BASE_URL . ($_SESSION['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $requested_role = $_POST['role'] ?? '';
    if ($username === '' || $password === '' || !in_array($requested_role, ['guru', 'siswa'], true)) {
        $error = 'Pilih peran dan isi username serta password.';
    } else {
        $query = mysqli_prepare($conn, 'SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
        if (!$query) {
            http_response_code(500);
            exit('Gagal memproses login.');
        }
        mysqli_stmt_bind_param($query, 's', $username);
        mysqli_stmt_execute($query);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($query));
        $password_valid = $user && (
            password_verify($password, $user['password'])
            || hash_equals((string) $user['password'], $password)
        );
        if ($password_valid && $user['role'] === $requested_role) {
            if (hash_equals((string) $user['password'], $password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $upgrade = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id = ?');
                mysqli_stmt_bind_param($upgrade, 'si', $hash, $user['id']);
                if (!mysqli_stmt_execute($upgrade)) {
                    http_response_code(500);
                    exit('Gagal memperbarui keamanan password.');
                }
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: ' . APP_BASE_URL . 'dashboard.php');
            exit;
        }
        $error = 'Username, password, atau pilihan peran tidak sesuai.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
    <form method="post" action="" class="card border-0 shadow-sm p-4 p-md-5 w-100" style="max-width: 440px">
        <h1 class="h3 mb-1">Masuk ke SIPN</h1>
        <p class="text-secondary mb-4">Sistem Informasi Pengelolaan Nilai</p>
        <?php if ($error !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
        <label class="form-label" for="role">Masuk sebagai</label>
        <select class="form-select mb-3" id="role" name="role" required>
            <option value="">Pilih peran</option>
            <option value="guru" <?= ($_POST['role'] ?? '') === 'guru' ? 'selected' : ''; ?>>Guru</option>
            <option value="siswa" <?= ($_POST['role'] ?? '') === 'siswa' ? 'selected' : ''; ?>>Siswa</option>
        </select>
        <label class="form-label" for="username">Username</label>
        <input class="form-control mb-3" id="username" name="username" autocomplete="username" required>
        <label class="form-label" for="password">Password</label>
        <input class="form-control mb-4" id="password" name="password" type="password" autocomplete="current-password" required>
        <button class="btn btn-primary w-100" type="submit">Masuk</button>
        <p class="text-center mt-3 mb-0"><a href="<?= APP_BASE_URL; ?>login/register.php">Buat akun siswa</a></p>
        <p class="text-center mt-2 mb-0"><a href="<?= APP_BASE_URL; ?>login/admin_login.php">Login khusus admin</a></p>
    </form>
</main>
</body>
</html>
