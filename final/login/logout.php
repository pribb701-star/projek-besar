<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';

$role = $_SESSION['role'] ?? '';


session_unset();
session_destroy();


if ($role === 'admin') {
    header('Location: admin_login.php');
} else {
    header('Location: login.php');
}
exit;
?>