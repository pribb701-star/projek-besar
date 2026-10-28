<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || ($_SESSION['role'] ?? null) !== 'admin') {
    echo "<script>
        alert('Akses ditolak! Halaman ini hanya untuk Admin.');
        window.location.href = 'daftar_terpadu.php';
    </script>";
    exit();
}

require 'Navbar_1.php';
?>

<div class="container mt-5 mb-5">
    <div class="card mx-auto shadow-sm" style="max-width: 600px;">
        
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Form Tambah Siswa</h4>
        </div>

        <div class="card-body p-4">
            <form action="aksi_tambah_user.php" method="POST">
                
                <!-- Bagian Profil Siswa -->
                <h5 class="mb-3 border-bottom pb-2">Data Profil Siswa</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">NIS:</label>
                    <input type="number" name="nis" class="form-control" placeholder="Nomor Induk Siswa | Contoh: 12345678" required />
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap:</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap Siswa | Contoh: Ujang Asep" required />
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Kelas:</label>
                    <input type="text" name="kelas" class="form-control" placeholder="Kelas Siswa | Contoh: X-RPL-1" required />
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold d-block">Jenis Kelamin:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_kelamin" value="L" id="laki" required>
                        <label class="form-check-label" for="laki">Laki-laki</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_kelamin" value="P" id="perempuan" required>
                        <label class="form-check-label" for="perempuan">Perempuan</label>
                    </div>
                </div>

                <br>
                <!-- Bagian Kredensial Akun -->
                <h5 class="mb-3 border-bottom pb-2">Data Kredensial Akun (Tabel Users)</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Username Akun:</label>
                    <input type="text" name="username" class="form-control" placeholder="Contoh: ujang123" required />
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Password Akun:</label>
                    <input type="password" name="password" class="form-control" placeholder="Contoh: password123" required />
                </div>
                
                <input type="hidden" name="role" value="siswa">
                
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-danger px-4" onclick="window.history.back();">Kembali</button>
                    <button type="submit" name="kirim" class="btn btn-primary px-4">Simpan Data</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require 'Footer.php'; ?>