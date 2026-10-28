<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_BASE_URL . 'user/daftar.php');
    exit;
}
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';
if ($username === '' || $password === '' || !in_array($role, ['admin', 'guru', 'siswa'], true)) {
    http_response_code(400);
    exit('Data akun tidak valid.');
}
$exists = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ?');
mysqli_stmt_bind_param($exists, 's', $username);
mysqli_stmt_execute($exists);
if (mysqli_stmt_get_result($exists)->num_rows > 0) {
    $_SESSION['crud_error'] = 'Username sudah digunakan. Pilih username lain.';
    header('Location: ' . APP_BASE_URL . 'user/daftar.php');
    exit;
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$insert = mysqli_prepare($conn, 'INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
mysqli_stmt_bind_param($insert, 'sss', $username, $hash, $role);
if (!mysqli_stmt_execute($insert)) {
    http_response_code(500);
    exit('Gagal membuat akun.');
}
header('Location: ' . APP_BASE_URL . 'user/daftar.php');
