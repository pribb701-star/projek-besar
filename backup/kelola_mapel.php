<?php
session_start();
require_once __DIR__ . '/app_url.php';

// Jika belum login ATAU role-nya bukan admin, tendang ke login.php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . APP_BASE_URL . "auth/login.php");
    exit;
}

require_once __DIR__ . '/koneksi.php';

$error = '';
if (!empty($_SESSION['crud_error'])) {
    $error = $_SESSION['crud_error'];
    unset($_SESSION['crud_error']);
}

if (isset($_POST['tambah'])) {
    $kode    = mysqli_real_escape_string($conn, $_POST['kode_mapel']);
    $nama    = mysqli_real_escape_string($conn, $_POST['nama_mapel']);
    
    $guru_id = $_POST['guru_id'] ?: 'NULL';

    mysqli_query($conn, "INSERT INTO mapel (kode_mapel, nama_mapel, guru_id) VALUES ('$kode', '$nama', $guru_id)");
    header("Location: " . APP_BASE_URL . "mapel/");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    $scheduled = mysqli_query($conn, "SELECT COUNT(*) AS total FROM jadwal_pelajaran WHERE mapel_id='$id'");
    if ((int) mysqli_fetch_assoc($scheduled)['total'] > 0) {
        $error = 'Mata pelajaran tidak dapat dihapus karena masih digunakan pada jadwal.';
    } else {
        mysqli_query($conn, "DELETE FROM mapel WHERE id='$id'");
        header("Location: " . APP_BASE_URL . "mapel/");
        exit;
    }
}

$mapel = mysqli_query($conn, "SELECT m.*, g.nama AS nama_guru FROM mapel m LEFT JOIN guru g ON m.guru_id = g.id");
$guru  = mysqli_query($conn, "SELECT * FROM guru");
include __DIR__ . '/header.php';
?>

<div class="container my-4">
    <div class="page-heading">
        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-2">ADMINISTRASI SEKOLAH</span>
        <h1 class="h2 mb-2">Mata Pelajaran</h1>
        <p>Atur daftar mata pelajaran dan guru pengampu.</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-1">Tambah Mata Pelajaran</h2>
            <p class="text-secondary small mb-4">Masukkan kode, nama mata pelajaran, dan guru pengampu.</p>
            <form method="POST" action="<?= APP_BASE_URL; ?>mapel/tambah.php" class="row g-3 align-items-end">
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="kode_mapel" class="form-label fw-semibold">Kode Mapel</label>
                    <input type="text" id="kode_mapel" name="kode_mapel" class="form-control" placeholder="Contoh: MP01" required>
                </div>
                <div class="col-12 col-md-6 col-xl-4">
                    <label for="nama_mapel" class="form-label fw-semibold">Nama Mapel</label>
                    <input type="text" id="nama_mapel" name="nama_mapel" class="form-control" placeholder="Contoh: Matematika" required>
                </div>
                <div class="col-12 col-md-8 col-xl-3">
                    <label for="guru_id" class="form-label fw-semibold">Guru Pengampu</label>
                    <select id="guru_id" name="guru_id" class="form-select">
                        <option value="">Belum ditentukan</option>
                        <?php while ($g = mysqli_fetch_assoc($guru)): ?>
                            <option value="<?= $g['id']; ?>" <?= ($guru_login_id == $g['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($g['nama'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <button type="submit" name="tambah" class="btn btn-primary w-100">Simpan Mapel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-bold">Daftar Mata Pelajaran</h2>
            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Mapel</th>
                        <th>Guru Pengampu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($mapel) === 0): ?>
                    <tr><td colspan="4" class="text-center text-secondary py-4">Belum ada mata pelajaran.</td></tr>
                    <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($mapel)): ?>
                    <tr>
                        <td><span class="badge rounded-pill bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['kode_mapel'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['nama_guru'] ?? 'Belum ditentukan', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="<?= APP_BASE_URL; ?>mapel/edit.php?id=<?= (int) $row['id']; ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                            <a href="<?= APP_BASE_URL; ?>mapel/hapus.php?id=<?= (int) $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus mapel ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>