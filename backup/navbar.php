<?php
if (!isset($dashboard_url)) {
    $dashboard_url = isset($_SESSION['role']) && $_SESSION['role'] === 'admin'
        ? APP_BASE_URL . 'dashboard_admin.php'
        : APP_BASE_URL . 'dashboard.php';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top no-print">
    <div class="container">
        <a class="navbar-brand fw-bold me-lg-4" href="<?= $dashboard_url; ?>">
            SIPN
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Buka navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $dashboard_url; ?>">Dashboard</a>
                </li>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">Data & Pengelolaan</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= APP_BASE_URL; ?>user/">Kelola User</a></li>
                            <li><a class="dropdown-item" href="<?= APP_BASE_URL; ?>siswa/">Siswa</a></li>
                            <li><a class="dropdown-item" href="<?= APP_BASE_URL; ?>guru/">Guru</a></li>
                            <li><a class="dropdown-item" href="<?= APP_BASE_URL; ?>mapel/">Mata Pelajaran</a></li>
                            <li><a class="dropdown-item" href="<?= APP_BASE_URL; ?>mengajar/">Mengajar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= APP_BASE_URL; ?>nilai/">Kelola Nilai</a></li>
                        </ul>
                    </li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'guru'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL; ?>nilai/">Kelola Nilai</a></li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'siswa'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL; ?>nilai/lihat.php">Lihat Nilai</a></li>
                <?php endif; ?>
            </ul>

            <?php if (isset($_SESSION['username'])): ?>
                <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 mt-3 mt-lg-0">
                    <a class="nav-link text-white py-1" href="<?= APP_BASE_URL; ?>profile/">
                        <?= htmlspecialchars($_SESSION['username']); ?>
                        <span class="opacity-75">· <?= ucfirst($_SESSION['role']); ?></span>
                    </a>
                    <a class="btn btn-outline-light btn-sm" href="<?= APP_BASE_URL; ?>auth/logout.php">Logout</a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column flex-lg-row gap-2 mt-3 mt-lg-0">
                    <a class="btn btn-light btn-sm text-primary" href="<?= APP_BASE_URL; ?>auth/login.php">Masuk</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
