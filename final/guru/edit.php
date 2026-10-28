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
    exit('ID guru tidak valid.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $user_id = ($_POST['user_id'] ?? '') === '' ? null : filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if ($nip === '' || $nama === '' || !in_array($jenis_kelamin, ['L', 'P'], true)
        || (($_POST['user_id'] ?? '') !== '' && $user_id === false)) {
        http_response_code(400);
        exit('Data guru tidak valid.');
    }

    $update = mysqli_prepare($conn, 'UPDATE guru SET nip = ?, nama = ?, jenis_kelamin = ?, user_id = ? WHERE id = ?');
    mysqli_stmt_bind_param($update, 'sssii', $nip, $nama, $jenis_kelamin, $user_id, $id);
    if (!mysqli_stmt_execute($update)) {
        http_response_code(500);
        exit('Gagal memperbarui data guru.');
    }
    header('Location: ' . APP_BASE_URL . 'guru/daftar.php');
    exit;
}

$query = mysqli_prepare($conn, 'SELECT * FROM guru WHERE id = ?');
mysqli_stmt_bind_param($query, 'i', $id);
mysqli_stmt_execute($query);
$guru = mysqli_fetch_assoc(mysqli_stmt_get_result($query));
if (!$guru) {
    http_response_code(404);
    exit('Data guru tidak ditemukan.');
}
$users = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'guru' ORDER BY username");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Guru - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-4">
    <h1 class="h3 mb-4">Edit Data Guru</h1>
    <form method="post" class="card card-body gap-3">
        <input type="hidden" name="id" value="<?= (int) $guru['id']; ?>">
        <label>NIP<input class="form-control" name="nip" required value="<?= htmlspecialchars($guru['nip'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label>Nama<input class="form-control" name="nama" required value="<?= htmlspecialchars($guru['nama'], ENT_QUOTES, 'UTF-8'); ?>"></label>
        <label>Jenis Kelamin<select class="form-select" name="jenis_kelamin" required>
            <option value="L" <?= $guru['jenis_kelamin'] === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
            <option value="P" <?= $guru['jenis_kelamin'] === 'P' ? 'selected' : ''; ?>>Perempuan</option>
        </select></label>
        <label>Akun<select class="form-select" name="user_id">
            <option value="">Tanpa akun</option>
            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                <option value="<?= (int) $user['id']; ?>" <?= (int) $guru['user_id'] === (int) $user['id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endwhile; ?>
        </select></label>
        <div><button class="btn btn-primary">Simpan Perubahan</button>
            <a class="btn btn-secondary" href="<?= APP_BASE_URL; ?>guru/daftar.php">Batal</a></div>
    </form>
</main>
</body>
</html>
