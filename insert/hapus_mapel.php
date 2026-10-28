<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] === 'siswa') {
    header("Location: mapel.php");
    exit();
}

$id = $_GET['id'] ?? '';
if ($id) {
    mysqli_query($koneksi, "DELETE FROM mapel WHERE id = '$id'");
}

header("Location: mapel.php");
exit();
?>