<?php
session_start();
require_once __DIR__ . '/koneksi/koneksi.php';

if (!isset($_SESSION['role'])) {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'dashboard.php');
    exit;
}

$counts = [];
foreach (['siswa' => 'Siswa', 'guru' => 'Guru', 'mapel' => 'Mata Pelajaran'] as $table => $label) {
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$table`");
    if (!$result) {
        http_response_code(500);
        exit('Gagal memuat statistik dashboard.');
    }
    $counts[$label] = (int) mysqli_fetch_assoc($result)['total'];
}

$sections = [
    'siswa' => 'Kelola Siswa',
    'guru' => 'Kelola Guru',
    'mapel' => 'Kelola Mata Pelajaran',
    'mengajar' => 'Kelola Mengajar',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= APP_BASE_URL; ?>dashboard_admin.php">SIPN Admin</a>
        <div class="navbar-nav ms-auto flex-row gap-3">
            <a class="nav-link" href="<?= APP_BASE_URL; ?>profile/profil.php">Profil</a>
            <a class="nav-link" href="<?= APP_BASE_URL; ?>login/logout.php">Keluar</a>
        </div>
    </div>
</nav>
<main class="container py-5">
    <h1 class="h2 mb-1">Dashboard Administrator</h1>
    <p class="text-secondary mb-4">Selamat datang, <?= htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>.</p>
    <div class="row g-3 mb-4">
        <?php foreach ($counts as $label => $total): ?>
            <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="text-secondary"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="display-6 fw-semibold"><?= number_format($total); ?></div>
            </div></div></div>
        <?php endforeach; ?>
    </div>
    <section class="card border-0 shadow-sm"><div class="card-body p-4">
        <h2 class="h5 mb-3">Menu Pengelolaan</h2>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($sections as $path => $label): ?>
                <a class="btn btn-outline-primary" href="<?= APP_BASE_URL . $path; ?>/daftar.php"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></a>
            <?php endforeach; ?>
        </div>
    </div></section>
</main>
</body>
</html>
