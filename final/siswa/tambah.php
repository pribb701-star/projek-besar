<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_BASE_URL . 'siswa/daftar.php');
    exit;
}
$nis = trim($_POST['nis'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$kelas = trim($_POST['kelas'] ?? '');
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$user_id = ($_POST['user_id'] ?? '') === '' ? null : filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
if ($nis === '' || $nama === '' || $kelas === '' || !in_array($jenis_kelamin, ['L', 'P'], true)
    || (($_POST['user_id'] ?? '') !== '' && $user_id === false)) {
    http_response_code(400);
    exit('Data siswa tidak valid.');
}
$insert = mysqli_prepare($conn, 'INSERT INTO siswa (nis, nama, kelas, jenis_kelamin, user_id) VALUES (?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($insert, 'ssssi', $nis, $nama, $kelas, $jenis_kelamin, $user_id);
if (!mysqli_stmt_execute($insert)) {
    http_response_code(500);
    exit('Gagal menyimpan data siswa.');
}
header('Location: ' . APP_BASE_URL . 'siswa/daftar.php');
