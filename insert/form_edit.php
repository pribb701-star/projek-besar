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

include 'koneksi.php';

if (!isset($_GET['id']) || !isset($_GET['role'])) {
    echo "<script>
        alert('ID atau role tidak ditemukan');
        window.location.href = 'daftar_terpadu.php';
    </script>";
    exit();
}

$id = $_GET['id'];
$role = $_GET['role'];

/* ==================== ADMIN ==================== */
if ($role === 'admin') {
    $query = "SELECT * FROM users WHERE id = '$id' LIMIT 1";
    $result = mysqli_fetch_assoc(mysqli_query($koneksi, $query));

    if (!$result) {
        echo "<script>
            alert('Data admin tidak ditemukan');
            window.location.href = 'daftar_terpadu.php';
        </script>";
        exit();
    }
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Edit Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style1.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">Form Edit User (Admin)</h4>
                </div>
                <div class="card-body">
                    <form action="edit.php" method="POST">
                        <h5>Data Kredensial Akun</h5>
                        <label>Username:</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($result['username']) ?>" required>
                        <br>
                        <label>Password Akun:</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika password tidak diubah">
                        <br>
                        <input type="hidden" name="role" value="admin">
                        <input type="hidden" name="id_user" value="<?= $result['id'] ?>">
                        <button type="submit" name="edit" class="btn btn-primary">Simpan Data</button>
                        <a href="daftar_terpadu.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
/* ==================== GURU ==================== */
} elseif ($role === 'guru') {
    $queryguru = "
        SELECT users.id AS user_id, users.username, users.role, guru.id AS guru_id, guru.nip, guru.nama, guru.jenis_kelamin
        FROM users
        JOIN guru ON users.id = guru.user_id
        WHERE users.id = '$id' LIMIT 1
    ";
    $resultguru = mysqli_fetch_assoc(mysqli_query($koneksi, $queryguru));

    if (!$resultguru) {
        echo "<script>
            alert('Data guru tidak ditemukan');
            window.location.href = 'daftar_terpadu.php';
        </script>";
        exit();
    }
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Edit Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style1.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">Form Edit User (Guru)</h4>
                </div>
                <div class="card-body">
                    <form action="edit.php" method="POST">
                        <h5>Data Profil Guru</h5>
                        <label>NIP:</label>
                        <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($resultguru['nip']) ?>" required>
                        <br>
                        <label>Nama Lengkap:</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($resultguru['nama']) ?>" required>
                        <br>
                        <label>Jenis Kelamin:</label><br>
                        <input type="radio" name="jenis_kelamin" value="L" class="form-check-input" <?= ($resultguru['jenis_kelamin'] == 'L') ? 'checked' : '' ?> required> Laki-laki
                        &nbsp;
                        <input type="radio" name="jenis_kelamin" value="P" class="form-check-input" <?= ($resultguru['jenis_kelamin'] == 'P') ? 'checked' : '' ?>> Perempuan
                        <br><br>
                        <input type="hidden" name="id_guru" value="<?= $resultguru['guru_id'] ?>">
                        <hr>
                        <h5>Data Kredensial Akun</h5>
                        <label>Username:</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($resultguru['username']) ?>" required>
                        <br>
                        <label>Password Akun:</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika password tidak diubah">
                        <br>
                        <input type="hidden" name="role" value="guru">
                        <input type="hidden" name="id_user" value="<?= $resultguru['user_id'] ?>">
                        <button type="submit" name="edit" class="btn btn-primary">Simpan Data</button>
                        <a href="daftar_terpadu.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
/* ==================== SISWA ==================== */
} elseif ($role === 'siswa') {
    $querysiswa = "
        SELECT users.id AS user_id, users.username, users.role, siswa.id AS siswa_id, siswa.nis, siswa.nama, siswa.kelas, siswa.jenis_kelamin
        FROM users
        JOIN siswa ON users.id = siswa.user_id
        WHERE users.id = '$id' LIMIT 1
    ";
    $resultsiswa = mysqli_fetch_assoc(mysqli_query($koneksi, $querysiswa));

    if (!$resultsiswa) {
        echo "<script>
            alert('Data siswa tidak ditemukan');
            window.location.href = 'daftar_terpadu.php';
        </script>";
        exit();
    }
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Edit Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style1.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">Form Edit User (Siswa)</h4>
                </div>
                <div class="card-body">
                    <form action="edit.php" method="POST">
                        <h5>Data Profil Siswa</h5>
                        <label>NIS:</label>
                        <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($resultsiswa['nis']) ?>" required>
                        <br>
                        <label>Nama Lengkap:</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($resultsiswa['nama']) ?>" required>
                        <br>
                        <label>Kelas:</label>
                        <input type="text" name="kelas" class="form-control" value="<?= htmlspecialchars($resultsiswa['kelas']) ?>" required>
                        <br>
                        <label>Jenis Kelamin:</label><br>
                        <input type="radio" name="jenis_kelamin" value="L" class="form-check-input" <?= ($resultsiswa['jenis_kelamin'] == 'L') ? 'checked' : '' ?> required> Laki-laki
                        &nbsp;
                        <input type="radio" name="jenis_kelamin" value="P" class="form-check-input" <?= ($resultsiswa['jenis_kelamin'] == 'P') ? 'checked' : '' ?>> Perempuan
                        <br><br>
                        <input type="hidden" name="id_siswa" value="<?= $resultsiswa['siswa_id'] ?>">
                        <hr>
                        <h5>Data Kredensial Akun</h5>
                        <label>Username:</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($resultsiswa['username']) ?>" required>
                        <br>
                        <label>Password Akun:</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika password tidak diubah">
                        <br>
                        <input type="hidden" name="role" value="siswa">
                        <input type="hidden" name="id_user" value="<?= $resultsiswa['user_id'] ?>">
                        <button type="submit" name="edit" class="btn btn-primary">Simpan Data</button>
                        <a href="daftar_terpadu.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
} else {
    echo "<script>
        alert('Role tidak valid');
        window.location.href = 'daftar_terpadu.php';
    </script>";
}
?>