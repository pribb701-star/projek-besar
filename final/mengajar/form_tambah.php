<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}

$mapel = mysqli_query($conn, 'SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel');
$siswa = mysqli_query($conn, 'SELECT id, nama, kelas FROM siswa ORDER BY nama');
$guru = mysqli_query($conn, "SELECT guru.id, users.username FROM guru JOIN users ON users.id = guru.user_id WHERE users.role = 'guru' ORDER BY users.username");
if (!$mapel || !$siswa || !$guru) {
    http_response_code(500);
    exit('Gagal memuat pilihan mata pelajaran, siswa, atau guru.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Penugasan Mengajar - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-4" style="max-width: 760px">
    <a href="<?= APP_BASE_URL; ?>mengajar/daftar.php" class="text-decoration-none">&larr; Kembali ke daftar mengajar</a>
    <h1 class="h3 mt-3 mb-4">Tambah Penugasan Mengajar</h1>
    <form method="post" action="<?= APP_BASE_URL; ?>mengajar/tambah.php" class="card border-0 shadow-sm">
        <div class="card-body d-grid gap-3">
            <label>Mata Pelajaran
                <select class="form-select mt-1" name="mapel_id" required>
                    <option value="">Pilih mata pelajaran</option>
                    <?php while ($row = mysqli_fetch_assoc($mapel)): ?>
                        <option value="<?= (int) $row['id']; ?>"><?= htmlspecialchars($row['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
            <label>Siswa
                <select class="form-select mt-1" name="siswa_id" required>
                    <option value="">Pilih siswa</option>
                    <?php while ($row = mysqli_fetch_assoc($siswa)): ?>
                        <option value="<?= (int) $row['id']; ?>"><?= htmlspecialchars($row['nama'] . ' (' . $row['kelas'] . ')', ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
            <label>Guru Pengajar
                <select class="form-select mt-1" name="guru_id" required>
                    <option value="">Pilih guru</option>
                    <?php while ($row = mysqli_fetch_assoc($guru)): ?>
                        <option value="<?= (int) $row['id']; ?>"><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-outline-secondary" href="<?= APP_BASE_URL; ?>mengajar/daftar.php">Batal</a>
            </div>
        </div>
    </form>
</main>
</body>
</html>
