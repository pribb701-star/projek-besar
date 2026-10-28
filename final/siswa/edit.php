<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
$id = filter_var($_GET['id'] ?? $_POST['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('ID siswa tidak valid.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = trim($_POST['nis'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $user_id = ($_POST['user_id'] ?? '') === '' ? null : filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if ($nis === '' || $nama === '' || $kelas === '' || !in_array($jenis_kelamin, ['L', 'P'], true)
        || (($_POST['user_id'] ?? '') !== '' && $user_id === false)) {
        http_response_code(400);
        exit('Data siswa tidak valid.');
    }
    $update = mysqli_prepare($conn, 'UPDATE siswa SET nis = ?, nama = ?, kelas = ?, jenis_kelamin = ?, user_id = ? WHERE id = ?');
    mysqli_stmt_bind_param($update, 'ssssii', $nis, $nama, $kelas, $jenis_kelamin, $user_id, $id);
    if (!mysqli_stmt_execute($update)) {
        http_response_code(500);
        exit('Gagal memperbarui data siswa.');
    }
    header('Location: ' . APP_BASE_URL . 'siswa/daftar.php');
    exit;
}
$query = mysqli_prepare($conn, 'SELECT * FROM siswa WHERE id = ?');
mysqli_stmt_bind_param($query, 'i', $id);
mysqli_stmt_execute($query);
$siswa = mysqli_fetch_assoc(mysqli_stmt_get_result($query));
if (!$siswa) {
    http_response_code(404);
    exit('Data siswa tidak ditemukan.');
}
$users = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'siswa' ORDER BY username");
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Siswa</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><main class="container py-4"><h1 class="h3 mb-4">Edit Data Siswa</h1>
<form method="post" class="card card-body gap-3"><input type="hidden" name="id" value="<?= (int) $siswa['id']; ?>">
<label>NIS<input class="form-control" name="nis" required value="<?= htmlspecialchars($siswa['nis'], ENT_QUOTES, 'UTF-8'); ?>"></label>
<label>Nama<input class="form-control" name="nama" required value="<?= htmlspecialchars($siswa['nama'], ENT_QUOTES, 'UTF-8'); ?>"></label>
<label>Kelas<input class="form-control" name="kelas" required value="<?= htmlspecialchars($siswa['kelas'], ENT_QUOTES, 'UTF-8'); ?>"></label>
<label>Jenis Kelamin<select class="form-select" name="jenis_kelamin"><option value="L" <?= $siswa['jenis_kelamin'] === 'L' ? 'selected' : ''; ?>>Laki-laki</option><option value="P" <?= $siswa['jenis_kelamin'] === 'P' ? 'selected' : ''; ?>>Perempuan</option></select></label>
<label>Akun<select class="form-select" name="user_id"><option value="">Tanpa akun</option><?php while ($user = mysqli_fetch_assoc($users)): ?><option value="<?= (int) $user['id']; ?>" <?= (int) $siswa['user_id'] === (int) $user['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></option><?php endwhile; ?></select></label>
<div><button class="btn btn-primary">Simpan Perubahan</button> <a class="btn btn-secondary" href="<?= APP_BASE_URL; ?>siswa/daftar.php">Batal</a></div></form></main></body></html>
