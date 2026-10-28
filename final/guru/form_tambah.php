<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}

$users = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'guru' ORDER BY username");
if (!$users) {
    http_response_code(500);
    exit('Gagal memuat akun guru: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Guru - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-4 py-lg-5" style="max-width: 760px">
    <a href="<?= APP_BASE_URL; ?>guru/daftar.php" class="text-decoration-none">&larr; Kembali ke daftar guru</a>
    <h1 class="h2 mt-3 mb-4">Tambah Guru</h1>
    <form method="post" action="<?= APP_BASE_URL; ?>guru/tambah.php" class="card border-0 shadow-sm">
        <div class="card-body p-4 d-grid gap-3">
            <label class="form-label mb-0">NIP
                <input class="form-control mt-1" name="nip" required>
            </label>
            <label class="form-label mb-0">Nama guru
                <input class="form-control mt-1" name="nama" required>
            </label>
            <label class="form-label mb-0">Jenis kelamin
                <select class="form-select mt-1" name="jenis_kelamin" required>
                    <option value="">Pilih jenis kelamin</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </label>
            <label class="form-label mb-0">Akun login (opsional)
                <select class="form-select mt-1" name="user_id">
                    <option value="">Tanpa akun</option>
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <option value="<?= (int) $user['id']; ?>"><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endwhile; ?>
                </select>
            </label>
            <div class="d-flex gap-2 mt-2">
                <button class="btn btn-danger" type="submit">Simpan Guru</button>
                <a class="btn btn-outline-secondary" href="<?= APP_BASE_URL; ?>guru/daftar.php">Batal</a>
            </div>
        </div>
    </form>
</main>
</body>
</html>
