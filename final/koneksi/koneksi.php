<?php
require_once __DIR__ . '/../app_url.php';

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_siacad_smk_2";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

if (!mysqli_set_charset($conn, "utf8mb4")) {
    die("Pengaturan charset koneksi database gagal: " . mysqli_error($conn));
}

?>