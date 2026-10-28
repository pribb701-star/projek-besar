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
<div class="form2" style="margin: 0 auto; width: 50%;">
    <div class="container text-center" style="margin: 0 auto;">
        <div class="form">
            <div class="card" style="margin-top: 20px;">
                <div class="card-form">
                    <h5>Form Tambah User (Admin)</h5><br>
                </div>
                
                <form action="aksi_tambah_user.php" method="POST">
                    <div class="form2" style="border: 1px solid #d3d3d3;">
                        <h4>Data Kredensial Akun (Tabel Users)</h4><br>
                        
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username Akun | Contoh: ujang123" required style="width: 90%; margin: 0 auto;" />
                        </div>
                        <br>
                        
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password Akun | Contoh: password123" required style="width: 90%; margin: 0 auto;" />
                        </div>
                        <br>
                        
                        <input type="hidden" name="role" value="admin">
                        
                        <div class="button2">
                            <button type="submit" name="kirim" class="btn btn-primary" style="margin-bottom: 20px;">Simpan Data</button>
                            <button type="button" class="btn btn-danger" style="margin-bottom: 20px;" onclick="window.history.back();">Kembali</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require 'Footer.php'; ?>