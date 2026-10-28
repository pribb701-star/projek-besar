<?php
session_start();
require_once __DIR__ . '/koneksi/koneksi.php';

if (!isset($_SESSION['role'])) {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
if ($_SESSION['role'] === 'admin') {
    header('Location: ' . APP_BASE_URL . 'dashboard_admin.php');
    exit;
}
if (!in_array($_SESSION['role'], ['guru', 'siswa'], true)) {
    http_response_code(403);
    exit('Peran akun tidak dikenali.');
}

$is_student = $_SESSION['role'] === 'siswa';
$grade_link = $is_student ? 'nilai/lihat.php' : 'nilai/form_tambah.php';
$grade_label = $is_student ? 'Cek Nilai' : 'Tambah Nilai';
$title = $is_student ? 'Dashboard Siswa' : 'Dashboard Guru';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?> - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= APP_BASE_URL; ?>dashboard.php">SIPN</a>
        <div class="navbar-nav ms-auto flex-row gap-3">
            <a class="nav-link" href="<?= APP_BASE_URL . $grade_link; ?>"><?= htmlspecialchars($grade_label, ENT_QUOTES, 'UTF-8'); ?></a>
            <a class="nav-link" href="<?= APP_BASE_URL; ?>pengumuman/daftar.php">Pengumuman</a>
            <a class="nav-link" href="<?= APP_BASE_URL; ?>login/logout.php">Keluar</a>
        </div>
    </div>
</nav>
<main class="container py-5">
    <section class="p-4 p-md-5 mb-4 bg-white rounded-4 shadow-sm">
        <span class="badge text-bg-primary mb-3"><?= $is_student ? 'RUANG SISWA' : 'RUANG GURU'; ?></span>
        <h1 class="h2">Selamat datang, <?= htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <p class="text-secondary mb-4"><?= $is_student ? 'Periksa nilai dan baca pengumuman terbaru.' : 'Kelola nilai siswa dan sampaikan pengumuman.'; ?></p>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary" href="<?= APP_BASE_URL . $grade_link; ?>"><?= htmlspecialchars($grade_label, ENT_QUOTES, 'UTF-8'); ?></a>
            <a class="btn btn-outline-primary" href="<?= APP_BASE_URL; ?>pengumuman/daftar.php"><?= $is_student ? 'Lihat Pengumuman' : 'Tambah Pengumuman'; ?></a>
        </div>
    </section>
</main>
</body>
</html>
