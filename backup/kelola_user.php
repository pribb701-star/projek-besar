<?php
session_start();
require_once __DIR__ . '/app_url.php';

// Jika belum login ATAU role-nya bukan admin, tendang ke login.php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . APP_BASE_URL . "auth/login.php");
    exit;
}

require_once __DIR__ . '/koneksi.php';

$pesan = '';

// Tambah User Baru
if (isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);

    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "<div class='alert alert-danger py-2'>Username sudah digunakan! Cari username lain.</div>";
    } else {
        mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");
        header("Location: " . APP_BASE_URL . "user/");
        exit;
    }
}

// Hapus User
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    
    // Proteksi: Admin tidak boleh menghapus akunnya sendiri yang sedang digunakan
    if ($id == $_SESSION['user_id']) {
        $_SESSION['flash_msg'] = "<div class='alert alert-warning py-2'>Kamu tidak bisa menghapus akun kamu sendiri yang sedang digunakan!</div>";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    }
    header("Location: " . APP_BASE_URL . "user/");
    exit;
}

// Reset Password User Lain oleh Admin
if (isset($_POST['reset_pw'])) {
    $id_target = $_POST['user_id'];
    $pw_baru   = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);
    
    mysqli_query($conn, "UPDATE users SET password='$pw_baru' WHERE id='$id_target'");
    $pesan = "<div class='alert alert-success py-2'>Password pengguna berhasil diubah!</div>";
}

if (isset($_SESSION['flash_msg'])) {
    $pesan = $_SESSION['flash_msg'];
    unset($_SESSION['flash_msg']);
}
if (!empty($_SESSION['crud_error'])) {
    $pesan = "<div class='alert alert-danger py-2'>" . htmlspecialchars($_SESSION['crud_error'], ENT_QUOTES, 'UTF-8') . "</div>";
    unset($_SESSION['crud_error']);
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, username ASC");
include __DIR__ . '/header.php';
?>

<div class="container my-4">
    <div class="page-heading">
        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-2">ADMINISTRASI SEKOLAH</span>
        <h1 class="h2 mb-2">Kelola Pengguna</h1>
        <p>Atur akun, peran akses, dan kata sandi pengguna SIPN.</p>
    </div>

    <!-- Form Tambah User -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold mb-1">Tambah Pengguna</h2>
            <p class="text-secondary small mb-4">Buat akun Admin, Guru, atau Siswa.</p>
            <?= $pesan; ?>
            <form method="POST" action="<?= APP_BASE_URL; ?>user/tambah.php" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="username" class="form-label fw-semibold">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                <div class="col-12 col-md-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Buat password" required>
                </div>
                <div class="col-12 col-md-3">
                    <label for="role" class="form-label fw-semibold">Peran</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="">Pilih peran</option>
                        <option value="admin">Admin</option>
                        <option value="guru">Guru</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" name="tambah" class="btn btn-primary w-100">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar User -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-bold mb-1">Daftar Pengguna</h2>
            <p class="text-secondary small mb-4">Kelola akses akun dan reset kata sandi.</p>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="8%">No</th>
                            <th>Username</th>
                            <th>Role / Akses</th>
                            <th width="30%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($u = mysqli_fetch_assoc($users)): 
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td>
                                <b><?= htmlspecialchars($u['username']); ?></b>
                                <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                    <span class="badge bg-success ms-1">Akun Kamu</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                if ($u['role'] === 'admin') echo '<span class="badge bg-danger">ADMIN</span>';
                                elseif ($u['role'] === 'guru') echo '<span class="badge bg-warning text-dark">GURU</span>';
                                else echo '<span class="badge bg-info text-white">SISWA</span>';
                                ?>
                            </td>
                            <td class="text-center">
                                <!-- Tombol Modal Reset Password -->
                                <button class="btn btn-warning btn-sm text-dark me-1" data-bs-toggle="modal" data-bs-target="#modalReset<?= $u['id']; ?>">
                                    Reset Password
                                </button>
                                
                                <!-- Tombol Hapus (Nonaktif untuk akun sendiri) -->
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="<?= APP_BASE_URL; ?>user/edit.php?id=<?= (int) $u['id']; ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                                    <a href="<?= APP_BASE_URL; ?>user/hapus.php?id=<?= (int) $u['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Hapus</button>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- Modal Reset Password -->
                        <div class="modal fade" id="modalReset<?= $u['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="<?= APP_BASE_URL; ?>user/edit.php">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Reset Password: <?= htmlspecialchars($u['username']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Password Baru</label>
                                                <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="reset_pw" class="btn btn-primary">Simpan Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>