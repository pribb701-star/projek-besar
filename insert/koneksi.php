<?php
// Sesuaikan dengan konfigurasi server lokal (XAMPP/Laragon)
$host = "127.0.0.1";
$user = "root";      
$pass = "root";           
$db   = "db_siacad_smk";

$koneksi = mysqli_connect("localhost", "root", "", "db_siacad_smk");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>