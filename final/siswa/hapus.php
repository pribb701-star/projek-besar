<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('ID siswa tidak valid.');
}
$has_attendance = mysqli_prepare($conn, 'SELECT COUNT(*) AS total FROM absensi WHERE siswa_id = ?');
mysqli_stmt_bind_param($has_attendance, 'i', $id);
mysqli_stmt_execute($has_attendance);
if ((int) mysqli_fetch_assoc(mysqli_stmt_get_result($has_attendance))['total'] > 0) {
    $_SESSION['crud_error'] = 'Siswa tidak dapat dihapus karena sudah memiliki riwayat absensi.';
} else {
    $delete = mysqli_prepare($conn, 'DELETE FROM siswa WHERE id = ?');
    mysqli_stmt_bind_param($delete, 'i', $id);
    if (!mysqli_stmt_execute($delete)) {
        http_response_code(500);
        exit('Gagal menghapus data siswa.');
    }
}
header('Location: ' . APP_BASE_URL . 'siswa/daftar.php');
