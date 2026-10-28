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
    $nis   = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $jk    = $_POST['jenis_kelamin'];
    $uid   = $_POST['user_id'] ?: 'NULL';

    mysqli_query($conn, "INSERT INTO siswa (nis, nama, kelas, jenis_kelamin, user_id) VALUES ('$nis', '$nama', '$kelas', '$jk', $uid)");
    header("Location: " . APP_BASE_URL . "siswa/");
}

if (isset($_GET['hapus'])) {
    $id = filter_var($_GET['hapus'], FILTER_VALIDATE_INT);
    if (!$id) {
        $error = 'ID siswa tidak valid.';
    } else {
        $has_attendance = mysqli_prepare($conn, 'SELECT COUNT(*) AS total FROM absensi WHERE siswa_id = ?');
        mysqli_stmt_bind_param($has_attendance, 'i', $id);
        mysqli_stmt_execute($has_attendance);
        if ((int) mysqli_fetch_assoc(mysqli_stmt_get_result($has_attendance))['total'] > 0) {
            $error = 'Siswa tidak dapat dihapus karena sudah memiliki riwayat absensi.';
        } else {
            $delete = mysqli_prepare($conn, 'DELETE FROM siswa WHERE id = ?');
            mysqli_stmt_bind_param($delete, 'i', $id);
            mysqli_stmt_execute($delete);
            header("Location: " . APP_BASE_URL . "siswa/");
            exit;
        }
    }
}

$siswa = mysqli_query($conn, "SELECT s.*, u.username FROM siswa s LEFT JOIN users u ON s.user_id = u.id");
$users = mysqli_query($conn, "SELECT * FROM users WHERE role='siswa'");
include __DIR__ . '/header.php';
?>

<div class="container my-4">
    <div class="page-heading">
        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-2">ADMINISTRASI SEKOLAH</span>
        <h1 class="h2 mb-2">Kelola Data Siswa</h1>
        <p>Kelola informasi siswa dan akun SIPN yang terhubung.</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-1">Tambah Siswa</h2>
            <p class="text-secondary small mb-4">Lengkapi informasi berikut untuk menambahkan siswa baru.</p>
            <form method="POST" action="<?= APP_BASE_URL; ?>siswa/tambah.php" class="row g-3 align-items-end">
                <div class="col-12 col-md-6 col-xl-2">
                    <label for="nis" class="form-label fw-semibold">NIS</label>
                    <input type="text" id="nis" name="nis" class="form-control" placeholder="Nomor induk" required>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-control" placeholder="Nama siswa" required>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <label for="kelas" class="form-label fw-semibold">Kelas</label>
                    <input type="text" id="kelas" name="kelas" class="form-control" placeholder="Contoh: 7A" required>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <label for="jenis_kelamin" class="form-label fw-semibold">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-select">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="col-12 col-md-8 col-xl-2">
                    <label for="user_id" class="form-label fw-semibold">Akun SIPN</label>
                    <select id="user_id" name="user_id" class="form-select">
                        <option value="">Tanpa akun</option>
                        <?php while ($u = mysqli_fetch_assoc($users)): ?>
                            <option value="<?= (int) $u['id']; ?>"><?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12 col-xl-1"><button type="submit" name="tambah" class="btn btn-primary w-100">Simpan</button></div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-bold">Daftar Siswa</h2>
            <?php if ($error !== ''): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr><th>NIS</th><th>Nama</th><th>Kelas</th><th>Jenis Kelamin</th><th>Akun SIPN</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($siswa) === 0): ?>
                    <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data siswa.</td></tr>
                    <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($siswa)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nis'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="badge rounded-pill bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['kelas'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?= $row['jenis_kelamin'] === 'P' ? 'Perempuan' : 'Laki-laki'; ?></td>
                        <td><?= htmlspecialchars($row['username'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="<?= APP_BASE_URL; ?>siswa/edit.php?id=<?= (int) $row['id']; ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                            <a href="<?= APP_BASE_URL; ?>siswa/hapus.php?id=<?= (int) $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus siswa ini?')">Hapus</a>
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