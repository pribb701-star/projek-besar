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
    exit('ID penugasan tidak valid.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mapel_id = filter_var($_POST['mapel_id'] ?? null, FILTER_VALIDATE_INT);
    $siswa_id = filter_var($_POST['siswa_id'] ?? null, FILTER_VALIDATE_INT);
    $guru_id = filter_var($_POST['guru_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$mapel_id || !$siswa_id || !$guru_id) {
        http_response_code(400);
        exit('Mata pelajaran, siswa, atau guru tidak valid.');
    }
    $check_guru = mysqli_prepare($conn, "SELECT guru.id FROM guru JOIN users ON users.id = guru.user_id WHERE guru.id = ? AND users.role = 'guru'");
    if (!$check_guru) {
        http_response_code(500);
        exit('Gagal memvalidasi guru pengajar.');
    }
    mysqli_stmt_bind_param($check_guru, 'i', $guru_id);
    if (!mysqli_stmt_execute($check_guru) || !mysqli_fetch_assoc(mysqli_stmt_get_result($check_guru))) {
        http_response_code(400);
        exit('Guru pengajar tidak valid.');
    }
    $update = mysqli_prepare($conn, 'UPDATE mengajar SET mapel_id = ?, siswa_id = ?, guru_id = ? WHERE id = ?');
    if (!$update) {
        http_response_code(500);
        exit('Gagal menyiapkan perubahan penugasan.');
    }
    mysqli_stmt_bind_param($update, 'iiii', $mapel_id, $siswa_id, $guru_id, $id);
    if (!mysqli_stmt_execute($update)) {
        http_response_code(500);
        exit('Gagal memperbarui penugasan.');
    }
    header('Location: ' . APP_BASE_URL . 'mengajar/daftar.php');
    exit;
}
$query = mysqli_prepare($conn, 'SELECT * FROM mengajar WHERE id = ?');
mysqli_stmt_bind_param($query, 'i', $id);
mysqli_stmt_execute($query);
$mengajar = mysqli_fetch_assoc(mysqli_stmt_get_result($query));
if (!$mengajar) {
    http_response_code(404);
    exit('Penugasan tidak ditemukan.');
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
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Penugasan</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><main class="container py-4"><h1 class="h3 mb-4">Edit Penugasan Mengajar</h1>
<form method="post" class="card card-body gap-3"><input type="hidden" name="id" value="<?= (int) $mengajar['id']; ?>">
<label>Mata Pelajaran<select class="form-select" name="mapel_id" required><?php while ($row = mysqli_fetch_assoc($mapel)): ?><option value="<?= (int) $row['id']; ?>" <?= (int) $mengajar['mapel_id'] === (int) $row['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($row['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></option><?php endwhile; ?></select></label>
<label>Siswa<select class="form-select" name="siswa_id" required><?php while ($row = mysqli_fetch_assoc($siswa)): ?><option value="<?= (int) $row['id']; ?>" <?= (int) $mengajar['siswa_id'] === (int) $row['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($row['nama'] . ' (' . $row['kelas'] . ')', ENT_QUOTES, 'UTF-8'); ?></option><?php endwhile; ?></select></label>
<label>Guru Pengajar<select class="form-select" name="guru_id" required><option value="">Pilih guru</option><?php while ($row = mysqli_fetch_assoc($guru)): ?><option value="<?= (int) $row['id']; ?>" <?= (int) $mengajar['guru_id'] === (int) $row['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></option><?php endwhile; ?></select></label>
<div><button class="btn btn-primary">Simpan Perubahan</button> <a class="btn btn-secondary" href="<?= APP_BASE_URL; ?>mengajar/daftar.php">Batal</a></div></form></main></body></html>
