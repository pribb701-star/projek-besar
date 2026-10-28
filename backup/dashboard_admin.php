<?php
session_start();
require_once __DIR__ . '/app_url.php';

// Jika belum login ATAU role-nya bukan admin, tendang ke login.php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . APP_BASE_URL . "auth/login.php");
    exit;
}

require_once __DIR__ . '/koneksi.php';

// Hitung Total Data (Statistik Dashboard Admin)
$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM siswa"))['total'] ?? 0;
$total_guru  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM guru"))['total'] ?? 0;
$total_mapel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM mapel"))['total'] ?? 0;
$total_user  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'] ?? 0;

include __DIR__ . '/header.php';
?>

<main class="container my-4">
    <div class="dashboard-hero card mb-4">
        <div class="card-body p-4 p-md-5 text-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-white text-primary mb-3">ADMIN PANEL</span>
                <h1 class="h2 mb-2">Dashboard Administrator</h1>
                <p class="mb-0 text-white-50">Selamat datang kembali, <b class="text-white"><?= htmlspecialchars($_SESSION['username']); ?></b>. Kelola data sekolah dari satu tempat.</p>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <small>Total Siswa</small>
                    <h2 class="mb-0 mt-2"><?= number_format($total_siswa); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <small>Total Guru</small>
                    <h2 class="mb-0 mt-2"><?= number_format($total_guru); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <small>Mata Pelajaran</small>
                    <h2 class="mb-0 mt-2"><?= number_format($total_mapel); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <small>Pengguna</small>
                    <h2 class="mb-0 mt-2"><?= number_format($total_user); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <h2 class="h5 mb-0">Akses Cepat Pengelolaan Data</h2>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= APP_BASE_URL; ?>user/" class="btn btn-outline-primary">Kelola User</a>
                <a href="<?= APP_BASE_URL; ?>siswa/" class="btn btn-outline-primary">Kelola Siswa</a>
                <a href="<?= APP_BASE_URL; ?>guru/" class="btn btn-outline-primary">Kelola Guru</a>
                <a href="<?= APP_BASE_URL; ?>mapel/" class="btn btn-outline-primary">Kelola Mapel</a>
                <a href="<?= APP_BASE_URL; ?>mengajar/" class="btn btn-outline-primary">Kelola Mengajar</a>
                <a href="<?= APP_BASE_URL; ?>nilai/" class="btn btn-outline-primary">Kelola Nilai</a>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>