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
<head>
    <link rel="stylesheet" href="style1.css">
</head>

<div class="container mt-5 mb-5">
    <div class="card mx-auto shadow-sm" style="max-width: 600px;">
        
        <div class="card-header bg-success text-white text-center">
            <h4 class="mb-0">Form Tambah Guru</h4>
        </div>

        <div class="card-body p-4">
            <form action="aksi_tambah_user.php" method="POST">
                
                <!-- Bagian Profil Guru -->
                <h5 class="mb-3 border-bottom pb-2">Data Profil Guru</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">NIP Guru:</label>
                    <input type="number" name="nip" class="form-control" placeholder="Nomor Induk Pegawai | Contoh: 198501012010011001" required />
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap:</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap Guru | Contoh: Budi Santoso, S.Pd" required />
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold d-block">Jenis Kelamin:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_kelamin" value="L" id="laki_guru" required>
                        <label class="form-check-label" for="laki_guru">Laki-laki</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_kelamin" value="P" id="perempuan_guru" required>
                        <label class="form-check-label" for="perempuan_guru">Perempuan</label>
                    </div>
                </div>

                <br>
                <!-- Bagian Kredensial Akun -->
                <h5 class="mb-3 border-bottom pb-2">Data Kredensial Akun (Tabel Users)</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Username Akun:</label>
                    <input type="text" name="username" class="form-control" placeholder="Contoh: gurubudi" required />
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Password Akun:</label>
                    <input type="password" name="password" class="form-control" placeholder="Contoh: password123" required />
                </div>
                
                <input type="hidden" name="role" value="guru">
                
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-danger px-4" onclick="window.history.back();">Kembali</button>
                    <button type="submit" name="kirim" class="btn btn-primary px-4">Simpan Data</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require 'Footer.php'; ?>