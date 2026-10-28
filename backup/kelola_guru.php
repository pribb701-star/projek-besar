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
    $nip  = mysqli_real_escape_string($conn, $_POST['nip']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jk   = $_POST['jenis_kelamin'];
    $uid  = $_POST['user_id'] ?: 'NULL';

    mysqli_query($conn, "INSERT INTO guru (nip, nama, jenis_kelamin, user_id) VALUES ('$nip', '$nama', '$jk', $uid)");
    header("Location: " . APP_BASE_URL . "guru/");
}

if (isset($_GET['hapus'])) {
    $id = filter_var($_GET['hapus'], FILTER_VALIDATE_INT);
    if (!$id) {
        $error = 'ID guru tidak valid.';
    } else {
        $scheduled = mysqli_prepare($conn, 'SELECT COUNT(*) AS total FROM mapel m JOIN jadwal_pelajaran jp ON jp.mapel_id = m.id WHERE m.guru_id = ?');
        mysqli_stmt_bind_param($scheduled, 'i', $id);
        mysqli_stmt_execute($scheduled);
        if ((int) mysqli_fetch_assoc(mysqli_stmt_get_result($scheduled))['total'] > 0) {
            $error = 'Guru tidak dapat dihapus karena masih memiliki jadwal pelajaran.';
        } else {
            $delete = mysqli_prepare($conn, 'DELETE FROM guru WHERE id = ?');
            mysqli_stmt_bind_param($delete, 'i', $id);
            mysqli_stmt_execute($delete);
            header("Location: " . APP_BASE_URL . "guru/");
            exit;
        }
    }
}

$guru  = mysqli_query($conn, "SELECT g.*, u.username FROM guru g LEFT JOIN users u ON g.user_id = u.id");
$users = mysqli_query($conn, "SELECT * FROM users WHERE role='guru'");
include __DIR__ . '/header.php';
?>

<div class="container my-4">
    <style>
        .teacher-heading {
            background: linear-gradient(120deg, #1e3a8a, #2563eb 58%, #60a5fa);
            border-radius: 24px;
            box-shadow: 0 18px 35px rgba(30, 64, 175, 0.2);
        }

        .teacher-panel {
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }

        .teacher-panel .form-label {
            color: #475569;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .teacher-panel .form-control,
        .teacher-panel .form-select {
            min-height: 46px;
            border-radius: 11px;
        }

        .teacher-table thead th {
            padding: 0.9rem 1rem;
            border: 0;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.76rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .teacher-table tbody td {
            padding: 0.9rem 1rem;
            vertical-align: middle;
            border-color: #eef2f7;
        }

        .teacher-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .teacher-id {
            color: #64748b;
            font-variant-numeric: tabular-nums;
        }
    </style>

    <section class="teacher-heading text-white p-4 p-md-5 mb-4">
        <span class="badge bg-white bg-opacity-25 text-white mb-3">ADMINISTRASI SEKOLAH</span>
        <h1 class="h2 fw-bold mb-2">Kelola Data Guru</h1>
        <p class="mb-0 text-white-50">Tambahkan dan kelola informasi guru serta akun SIPN yang terhubung.</p>
    </section>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <section class="card teacher-panel mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex align-items-start gap-3 mb-4">
                <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                    style="width: 46px; height: 46px;">+</div>
                <div>
                    <h2 class="h5 fw-bold mb-1">Tambah Guru</h2>
                    <p class="text-secondary small mb-0">Lengkapi informasi di bawah untuk menambahkan data guru.</p>
                </div>
            </div>
            <form method="POST" action="<?= APP_BASE_URL; ?>guru/tambah.php" class="row g-3 align-items-end">
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="nip" class="form-label">NIP</label>
                    <input type="text" id="nip" name="nip" class="form-control" placeholder="Masukkan NIP" required>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="nama" class="form-label">Nama Guru</label>
                    <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="col-12 col-md-6 col-xl-2">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-select">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 col-xl-2">
                    <label for="user_id" class="form-label">Akun SIPN</label>
                    <select id="user_id" name="user_id" class="form-select">
                        <option value="">Tanpa akun</option>
                        <?php while ($u = mysqli_fetch_assoc($users)): ?>
                            <option value="<?= (int) $u['id']; ?>"><?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12 col-xl-2">
                    <button type="submit" name="tambah" class="btn btn-primary w-100">Simpan Guru</button>
                </div>
            </form>
        </div>
    </section>

    <section class="card teacher-panel overflow-hidden">
        <div class="card-body p-0">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 p-4">
                <div>
                    <h2 class="h5 fw-bold mb-1">Daftar Guru</h2>
                    <p class="text-secondary small mb-0">Informasi guru yang terdaftar di SIPN.</p>
                </div>
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">
                    <?= number_format(mysqli_num_rows($guru)); ?> guru
                </span>
            </div>
            <div class="table-responsive">
                <table class="table teacher-table mb-0">
                    <thead>
                        <tr><th>NIP</th><th>Nama Guru</th><th>Jenis Kelamin</th><th>Akun SIPN</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($guru) === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-5">Belum ada data guru.</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($guru)): ?>
                            <tr>
                                <td class="teacher-id"><?= htmlspecialchars($row['nip'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-secondary border">
                                        <?= $row['jenis_kelamin'] === 'P' ? 'Perempuan' : 'Laki-laki'; ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['username'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end">
                                    <a href="<?= APP_BASE_URL; ?>guru/edit.php?id=<?= (int) $row['id']; ?>"
                                        class="btn btn-outline-primary btn-sm">Edit</a>
                                    <a href="<?= APP_BASE_URL; ?>guru/hapus.php?id=<?= (int) $row['id']; ?>"
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Hapus guru ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/footer.php'; ?>