<?php
session_start();
require_once __DIR__ . '/app_url.php';

// Jika belum login ATAU role-nya bukan admin, tendang ke login.php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . APP_BASE_URL . "auth/login.php");
    exit;
}

require_once __DIR__ . '/koneksi.php';

if (isset($_POST['tambah'])) {
    $mapel_id = $_POST['mapel_id'];
    $siswa_id = $_POST['siswa_id'];

    mysqli_query($conn, "INSERT INTO mengajar (mapel_id, siswa_id) VALUES ('$mapel_id', '$siswa_id')");
    header("Location: " . APP_BASE_URL . "mengajar/");
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM mengajar WHERE id='$id'");
    header("Location: " . APP_BASE_URL . "mengajar/");
}

$mengajar = mysqli_query($conn, "SELECT mg.id, m.nama_mapel, s.nama AS nama_siswa, s.kelas 
                                 FROM mengajar mg 
                                 JOIN mapel m ON mg.mapel_id = m.id 
                                 JOIN siswa s ON mg.siswa_id = s.id");
$mapel = mysqli_query($conn, "SELECT * FROM mapel");
$siswa = mysqli_query($conn, "SELECT * FROM siswa");
include __DIR__ . '/header.php';
?>

<div class="container my-4">
    <div class="page-heading">
        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-2">ADMINISTRASI SEKOLAH</span>
        <h1 class="h2 mb-2">Penugasan Mengajar</h1>
        <p>Hubungkan mata pelajaran dengan siswa yang mengikuti pembelajaran.</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-1">Buat Penugasan</h2>
            <p class="text-secondary small mb-4">Pilih mata pelajaran dan siswa untuk membuat penugasan.</p>
            <form method="POST" action="<?= APP_BASE_URL; ?>mengajar/tambah.php" class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label for="mapel_id" class="form-label fw-semibold">Mata Pelajaran</label>
                    <select id="mapel_id" name="mapel_id" class="form-select" required>
                        <option value="">Pilih mata pelajaran</option>
                        <?php while ($m = mysqli_fetch_assoc($mapel)): ?>
                            <option value="<?= (int) $m['id']; ?>"><?= htmlspecialchars($m['kode_mapel'] . ' - ' . $m['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label for="siswa_id" class="form-label fw-semibold">Siswa</label>
                    <select id="siswa_id" name="siswa_id" class="form-select" required>
                        <option value="">Pilih siswa</option>
                        <?php while ($s = mysqli_fetch_assoc($siswa)): ?>
                            <option value="<?= (int) $s['id']; ?>"><?= htmlspecialchars($s['nis'] . ' - ' . $s['nama'] . ' (' . $s['kelas'] . ')', ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2"><button type="submit" name="tambah" class="btn btn-primary w-100">Simpan</button></div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-bold">Daftar Penugasan</h2>
            <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr><th>No</th><th>Mata Pelajaran</th><th>Siswa</th><th>Kelas</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($mengajar) === 0): ?>
                    <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada penugasan mengajar.</td></tr>
                    <?php else: ?>
                    <?php $no=1; while ($row = mysqli_fetch_assoc($mengajar)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['nama_siswa'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="badge rounded-pill bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['kelas'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td>
                            <a href="<?= APP_BASE_URL; ?>mengajar/edit.php?id=<?= (int) $row['id']; ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                            <a href="<?= APP_BASE_URL; ?>mengajar/hapus.php?id=<?= (int) $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
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