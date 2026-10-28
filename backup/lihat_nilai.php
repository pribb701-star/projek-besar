<?php
session_start();
require_once __DIR__ . '/app_url.php';
require_once __DIR__ . '/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') { header("Location: " . APP_BASE_URL . "dashboard.php"); exit; }

$user_id = $_SESSION['user_id'];
$get_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE user_id='$user_id'");
$siswa = mysqli_fetch_assoc($get_siswa);

$siswa_id = $siswa['id'] ?? 0;
$nilai = mysqli_query($conn, "SELECT nr.*, m.nama_mapel, m.kode_mapel 
                              FROM nilai_rapor nr 
                              JOIN mapel m ON nr.mapel_id = m.id 
                              WHERE nr.siswa_id='$siswa_id'");
include __DIR__ . '/header.php';
?>

<div class="container my-4">
    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary">Cetak Rapor (PDF)</button>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="text-center text-white p-4 p-md-5" style="background: linear-gradient(120deg, #1e3a8a, #2563eb 58%, #60a5fa);">
            <span class="badge bg-white bg-opacity-25 text-white mb-3">SIPN · RAPOR AKADEMIK</span>
            <h1 class="h2 fw-bold mb-2">Rapor Nilai Siswa</h1>
            <p class="text-white-50 mb-0">Ringkasan hasil belajar dan pencapaian akademik.</p>
        </div>
        <div class="card-body p-4 p-md-5">
        
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="rounded-4 bg-light p-3 h-100">
                    <small class="text-secondary d-block mb-1">NAMA SISWA</small>
                    <strong><?= htmlspecialchars($siswa['nama'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="rounded-4 bg-light p-3 h-100">
                    <small class="text-secondary d-block mb-1">NIS</small>
                    <strong><?= htmlspecialchars($siswa['nis'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="rounded-4 bg-light p-3 h-100">
                    <small class="text-secondary d-block mb-1">KELAS</small>
                    <strong><?= htmlspecialchars($siswa['kelas'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            </div>
            <div class="col-12 col-md-2">
                <div class="rounded-4 bg-light p-3 h-100">
                    <small class="text-secondary d-block mb-1">JENIS KELAMIN</small>
                    <strong><?= htmlspecialchars($siswa['jenis_kelamin'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            </div>
        </div>

        <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr><th>Kode</th><th>Mata Pelajaran</th><th>KKM</th><th>Pengetahuan</th><th>Keterampilan</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($nilai) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($nilai)): ?>
                    <tr>
                        <td><span class="badge rounded-pill bg-primary bg-opacity-10 text-primary"><?= htmlspecialchars($row['kode_mapel'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['nama_mapel'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= $row['nilai_kkm']; ?></td>
                        <td><?= $row['nilai_pengetahuan']; ?></td>
                        <td><?= $row['nilai_keterampilan']; ?></td>
                        <td><b><?= htmlspecialchars($row['keterangan'], ENT_QUOTES, 'UTF-8'); ?></b></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted">Belum ada nilai yang diinputkan oleh guru.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>