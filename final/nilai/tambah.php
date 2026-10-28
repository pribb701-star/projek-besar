<?php
session_start();
require_once __DIR__ . '/../koneksi/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header('Location: ' . APP_BASE_URL . 'login/login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . APP_BASE_URL . 'nilai/daftar.php');
    exit;
}
$siswa_id = filter_var($_POST['siswa_id'] ?? null, FILTER_VALIDATE_INT);
$mapel_id = filter_var($_POST['mapel_id'] ?? null, FILTER_VALIDATE_INT);
$kkm = filter_var($_POST['nilai_kkm'] ?? null, FILTER_VALIDATE_INT);
$pengetahuan = filter_var($_POST['nilai_pengetahuan'] ?? null, FILTER_VALIDATE_INT);
$keterampilan = filter_var($_POST['nilai_keterampilan'] ?? null, FILTER_VALIDATE_INT);
if (!$siswa_id || !$mapel_id || $kkm === false || $pengetahuan === false || $keterampilan === false
    || min($kkm, $pengetahuan, $keterampilan) < 0 || max($kkm, $pengetahuan, $keterampilan) > 100) {
    http_response_code(400);
    exit('Data nilai tidak valid. Nilai harus berada pada rentang 0 sampai 100.');
}
$keterangan = (($pengetahuan + $keterampilan) / 2 >= $kkm) ? 'LUNAS / TUNTAS' : 'REMIDI';
$exists = mysqli_prepare($conn, 'SELECT id FROM nilai_rapor WHERE siswa_id = ? AND mapel_id = ? LIMIT 1');
mysqli_stmt_bind_param($exists, 'ii', $siswa_id, $mapel_id);
mysqli_stmt_execute($exists);
$existing = mysqli_fetch_assoc(mysqli_stmt_get_result($exists));
if ($existing) {
    $_SESSION['crud_error'] = 'Nilai untuk siswa dan mata pelajaran tersebut sudah ada.';
    header('Location: ' . APP_BASE_URL . 'nilai/form_tambah.php');
    exit;
}
$save = mysqli_prepare($conn, 'INSERT INTO nilai_rapor (siswa_id, mapel_id, nilai_kkm, nilai_pengetahuan, nilai_keterampilan, keterangan) VALUES (?, ?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($save, 'iiiiis', $siswa_id, $mapel_id, $kkm, $pengetahuan, $keterampilan, $keterangan);
if (!mysqli_stmt_execute($save)) {
    http_response_code(500);
    exit('Gagal menyimpan nilai.');
}
header('Location: ' . APP_BASE_URL . 'nilai/daftar.php');
