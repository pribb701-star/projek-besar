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
    exit('ID mata pelajaran tidak valid.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode_mapel'] ?? '');
    $nama = trim($_POST['nama_mapel'] ?? '');
    $guru_id = ($_POST['guru_id'] ?? '') === '' ? null : filter_var($_POST['guru_id'], FILTER_VALIDATE_INT);
    if ($kode === '' || $nama === '' || (($_POST['guru_id'] ?? '') !== '' && $guru_id === false)) {
        http_response_code(400);
        exit('Data mata pelajaran tidak valid.');
    }
    $update = mysqli_prepare($conn, 'UPDATE mapel SET kode_mapel = ?, nama_mapel = ?, guru_id = ? WHERE id = ?');
    mysqli_stmt_bind_param($update, 'ssii', $kode, $nama, $guru_id, $id);
    if (!mysqli_stmt_execute($update)) {
        http_response_code(500);
        exit('Gagal memperbarui mata pelajaran.');
    }
    header('Location: ' . APP_BASE_URL . 'mapel/daftar.php');
    exit;
}
$query = mysqli_prepare($conn, 'SELECT * FROM mapel WHERE id = ?');
mysqli_stmt_bind_param($query, 'i', $id);
mysqli_stmt_execute($query);
$mapel = mysqli_fetch_assoc(mysqli_stmt_get_result($query));
if (!$mapel) {
    http_response_code(404);
    exit('Mata pelajaran tidak ditemukan.');
}
$guru = mysqli_query($conn, 'SELECT id, nama FROM guru ORDER BY nama');
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Mata Pelajaran</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><main class="container py-4"><h1 class="h3 mb-4">Edit Mata Pelajaran</h1>
<form method="post" class="card card-body gap-3">
<input type="hidden" name="id" value="<?= (int) $mapel['id']; ?>">
<label>Kode<input class="form-control" name="kode_mapel" required value="<?= htmlspecialchars($mapel['kode_mapel'], ENT_QUOTES, 'UTF-8'); ?>"></label>
<label>Nama<input class="form-control" name="nama_mapel" required value="<?= htmlspecialchars($mapel['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?>"></label>
<label>Guru Pengampu<select class="form-select" name="guru_id"><option value="">Belum ditentukan</option>
<?php while ($row = mysqli_fetch_assoc($guru)): ?><option value="<?= (int) $row['id']; ?>" <?= (int) $mapel['guru_id'] === (int) $row['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8'); ?></option><?php endwhile; ?>
</select></label><div><button class="btn btn-primary">Simpan Perubahan</button> <a class="btn btn-secondary" href="<?= APP_BASE_URL; ?>mapel/daftar.php">Batal</a></div>
</form></main></body></html>
