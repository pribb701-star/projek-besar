<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Akun Siswa - SIPN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container min-vh-100 d-flex align-items-center justify-content-center py-4">
    <form method="post" action="proses_register.php" class="card border-0 shadow-sm p-4 p-md-5 w-100" style="max-width: 550px">
        <h1 class="h3 mb-2 text-center">Buat akun siswa</h1>
        <p class="text-secondary mb-4 text-center">Lengkapi data diri kamu di bawah ini</p>
        
        <!-- Pesan Error (Opsional, jika ada) -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Kolom Nama Lengkap & NIS -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="nama_lengkap">Nama Lengkap</label>
                <input class="form-control" id="nama_lengkap" name="nama_lengkap" type="text" placeholder="Cth: Budi Santoso" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label" for="nis">NIS / NISN</label>
                <input class="form-control" id="nis" name="nis" type="number" placeholder="Cth: 10293847" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="kelas">Kelas</label>
            <select class="form-select" id="kelas" name="kelas" required>
                <option value="">-- Pilih Kelas --</option>
                <option value="X">Kelas X (10)</option>
                <option value="XI">Kelas XI (11)</option>
                <option value="XII">Kelas XII (12)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>

        <hr class="my-4">

        <div class="mb-3">
            <label class="form-label" for="username">Username (Untuk Login)</label>
            <input class="form-control" id="username" name="username" type="text" autocomplete="username" required>
        </div>
        
        <div class="row">
            <!-- Kolom Password & Konfirmasi -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="password">Password (min. 8 karakter)</label>
                <input class="form-control" id="password" name="password" type="password" minlength="8" required>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label" for="konfirmasi_password">Ulangi Password</label>
                <input class="form-control" id="konfirmasi_password" name="konfirmasi_password" type="password" minlength="8" required>
            </div>
        </div>
        
        <button class="btn btn-primary w-100 py-2 mb-3" type="submit">Daftar Sekarang</button>
        <p class="text-center mb-0"><a href="login.php" class="text-decoration-none">Kembali ke halaman login</a></p>
    </form>
</main>
</body>
</html>