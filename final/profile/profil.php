<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$query = mysqli_prepare($conn, 'SELECT id, username, role FROM users WHERE id = ?');
if (!$query) {
    http_response_code(500);
    exit('Gagal memuat profil pengguna.');
}
mysqli_stmt_bind_param($query, 'i', $user_id);
mysqli_stmt_execute($query);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($query));
if (!$user) {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Pengguna - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <a class="text-decoration-none" href="<?= APP_BASE_URL . ($user['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard.php'); ?>">&larr; Kembali ke dashboard</a>
    <section class="card border-0 shadow-sm mt-3 mx-auto" style="max-width: 560px">
        <div class="card-body p-4 p-md-5">
            <h1 class="h3 mb-4">Profil Pengguna</h1>
            <dl class="row mb-4">
                <dt class="col-sm-4">Username</dt><dd class="col-sm-8"><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></dd>
                <dt class="col-sm-4">Peran</dt><dd class="col-sm-8"><?= htmlspecialchars(ucfirst($user['role']), ENT_QUOTES, 'UTF-8'); ?></dd>
                <dt class="col-sm-4">ID Pengguna</dt><dd class="col-sm-8"><?= (int) $user['id']; ?></dd>
            </dl>
            <a class="btn btn-outline-danger" href="<?= APP_BASE_URL; ?>login/logout.php">Keluar</a>
        </div>
    </section>
</main>
</body>
</html>
