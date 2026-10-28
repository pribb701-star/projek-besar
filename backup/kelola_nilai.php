<?php
session_start();
require_once __DIR__ . '/app_url.php';
require_once __DIR__ . '/koneksi.php';

// Akses khusus Admin dan Guru
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'guru'])) {
    header("Location: " . APP_BASE_URL . "dashboard.php");
    exit;
}

// Simpan / Edit Data Nilai
if (isset($_POST['simpan'])) {
    $siswa_id           = $_POST['siswa_id'];
    $mapel_id           = $_POST['mapel_id'];
    $nilai_kkm          = $_POST['nilai_kkm'];
    $nilai_pengetahuan  = $_POST['nilai_pengetahuan'];
    $nilai_keterampilan = $_POST['nilai_keterampilan'];

    $rata_rata  = ($nilai_pengetahuan + $nilai_keterampilan) / 2;
    $keterangan = ($rata_rata >= $nilai_kkm) ? "LUNAS / TUNTAS" : "REMIDI";

    // Cek apakah nilai siswa pada mapel tersebut sudah ada
    $cek = mysqli_query($conn, "SELECT id FROM nilai_rapor WHERE siswa_id='$siswa_id' AND mapel_id='$mapel_id'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE nilai_rapor SET nilai_kkm='$nilai_kkm', nilai_pengetahuan='$nilai_pengetahuan', nilai_keterampilan='$nilai_keterampilan', keterangan='$keterangan' WHERE siswa_id='$siswa_id' AND mapel_id='$mapel_id'");
    } else {
        mysqli_query($conn, "INSERT INTO nilai_rapor (siswa_id, mapel_id, nilai_kkm, nilai_pengetahuan, nilai_keterampilan, keterangan) 
                             VALUES ('$siswa_id', '$mapel_id', '$nilai_kkm', '$nilai_pengetahuan', '$nilai_keterampilan', '$keterangan')");
    }
    header("Location: " . APP_BASE_URL . "nilai/");
    exit;
}

// Hapus Data Nilai
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM nilai_rapor WHERE id='$id'");
    header("Location: " . APP_BASE_URL . "nilai/");
    exit;
}

// Ambil Data Rekap Nilai
$nilai = mysqli_query($conn, "SELECT nr.*, s.nama AS nama_siswa, s.nis, m.nama_mapel 
                              FROM nilai_rapor nr 
                              JOIN siswa s ON nr.siswa_id = s.id 
                              JOIN mapel m ON nr.mapel_id = m.id 
                              ORDER BY s.nama ASC");

// Ambil Data Dropdown
$siswa = mysqli_query($conn, "SELECT * FROM siswa ORDER BY nama ASC");
$mapel = mysqli_query($conn, "SELECT * FROM mapel ORDER BY nama_mapel ASC");

include __DIR__ . '/header.php';
?>

<div class="container my-4">
    <div class="page-heading">
        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-2">AKADEMIK</span>
        <h1 class="h2 mb-2">Pengelolaan Nilai</h1>
        <p>Input, tinjau, dan kelola rekap nilai rapor siswa.</p>
    </div>

    <!-- Form Input Nilai -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-1">Input Nilai Siswa</h2>
            <p class="text-secondary small mb-4">Pilih siswa dan mata pelajaran, lalu masukkan nilai.</p>
            <form method="POST" action="<?= APP_BASE_URL; ?>nilai/tambah.php" class="row g-3">
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="siswa_id" class="form-label fw-semibold">Pilih Siswa</label>
                    <select id="siswa_id" name="siswa_id" class="form-select" required>
                        <option value="">Pilih siswa</option>
                        <?php while ($s = mysqli_fetch_assoc($siswa)): ?>
                            <option value="<?= (int) $s['id']; ?>"><?= htmlspecialchars($s['nama'] . ' (' . $s['nis'] . ')', ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="mapel_id" class="form-label fw-semibold">Pilih Mapel</label>
                    <select id="mapel_id" name="mapel_id" class="form-select" required>
                        <option value="">Pilih mata pelajaran</option>
                        <?php while ($m = mysqli_fetch_assoc($mapel)): ?>
                            <option value="<?= (int) $m['id']; ?>"><?= htmlspecialchars($m['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <label for="nilai_kkm" class="form-label fw-semibold">KKM</label>
                    <input type="number" id="nilai_kkm" name="nilai_kkm" class="form-control" value="75" min="0" max="100" required>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <label for="nilai_pengetahuan" class="form-label fw-semibold">Pengetahuan</label>
                    <input type="number" id="nilai_pengetahuan" name="nilai_pengetahuan" class="form-control" placeholder="0-100" min="0" max="100" required>
                </div>
                <div class="col-12 col-md-4 col-xl-2">
                    <label for="nilai_keterampilan" class="form-label fw-semibold">Keterampilan</label>
                    <input type="number" id="nilai_keterampilan" name="nilai_keterampilan" class="form-control" placeholder="0-100" min="0" max="100" required>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" name="simpan" class="btn btn-success px-4">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Rekap Nilai -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-1">Rekap Nilai Rapor</h2>
            <p class="text-secondary small mb-4">Ringkasan pencapaian siswa di setiap mata pelajaran.</p>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Siswa</th>
                            <th>Mapel</th>
                            <th>KKM</th>
                            <th>Pengetahuan</th>
                            <th>Keterampilan</th>
                            <th>Rata-Rata</th>
                            <th>Predikat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($nilai) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($nilai)): 
                                $avg = ($row['nilai_pengetahuan'] + $row['nilai_keterampilan']) / 2;
                                
                                if ($avg >= 90) {
                                    $predikat = 'A';
                                } elseif ($avg >= 80) {
                                    $predikat = 'B';
                                } elseif ($avg >= 70) {
                                    $predikat = 'C';
                                } else {
                                    $predikat = 'D';
                                }

                                $status = ($avg >= $row['nilai_kkm']) ? '<span class="badge bg-success">TUNTAS</span>' : '<span class="badge bg-danger">BELUM TUNTAS</span>';
                            ?>
                            <tr>
                                <td><b><?= htmlspecialchars($row['nama_siswa'], ENT_QUOTES, 'UTF-8'); ?></b> <br><small class="text-muted">NIS: <?= htmlspecialchars($row['nis'], ENT_QUOTES, 'UTF-8'); ?></small></td>
                                <td><?= htmlspecialchars($row['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-center"><?= $row['nilai_kkm']; ?></td>
                                <td class="text-center"><?= $row['nilai_pengetahuan']; ?></td>
                                <td class="text-center"><?= $row['nilai_keterampilan']; ?></td>
                                <td class="text-center fw-bold"><?= number_format($avg, 1); ?></td>
                                <td class="text-center fw-bold text-primary"><?= $predikat; ?></td>
                                <td class="text-center"><?= $status; ?></td>
                                <td class="text-center">
                                    <a href="<?= APP_BASE_URL; ?>nilai/edit.php?id=<?= (int) $row['id']; ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                                    <a href="<?= APP_BASE_URL; ?>nilai/hapus.php?id=<?= (int) $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data nilai ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted">Belum ada data nilai.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>