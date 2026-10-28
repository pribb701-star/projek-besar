<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}

$students = mysqli_query($conn, 'SELECT id, nis, nama, kelas FROM siswa ORDER BY nama');
$subjects = mysqli_query($conn, 'SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel');
if (!$students || !$subjects) {
    http_response_code(500);
    exit('Gagal memuat pilihan siswa atau mata pelajaran.');
}
$error = $_SESSION['crud_error'] ?? '';
unset($_SESSION['crud_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Nilai - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-4 py-lg-5" style="max-width: 760px">
    <a href="<?= APP_BASE_URL; ?>dashboard.php" class="text-decoration-none">&larr; Kembali ke dashboard guru</a>
    <h1 class="h2 mt-3 mb-4">Tambah Nilai</h1>
    <form method="post" action="<?= APP_BASE_URL; ?>nilai/tambah.php" class="card border-0 shadow-sm">
        <div class="card-body p-4 d-grid gap-3">
            <?php if ($error !== ''): ?><div class="alert alert-warning mb-0"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <label class="form-label mb-0">Siswa
                <select class="form-select mt-1" name="siswa_id" required>
                    <option value="">Pilih siswa</option>
                    <?php while ($student = mysqli_fetch_assoc($students)): ?>
                        <option value="<?= (int) $student['id']; ?>">
                            <?= htmlspecialchars($student['nama'] . ' — ' . $student['kelas'] . ' (' . $student['nis'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </label>
            <label class="form-label mb-0">Mata pelajaran
                <select class="form-select mt-1" name="mapel_id" required>
                    <option value="">Pilih mata pelajaran</option>
                    <?php while ($subject = mysqli_fetch_assoc($subjects)): ?>
                        <option value="<?= (int) $subject['id']; ?>"><?= htmlspecialchars($subject['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label w-100">KKM<input class="form-control" type="number" name="nilai_kkm" min="0" max="100" required></label></div>
                <div class="col-md-4"><label class="form-label w-100">Pengetahuan<input class="form-control" type="number" name="nilai_pengetahuan" min="0" max="100" required></label></div>
                <div class="col-md-4"><label class="form-label w-100">Keterampilan<input class="form-control" type="number" name="nilai_keterampilan" min="0" max="100" required></label></div>
            </div>
            <p class="small text-secondary mb-0">Satu nilai dapat dicatat untuk setiap kombinasi siswa dan mata pelajaran.</p>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">Simpan Nilai</button>
                <a class="btn btn-outline-secondary" href="<?= APP_BASE_URL; ?>nilai/daftar.php">Lihat Daftar Nilai</a>
            </div>
        </div>
    </form>
</main>
</body>
</html>
