<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}

$query = mysqli_query(
    $conn,
    'SELECT guru.id, guru.nip, guru.nama, guru.jenis_kelamin, users.username
     FROM guru
     LEFT JOIN users ON users.id = guru.user_id
     ORDER BY guru.nama'
);
if (!$query) {
    http_response_code(500);
    exit('Gagal memuat daftar guru: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'));
}
$total = mysqli_num_rows($query);
$error = $_SESSION['crud_error'] ?? '';
unset($_SESSION['crud_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Guru - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f3ef; }
        .teacher-heading { border-left: 5px solid #a34f36; }
        .teacher-table thead th { background: #493b2d; color: #fff; white-space: nowrap; }
        .teacher-table tbody tr:hover { --bs-table-accent-bg: #fbf1e6; }
        .teacher-mark { color: #a34f36; }
    </style>
</head>
<body>
<main class="container py-4 py-lg-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="teacher-heading ps-3">
            <a href="<?= APP_BASE_URL; ?>dashboard_admin.php" class="small text-decoration-none text-secondary">&larr; Dashboard admin</a>
            <h1 class="h2 mt-2 mb-1">Data Guru</h1>
            <p class="text-secondary mb-0">Kelola identitas dan akun pengajar.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="<?= APP_BASE_URL; ?>dashboard_admin.php">Dashboard</a>
            <a class="btn btn-danger" href="<?= APP_BASE_URL; ?>guru/form_tambah.php">+ Tambah Guru</a>
        </div>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <section class="card border-0 shadow-sm">
        <div class="card-body border-bottom d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0 teacher-mark">Daftar Pengajar</h2>
            <span class="badge rounded-pill text-bg-secondary"><?= $total; ?> guru</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle teacher-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">No.</th>
                        <th>NIP</th>
                        <th>Nama Guru</th>
                        <th>Jenis Kelamin</th>
                        <th>Akun Login</th>
                        <th class="text-end pe-3">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($total === 0): ?>
                    <tr><td colspan="6" class="text-center text-secondary py-5">Belum ada data guru.</td></tr>
                <?php else: $number = 1; while ($teacher = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td class="ps-3"><?= $number++; ?></td>
                        <td><?= htmlspecialchars($teacher['nip'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($teacher['nama'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= $teacher['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                        <td><?= htmlspecialchars($teacher['username'] ?? 'Belum terhubung', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-end text-nowrap pe-3">
                            <a class="btn btn-sm btn-outline-primary" href="<?= APP_BASE_URL; ?>guru/edit.php?id=<?= (int) $teacher['id']; ?>">Edit</a>
                            <a class="btn btn-sm btn-outline-danger" href="<?= APP_BASE_URL; ?>guru/hapus.php?id=<?= (int) $teacher['id']; ?>" onclick="return confirm('Hapus data guru ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
