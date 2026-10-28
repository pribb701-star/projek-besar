<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_BASE_URL . 'mapel/daftar.php');
    exit;
}
$kode = trim($_POST['kode_mapel'] ?? '');
$nama = trim($_POST['nama_mapel'] ?? '');
$guru_id = ($_POST['guru_id'] ?? '') === '' ? null : filter_var($_POST['guru_id'], FILTER_VALIDATE_INT);
if ($kode === '' || $nama === '' || (($_POST['guru_id'] ?? '') !== '' && $guru_id === false)) {
    http_response_code(400);
    exit('Data mata pelajaran tidak valid.');
}
$insert = mysqli_prepare($conn, 'INSERT INTO mapel (kode_mapel, nama_mapel, guru_id) VALUES (?, ?, ?)');
mysqli_stmt_bind_param($insert, 'ssi', $kode, $nama, $guru_id);
if (!mysqli_stmt_execute($insert)) {
    http_response_code(500);
    exit('Gagal menyimpan mata pelajaran.');
}
header('Location: ' . APP_BASE_URL . 'mapel/daftar.php');
