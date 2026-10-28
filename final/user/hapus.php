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
    exit('ID user tidak valid.');
}
if ($id === (int) ($_SESSION['user_id'] ?? 0)) {
    $_SESSION['crud_error'] = 'Akun yang sedang digunakan tidak dapat dihapus.';
    header('Location: ' . APP_BASE_URL . 'user/daftar.php');
    exit;
}
$delete = mysqli_prepare($conn, 'DELETE FROM users WHERE id = ?');
mysqli_stmt_bind_param($delete, 'i', $id);
if (!mysqli_stmt_execute($delete)) {
    http_response_code(500);
    exit('Gagal menghapus akun.');
}
header('Location: ' . APP_BASE_URL . 'user/daftar.php');
