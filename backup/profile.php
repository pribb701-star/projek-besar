<?php
session_start();
require_once dirname(__DIR__) . '/app_url.php';
require_once dirname(__DIR__) . '/koneksi/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . APP_BASE_URL . "auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pesan   = '';

// Proses Upload Foto Profil
if (isset($_POST['upload'])) {
    $foto_nama  = $_FILES['foto']['name'];
    $foto_tmp   = $_FILES['foto']['tmp_name'];
    $foto_size  = $_FILES['foto']['size'];
    $error_code = $_FILES['foto']['error'];

    if ($error_code === 0) {
        $ekstensi_valid = ['jpg', 'jpeg', 'png', 'webp'];
        $ekstensi_foto  = strtolower(pathinfo($foto_nama, PATHINFO_EXTENSION));

        if (in_array($ekstensi_foto, $ekstensi_valid)) {
            if ($foto_size <= 2000000) { // Maksimal 2MB
                $nama_foto_baru = 'user_' . $user_id . '_' . time() . '.' . $ekstensi_foto;
                
                // Buat folder uploads jika belum ada
                $upload_dir = dirname(__DIR__) . '/uploads';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($foto_tmp, $upload_dir . '/' . $nama_foto_baru)) {
                    // Hapus foto lama jika bukan default
                    $q_lama = mysqli_query($conn, "SELECT foto FROM users WHERE id='$user_id'");
                    $dt_lama = mysqli_fetch_assoc($q_lama);
                    if (!empty($dt_lama['foto']) && $dt_lama['foto'] !== 'default.png' && file_exists($upload_dir . '/' . $dt_lama['foto'])) {
                        unlink($upload_dir . '/' . $dt_lama['foto']);
                    }

                    mysqli_query($conn, "UPDATE users SET foto='$nama_foto_baru' WHERE id='$user_id'");
                    $pesan = "<div class='alert alert-success py-2'>Foto profil berhasil diperbarui!</div>";
                } else {
                    $pesan = "<div class='alert alert-danger py-2'>Gagal mengunggah foto.</div>";
                }
            } else {
                $pesan = "<div class='alert alert-danger py-2'>Ukuran foto terlalu besar (maksimal 2MB).</div>";
            }
        } else {
            $pesan = "<div class='alert alert-danger py-2'>Format file harus JPG, JPEG, PNG, atau WEBP.</div>";
        }
    } else {
        $pesan = "<div class='alert alert-danger py-2'>Pilih foto terlebih dahulu.</div>";
    }
}

// [FITUR BARU] Proses Hapus Foto Profil
if (isset($_POST['hapus'])) {
    $q_foto  = mysqli_query($conn, "SELECT foto FROM users WHERE id='$user_id'");
    $dt_foto = mysqli_fetch_assoc($q_foto);

    if (!empty($dt_foto['foto']) && $dt_foto['foto'] !== 'default.png') {
        // Hapus file fisik dari folder uploads
        if (file_exists(dirname(__DIR__) . '/uploads/' . $dt_foto['foto'])) {
            unlink(dirname(__DIR__) . '/uploads/' . $dt_foto['foto']);
        }

        // Kembalikan ke foto default (struktur tabel tidak berubah)
        mysqli_query($conn, "UPDATE users SET foto='default.png' WHERE id='$user_id'");
        $pesan = "<div class='alert alert-success py-2'>Foto profil berhasil dihapus!</div>";
    } else {
        $pesan = "<div class='alert alert-warning py-2'>Tidak ada foto profil yang bisa dihapus.</div>";
    }
}

// Ambil Data User
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));
$foto_profil = (!empty($user['foto']) && file_exists(dirname(__DIR__) . '/uploads/' . $user['foto']))
                ? APP_BASE_URL . 'uploads/' . rawurlencode($user['foto'])
                : 'https://via.placeholder.com/150?text=No+Image';

// [FITUR BARU] Cek apakah user punya foto selain default (untuk status tombol hapus)
$punya_foto = (!empty($user['foto']) && $user['foto'] !== 'default.png');

include __DIR__ . '/header.php';
?>

<div class="container my-4">
    <div class="page-heading">
        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-2">AKUN SAYA</span>
        <h1 class="h2 mb-2">Profil Pengguna</h1>
        <p>Kelola foto dan informasi akun SIPN Anda.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-7 col-xl-6">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-4 p-md-5 text-center">
                    <?= $pesan; ?>

                    <div class="mb-4">
                        <img src="<?= htmlspecialchars($foto_profil, ENT_QUOTES, 'UTF-8'); ?>" alt="Foto profil"
                            class="rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h2 class="h4 fw-bold mt-1 mb-2"><?= htmlspecialchars(ucfirst($user['username']), ENT_QUOTES, 'UTF-8'); ?></h2>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2"><?= htmlspecialchars(strtoupper($user['role']), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold">Ganti Foto Profil</label>
                            <input type="file" name="foto" class="form-control" accept="image/*" required>
                            <small class="text-muted">Format JPG, PNG, atau WEBP. Ukuran maksimal 2MB.</small>
                        </div>
                        <button type="submit" name="upload" class="btn btn-primary w-100">Simpan Foto Profil</button>
                    </form>

                    <form method="POST" class="mt-2" onsubmit="return confirm('Yakin ingin menghapus foto profil?');">
                        <button type="submit" name="hapus" class="btn btn-outline-danger w-100" <?= $punya_foto ? '' : 'disabled'; ?>>Hapus Foto Profil</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>