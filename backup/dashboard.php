<?php
session_start();
require_once __DIR__ . '/app_url.php';
require_once __DIR__ . '/koneksi.php';

if (!isset($_SESSION['role'])) {
    header("Location: " . APP_BASE_URL . "auth/login.php");
    exit;
}

// Jika role Admin, arahkan otomatis ke Dashboard Admin
if ($_SESSION['role'] === 'admin') {
    header("Location: " . APP_BASE_URL . "dashboard_admin.php");
    exit;
}

include __DIR__ . '/header.php';
?>

<main class="container my-4">
    <?php $role_class = $_SESSION['role'] === 'siswa' ? 'student-welcome' : 'teacher-welcome'; ?>
    <section class="card dashboard-hero role-welcome <?= $role_class; ?> mb-4">
        <div class="card-body p-4 p-md-5 text-white">
            <?php if ($_SESSION['role'] === 'siswa'): ?>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="badge bg-white text-primary mb-3">RUANG SISWA</span>
                        <h1 class="h2 fw-bold mb-2">Selamat datang, <?= htmlspecialchars(ucfirst($_SESSION['username'])); ?>!</h1>
                        <p class="mb-0 text-white-50">Siap belajar hal baru hari ini? Periksa perkembangan belajarmu di sini.</p>
                    </div>
                    <a class="btn btn-light text-primary px-4" href="<?= APP_BASE_URL; ?>nilai/lihat.php">Lihat Nilai</a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="badge bg-white text-primary mb-3">RUANG GURU</span>
                        <h1 class="h2 fw-bold mb-2">Selamat datang, <?= htmlspecialchars(ucfirst($_SESSION['username'])); ?>!</h1>
                        <p class="mb-0 text-white-50">Ruang kerja untuk mengelola nilai dan mendampingi kemajuan siswa.</p>
                    </div>
                    <a class="btn btn-light text-primary px-4" href="<?= APP_BASE_URL; ?>nilai/">Kelola Nilai</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($_SESSION['role'] === 'siswa'): ?>
        <section class="student-note card border-0 mb-4">
            <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center gap-3">
                <div class="student-note-icon">✦</div>
                <div>
                    <h2 class="h5 fw-bold mb-1">Setiap langkah belajar itu berarti</h2>
                    <p class="text-secondary mb-0">Lihat rapormu secara berkala dan terus semangat meraih hasil terbaik.</p>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="teacher-note card border-0 mb-4">
            <div class="card-body p-4">
                <span class="teacher-note-label">CATATAN HARI INI</span>
                <p class="mb-0 mt-2">“Pendidikan adalah kunci untuk membuka dunia.” <span class="text-secondary">— Catatan pengajar</span></p>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/footer.php'; ?>