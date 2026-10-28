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
    exit('ID mata pelajaran tidak valid.');
}
$scheduled = mysqli_prepare($conn, 'SELECT COUNT(*) AS total FROM jadwal_pelajaran WHERE mapel_id = ?');
mysqli_stmt_bind_param($scheduled, 'i', $id);
mysqli_stmt_execute($scheduled);
if ((int) mysqli_fetch_assoc(mysqli_stmt_get_result($scheduled))['total'] > 0) {
    $_SESSION['crud_error'] = 'Mata pelajaran tidak dapat dihapus karena masih digunakan pada jadwal.';
} else {
    $delete = mysqli_prepare($conn, 'DELETE FROM mapel WHERE id = ?');
    mysqli_stmt_bind_param($delete, 'i', $id);
    if (!mysqli_stmt_execute($delete)) {
        http_response_code(500);
        exit('Gagal menghapus mata pelajaran.');
    }
}
header('Location: ' . APP_BASE_URL . 'mapel/daftar.php');
