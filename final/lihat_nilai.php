<?php
session_start();
require_once __DIR__ . '/koneksi/koneksi.php';

if (!isset($_SESSION['role'])) {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
if ($_SESSION['role'] !== 'siswa') {
    header('Location: ' . APP_BASE_URL . ($_SESSION['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard.php'));
    exit;
}

$query = mysqli_prepare(
    $conn,
    'SELECT mapel.nama_mapel, nilai_rapor.nilai_kkm, nilai_rapor.nilai_pengetahuan, nilai_rapor.nilai_keterampilan, nilai_rapor.keterangan
     FROM nilai_rapor
     JOIN siswa ON siswa.id = nilai_rapor.siswa_id
     JOIN mapel ON mapel.id = nilai_rapor.mapel_id
     WHERE siswa.user_id = ?
     ORDER BY mapel.nama_mapel'
);
if (!$query) {
    http_response_code(500);
    exit('Gagal memuat nilai.');
}
$user_id = (int) $_SESSION['user_id'];
mysqli_stmt_bind_param($query, 'i', $user_id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nilai Saya - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-4">
    <a href="<?= APP_BASE_URL; ?>dashboard.php" class="text-decoration-none">&larr; Dashboard</a>
    <h1 class="h3 my-3">Nilai Saya</h1>
    <div class="card border-0 shadow-sm"><div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead><tr><th>Mata Pelajaran</th><th>KKM</th><th>Pengetahuan</th><th>Keterampilan</th><th>Keterangan</th></tr></thead>
            <tbody>
            <?php if (mysqli_num_rows($result) === 0): ?>
                <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada nilai yang tersedia.</td></tr>
            <?php else: while ($row = mysqli_fetch_assoc($result)): ?>
                <tr><?php foreach ($row as $value): ?><td><?= htmlspecialchars((string) ($value ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td><?php endforeach; ?></tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div></div>
</main>
</body>
</html>
