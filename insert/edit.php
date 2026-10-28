<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: daftar_terpadu.php');
    exit();
}

$id_user  = $_POST['id_user'] ?? '';
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

if ($id_user === '' || $username === '' || !in_array($role, ['admin', 'guru', 'siswa'], true)) {
    echo "<script>
        alert('Data edit tidak valid');
        window.location.href = 'daftar_terpadu.php';
    </script>";
    exit();
}

$id_user = (int) $id_user;
$nip           = trim($_POST['nip'] ?? '');
$nis           = trim($_POST['nis'] ?? '');
$nama          = trim($_POST['nama'] ?? '');
$kelas         = trim($_POST['kelas'] ?? '');
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';

if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($koneksi, "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'sssi', $username, $hashed_password, $role, $id_user);
} else {
    $stmt = mysqli_prepare($koneksi, "UPDATE users SET username = ?, role = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'ssi', $username, $role, $id_user);
}

if (!mysqli_stmt_execute($stmt)) {
    echo "Data user gagal diperbarui: " . mysqli_error($koneksi);
    exit();
}

if ($role === 'guru') {
    $stmtGuru = mysqli_prepare($koneksi, "UPDATE guru SET nip = ?, nama = ?, jenis_kelamin = ? WHERE user_id = ?");
    mysqli_stmt_bind_param($stmtGuru, 'sssi', $nip, $nama, $jenis_kelamin, $id_user);

    if (!mysqli_stmt_execute($stmtGuru)) {
        die("Data guru gagal diperbarui: " . mysqli_error($koneksi));
    }
} elseif ($role === 'siswa') {
    $stmtSiswa = mysqli_prepare($koneksi, "UPDATE siswa SET nis = ?, nama = ?, kelas = ?, jenis_kelamin = ? WHERE user_id = ?");
    mysqli_stmt_bind_param($stmtSiswa, 'ssssi', $nis, $nama, $kelas, $jenis_kelamin, $id_user);

    if (!mysqli_stmt_execute($stmtSiswa)) {
        die("Data siswa gagal diperbarui: " . mysqli_error($koneksi));
    }
}

header("Location: daftar_terpadu.php");
exit();
?>