<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || ($_SESSION['role'] ?? null) !== 'admin') {
    echo "<script>
        alert('Akses ditolak! Halaman ini hanya untuk Admin.');
        window.location.href = 'daftar_terpadu.php';
    </script>";
    exit();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    require_once "koneksi.php";
} catch (mysqli_sql_exception $e) {
    error_log("Koneksi database gagal saat menghapus akun: " . $e->getMessage());
    http_response_code(500);
    exit('Koneksi database gagal. Periksa konfigurasi koneksi dan log server.');
}

function kembaliDenganPesan($pesan)
{
    echo '<script>alert(' . json_encode($pesan, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . '); window.location.href="daftar_terpadu.php";</script>';
    exit();
}

$id = $_GET['id'] ?? null;
if (!is_scalar($id) || filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
    kembaliDenganPesan('ID akun tidak valid');
}
$id = (int) $id;

$transaksiDimulai = false;

try {
    mysqli_begin_transaction($koneksi);
    $transaksiDimulai = true;

    $stmt = mysqli_prepare($koneksi, "SELECT role FROM users WHERE id = ? FOR UPDATE");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $role);
    $ditemukan = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!$ditemukan) {
        mysqli_rollback($koneksi);
        kembaliDenganPesan('Data tidak ditemukan');
    }

    if ($role === 'siswa') {
        $tabelProfil = 'siswa';
        $pesanSukses = 'Siswa berhasil dihapus';
    } elseif ($role === 'guru') {
        $tabelProfil = 'guru';
        $pesanSukses = 'Guru berhasil dihapus';
    } elseif ($role === 'admin') {
        $tabelProfil = null;
        $pesanSukses = 'Admin berhasil dihapus';
    } else {
        mysqli_rollback($koneksi);
        kembaliDenganPesan('Role akun tidak dikenali; data tidak dihapus');
    }

    if ($tabelProfil !== null) {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM {$tabelProfil} WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $jumlahTerhapus = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($jumlahTerhapus !== 1) {
        mysqli_rollback($koneksi);
        kembaliDenganPesan('Data tidak ditemukan; penghapusan dibatalkan');
    }

    mysqli_commit($koneksi);
    kembaliDenganPesan($pesanSukses);
} catch (mysqli_sql_exception $e) {
    if ($transaksiDimulai) {
        mysqli_rollback($koneksi);
    }

    error_log("Gagal menghapus akun ID {$id}: " . $e->getMessage());
    kembaliDenganPesan('Penghapusan gagal. Tidak ada perubahan yang disimpan; periksa log server.');
}
?>
