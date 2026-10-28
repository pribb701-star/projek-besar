<?php
session_start();
require_once __DIR__ . '/koneksi/koneksi.php';

$list_type = $list_type ?? '';
$definitions = [
    'user' => [
        'title' => 'Daftar Pengguna',
        'roles' => ['admin'],
        'columns' => ['ID', 'Username', 'Peran'],
        'sql' => 'SELECT id, username, role FROM users ORDER BY id DESC',
        'edit' => 'user/edit.php',
        'delete' => 'user/hapus.php',
    ],
    'siswa' => [
        'title' => 'Daftar Siswa',
        'roles' => ['admin'],
        'columns' => ['ID', 'NIS', 'Nama', 'Kelas', 'Jenis Kelamin'],
        'sql' => 'SELECT id, nis, nama, kelas, jenis_kelamin FROM siswa ORDER BY nama',
        'edit' => 'siswa/edit.php',
        'delete' => 'siswa/hapus.php',
    ],
    'guru' => [
        'title' => 'Daftar Guru',
        'roles' => ['admin'],
        'columns' => ['ID', 'NIP', 'Nama', 'Jenis Kelamin'],
        'sql' => 'SELECT id, nip, nama, jenis_kelamin FROM guru ORDER BY nama',
        'edit' => 'guru/edit.php',
        'delete' => 'guru/hapus.php',
    ],
    'mapel' => [
        'title' => 'Daftar Mata Pelajaran',
        'roles' => ['admin'],
        'columns' => ['ID', 'Kode', 'Nama Mata Pelajaran', 'ID Guru'],
        'sql' => 'SELECT id, kode_mapel, nama_mapel, guru_id FROM mapel ORDER BY nama_mapel',
        'edit' => 'mapel/edit.php',
        'delete' => 'mapel/hapus.php',
    ],
    'mengajar' => [
        'title' => 'Daftar Penugasan Mengajar',
        'roles' => ['admin'],
        'columns' => ['ID', 'Mata Pelajaran', 'Guru Pengajar', 'Siswa'],
        'sql' => 'SELECT mengajar.id, mapel.nama_mapel, COALESCE(guru.nama, \'Belum ditentukan\'), siswa.nama FROM mengajar JOIN mapel ON mapel.id = mengajar.mapel_id LEFT JOIN guru ON guru.id = mengajar.guru_id JOIN siswa ON siswa.id = mengajar.siswa_id ORDER BY mengajar.id DESC',
        'edit' => 'mengajar/edit.php',
        'delete' => 'mengajar/hapus.php',
    ],
    'nilai' => [
        'title' => 'Daftar Nilai',
        'roles' => ['guru'],
        'columns' => ['ID', 'Siswa', 'Mata Pelajaran', 'KKM', 'Pengetahuan', 'Keterampilan', 'Keterangan'],
        'sql' => 'SELECT nilai_rapor.id, siswa.nama, mapel.nama_mapel, nilai_rapor.nilai_kkm, nilai_rapor.nilai_pengetahuan, nilai_rapor.nilai_keterampilan, nilai_rapor.keterangan FROM nilai_rapor JOIN siswa ON siswa.id = nilai_rapor.siswa_id JOIN mapel ON mapel.id = nilai_rapor.mapel_id ORDER BY siswa.nama, mapel.nama_mapel',
        'edit' => 'nilai/edit.php',
        'delete' => 'nilai/hapus.php',
        'show_actions' => false,
    ],
];

if (!isset($_SESSION['role'])) {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
if (!isset($definitions[$list_type])) {
    http_response_code(404);
    exit('Halaman daftar tidak ditemukan.');
}
$definition = $definitions[$list_type];
if (!in_array($_SESSION['role'], $definition['roles'], true)) {
    http_response_code(403);
    exit('Anda tidak memiliki akses ke halaman ini.');
}
$result = mysqli_query($conn, $definition['sql']);
if (!$result) {
    http_response_code(500);
    exit('Gagal memuat daftar: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'));
}
$rows = [];
while ($row = mysqli_fetch_row($result)) {
    $rows[] = $row;
}
$error = $_SESSION['crud_error'] ?? '';
unset($_SESSION['crud_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($definition['title'], ENT_QUOTES, 'UTF-8'); ?> - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <a href="<?= APP_BASE_URL . ($_SESSION['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard.php'); ?>" class="text-decoration-none">&larr; Dashboard</a>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2">
                <h1 class="h3 mb-0"><?= htmlspecialchars($definition['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <?php if ($list_type === 'mengajar'): ?>
                    <a href="<?= APP_BASE_URL; ?>mengajar/form_tambah.php" class="btn btn-primary">+ Tambah Data</a>
                <?php endif; ?>
            </div>
        </div>
        <a class="btn btn-outline-secondary" href="<?= APP_BASE_URL; ?>login/logout.php">Keluar</a>
    </div>
    <?php if ($list_type === 'nilai' && $_SESSION['role'] === 'guru'): ?>
        <p><a class="btn btn-primary" href="<?= APP_BASE_URL; ?>nilai/form_tambah.php">+ Tambah Nilai</a></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?><div class="alert alert-warning"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
    <div class="card border-0 shadow-sm"><div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead><tr><?php foreach ($definition['columns'] as $column): ?><th><?= htmlspecialchars($column, ENT_QUOTES, 'UTF-8'); ?></th><?php endforeach; ?><?php if ($definition['show_actions'] ?? true): ?><th>Aksi</th><?php endif; ?></tr></thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="<?= count($definition['columns']) + (($definition['show_actions'] ?? true) ? 1 : 0); ?>" class="text-center text-secondary py-4">Belum ada data.</td></tr>
            <?php else: foreach ($rows as $row): $id = (int) $row[0]; ?>
                <tr>
                    <?php foreach ($row as $value): ?><td><?= htmlspecialchars((string) ($value ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td><?php endforeach; ?>
                    <?php if ($definition['show_actions'] ?? true): ?><td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="<?= APP_BASE_URL . $definition['edit']; ?>?id=<?= $id; ?>">Edit</a>
                        <a class="btn btn-sm btn-outline-danger" href="<?= APP_BASE_URL . $definition['delete']; ?>?id=<?= $id; ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                    </td><?php endif; ?>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div></div>
</main>
</body>
</html>
