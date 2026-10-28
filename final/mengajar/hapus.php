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
    exit('ID penugasan tidak valid.');
}
$delete = mysqli_prepare($conn, 'DELETE FROM mengajar WHERE id = ?');
mysqli_stmt_bind_param($delete, 'i', $id);
if (!mysqli_stmt_execute($delete)) {
    http_response_code(500);
    exit('Gagal menghapus penugasan mengajar.');
}
header('Location: ' . APP_BASE_URL . 'mengajar/daftar.php');
