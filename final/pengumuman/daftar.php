<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['guru', 'siswa'], true)) {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}

$create_table = "CREATE TABLE IF NOT EXISTS pengumuman (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    judul VARCHAR(180) NOT NULL,
    isi TEXT NOT NULL,
    author_id INT NULL,
    dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_pengumuman_dibuat_pada (dibuat_pada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if (!mysqli_query($conn, $create_table)) {
    http_response_code(500);
    exit('Gagal menyiapkan penyimpanan pengumuman: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'));
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['role'] !== 'guru') {
        http_response_code(403);
        exit('Hanya guru yang dapat membuat pengumuman.');
    }
    $title = trim($_POST['judul'] ?? '');
    $body = trim($_POST['isi'] ?? '');
    if ($title === '' || strlen($title) > 720 || $body === '') {
        $error = 'Judul wajib diisi (maksimal 180 karakter) dan isi pengumuman tidak boleh kosong.';
    } else {
        $insert = mysqli_prepare($conn, 'INSERT INTO pengumuman (judul, isi, author_id) VALUES (?, ?, ?)');
        if (!$insert) {
            http_response_code(500);
            exit('Gagal menyiapkan penyimpanan pengumuman.');
        }
        $author_id = (int) $_SESSION['user_id'];
        mysqli_stmt_bind_param($insert, 'ssi', $title, $body, $author_id);
        if (!mysqli_stmt_execute($insert)) {
            http_response_code(500);
            exit('Gagal menyimpan pengumuman: ' . htmlspecialchars(mysqli_stmt_error($insert), ENT_QUOTES, 'UTF-8'));
        }
        header('Location: ' . APP_BASE_URL . 'pengumuman/daftar.php?tersimpan=1');
        exit;
    }
}

$announcements = mysqli_query(
    $conn,
    'SELECT pengumuman.id, pengumuman.judul, pengumuman.isi, pengumuman.dibuat_pada, users.username AS nama_guru
     FROM pengumuman
     LEFT JOIN users ON users.id = pengumuman.author_id
     ORDER BY pengumuman.dibuat_pada DESC, pengumuman.id DESC'
);
if (!$announcements) {
    http_response_code(500);
    exit('Gagal memuat pengumuman: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'));
}
$is_teacher = $_SESSION['role'] === 'guru';
$saved = isset($_GET['tersimpan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengumuman - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-4 py-lg-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <a href="<?= APP_BASE_URL; ?>dashboard.php" class="text-decoration-none">&larr; Dashboard</a>
            <h1 class="h2 mt-2 mb-0">Pengumuman</h1>
            <p class="text-secondary mb-0"><?= $is_teacher ? 'Buat pengumuman untuk siswa.' : 'Informasi terbaru dari guru.'; ?></p>
        </div>
    </div>

    <?php if ($saved): ?><div class="alert alert-success">Pengumuman berhasil diterbitkan.</div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>

    <?php if ($is_teacher): ?>
        <form method="post" class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Tambah Pengumuman</h2>
                <label class="form-label w-100">Judul
                    <input class="form-control mt-1" name="judul" maxlength="180" required value="<?= htmlspecialchars($_POST['judul'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </label>
                <label class="form-label w-100">Isi pengumuman
                    <textarea class="form-control mt-1" name="isi" rows="4" required><?= htmlspecialchars($_POST['isi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </label>
                <button class="btn btn-primary" type="submit">Terbitkan</button>
            </div>
        </form>
    <?php endif; ?>

    <section class="d-grid gap-3">
        <?php if (mysqli_num_rows($announcements) === 0): ?>
            <div class="card border-0 shadow-sm"><div class="card-body text-center text-secondary py-5">Belum ada pengumuman.</div></div>
        <?php else: while ($announcement = mysqli_fetch_assoc($announcements)): ?>
            <article class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-2"><?= htmlspecialchars($announcement['judul'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="small text-secondary">Oleh <?= htmlspecialchars($announcement['nama_guru'] ?? 'Guru', ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars($announcement['dibuat_pada'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="mb-0"><?= nl2br(htmlspecialchars($announcement['isi'], ENT_QUOTES, 'UTF-8')); ?></div>
                </div>
            </article>
        <?php endwhile; endif; ?>
    </section>
</main>
</body>
</html>
