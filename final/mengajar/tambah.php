<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_BASE_URL . 'mengajar/daftar.php');
    exit;
}
$mapel_id = filter_var($_POST['mapel_id'] ?? null, FILTER_VALIDATE_INT);
$siswa_id = filter_var($_POST['siswa_id'] ?? null, FILTER_VALIDATE_INT);
$guru_id = filter_var($_POST['guru_id'] ?? null, FILTER_VALIDATE_INT);
if (!$mapel_id || !$siswa_id || !$guru_id) {
    http_response_code(400);
    exit('Mata pelajaran, siswa, atau guru tidak valid.');
}
$check_guru = mysqli_prepare($conn, "SELECT guru.id FROM guru JOIN users ON users.id = guru.user_id WHERE guru.id = ? AND users.role = 'guru'");
if (!$check_guru) {
    http_response_code(500);
    exit('Gagal memvalidasi guru pengajar.');
}
mysqli_stmt_bind_param($check_guru, 'i', $guru_id);
if (!mysqli_stmt_execute($check_guru) || !mysqli_fetch_assoc(mysqli_stmt_get_result($check_guru))) {
    http_response_code(400);
    exit('Guru pengajar tidak valid.');
}
$insert = mysqli_prepare($conn, 'INSERT INTO mengajar (mapel_id, siswa_id, guru_id) VALUES (?, ?, ?)');
if (!$insert) {
    http_response_code(500);
    exit('Gagal menyiapkan penyimpanan penugasan mengajar.');
}
mysqli_stmt_bind_param($insert, 'iii', $mapel_id, $siswa_id, $guru_id);
if (!mysqli_stmt_execute($insert)) {
    http_response_code(500);
    exit('Gagal menyimpan penugasan mengajar.');
}
header('Location: ' . APP_BASE_URL . 'mengajar/daftar.php');
