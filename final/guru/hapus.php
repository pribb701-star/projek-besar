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
    exit('ID guru tidak valid.');
}

$scheduled = mysqli_prepare($conn, 'SELECT COUNT(*) AS total FROM mapel m JOIN jadwal_pelajaran jp ON jp.mapel_id = m.id WHERE m.guru_id = ?');
mysqli_stmt_bind_param($scheduled, 'i', $id);
mysqli_stmt_execute($scheduled);
if ((int) mysqli_fetch_assoc(mysqli_stmt_get_result($scheduled))['total'] > 0) {
    $_SESSION['crud_error'] = 'Guru tidak dapat dihapus karena masih memiliki jadwal pelajaran.';
} else {
    $delete = mysqli_prepare($conn, 'DELETE FROM guru WHERE id = ?');
    mysqli_stmt_bind_param($delete, 'i', $id);
    if (!mysqli_stmt_execute($delete)) {
        http_response_code(500);
        exit('Gagal menghapus data guru.');
    }
}

header('Location: ' . APP_BASE_URL . 'guru/daftar.php');
