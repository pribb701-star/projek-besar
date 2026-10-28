<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
$id = filter_var($_GET['id'] ?? $_POST['id'] ?? $_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('ID user tidak valid.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset_pw'])) {
        $password = $_POST['password_baru'] ?? '';
        if ($password === '') {
            http_response_code(400);
            exit('Password baru wajib diisi.');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id = ?');
        mysqli_stmt_bind_param($update, 'si', $hash, $id);
    } else {
        $username = trim($_POST['username'] ?? '');
        $role = $_POST['role'] ?? '';
        $password = $_POST['password_baru'] ?? '';
        if ($username === '' || !in_array($role, ['admin', 'guru', 'siswa'], true)) {
            http_response_code(400);
            exit('Data akun tidak valid.');
        }
        $duplicate = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? AND id <> ?');
        mysqli_stmt_bind_param($duplicate, 'si', $username, $id);
        mysqli_stmt_execute($duplicate);
        if (mysqli_stmt_get_result($duplicate)->num_rows > 0) {
            http_response_code(409);
            exit('Username sudah digunakan.');
        }
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $update = mysqli_prepare($conn, 'UPDATE users SET username = ?, role = ?, password = ? WHERE id = ?');
            mysqli_stmt_bind_param($update, 'sssi', $username, $role, $hash, $id);
        } else {
            $update = mysqli_prepare($conn, 'UPDATE users SET username = ?, role = ? WHERE id = ?');
            mysqli_stmt_bind_param($update, 'ssi', $username, $role, $id);
        }
    }
    if (!mysqli_stmt_execute($update)) {
        http_response_code(500);
        exit('Gagal memperbarui akun.');
    }
    header('Location: ' . APP_BASE_URL . 'user/daftar.php');
    exit;
}
$query = mysqli_prepare($conn, 'SELECT id, username, role FROM users WHERE id = ?');
mysqli_stmt_bind_param($query, 'i', $id);
mysqli_stmt_execute($query);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($query));
if (!$user) {
    http_response_code(404);
    exit('User tidak ditemukan.');
}
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit User</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><main class="container py-4"><h1 class="h3 mb-4">Edit Pengguna</h1>
<form method="post" class="card card-body gap-3"><input type="hidden" name="id" value="<?= (int) $user['id']; ?>">
<label>Username<input class="form-control" name="username" required value="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>"></label>
<label>Peran<select class="form-select" name="role" required><?php foreach (['admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa'] as $role => $label): ?><option value="<?= $role; ?>" <?= $user['role'] === $role ? 'selected' : ''; ?>><?= $label; ?></option><?php endforeach; ?></select></label>
<label>Password baru <small>(kosongkan jika tidak diubah)</small><input class="form-control" type="password" name="password_baru"></label>
<div><button class="btn btn-primary">Simpan Perubahan</button> <a class="btn btn-secondary" href="<?= APP_BASE_URL; ?>user/daftar.php">Batal</a></div></form></main></body></html>
