<?php
require_once __DIR__ . '/../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_BASE_URL . 'login/register.php');
    exit;
}

$nama = trim($_POST['nama_lengkap'] ?? '');
$nis = trim($_POST['nis'] ?? '');
$kelas = $_POST['kelas'] ?? '';
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

if ($nama === '' || strlen($nama) > 100
    || !preg_match('/^\d{1,10}$/', $nis)
    || !in_array($kelas, ['X', 'XI', 'XII'], true)
    || !in_array($jenis_kelamin, ['L', 'P'], true)
    || $username === '' || strlen($username) > 50
    || strlen($password) < 8 || $password !== $konfirmasi_password) {
    http_response_code(400);
    exit('Data pendaftaran tidak valid. Periksa kembali semua isian dan pastikan password minimal 8 karakter serta konfirmasi password cocok.');
}

$duplicate = mysqli_prepare($conn, 'SELECT users.id FROM users LEFT JOIN siswa ON siswa.user_id = users.id WHERE users.username = ? OR siswa.nis = ? LIMIT 1');
if (!$duplicate) {
    http_response_code(500);
    exit('Gagal memeriksa data pendaftaran.');
}
mysqli_stmt_bind_param($duplicate, 'ss', $username, $nis);
if (!mysqli_stmt_execute($duplicate)) {
    http_response_code(500);
    exit('Gagal memeriksa data pendaftaran.');
}
if (mysqli_fetch_assoc(mysqli_stmt_get_result($duplicate))) {
    http_response_code(409);
    exit('Username atau NIS sudah terdaftar.');
}

if (!mysqli_begin_transaction($conn)) {
    http_response_code(500);
    exit('Gagal memulai proses pendaftaran.');
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$insert_user = mysqli_prepare($conn, 'INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
if (!$insert_user) {
    mysqli_rollback($conn);
    http_response_code(500);
    exit('Gagal menyiapkan pembuatan akun.');
}
$role = 'siswa';
mysqli_stmt_bind_param($insert_user, 'sss', $username, $hashed_password, $role);
if (!mysqli_stmt_execute($insert_user)) {
    mysqli_rollback($conn);
    http_response_code(500);
    exit('Gagal membuat akun siswa.');
}
$user_id = mysqli_insert_id($conn);

$insert_student = mysqli_prepare($conn, 'INSERT INTO siswa (nis, nama, kelas, jenis_kelamin, user_id) VALUES (?, ?, ?, ?, ?)');
if (!$insert_student) {
    mysqli_rollback($conn);
    http_response_code(500);
    exit('Gagal menyiapkan penyimpanan data siswa.');
}
mysqli_stmt_bind_param($insert_student, 'ssssi', $nis, $nama, $kelas, $jenis_kelamin, $user_id);
if (!mysqli_stmt_execute($insert_student) || !mysqli_commit($conn)) {
    mysqli_rollback($conn);
    http_response_code(500);
    exit('Gagal menyimpan data siswa.');
}

header('Location: ' . APP_BASE_URL . 'login/login.php');
exit;
