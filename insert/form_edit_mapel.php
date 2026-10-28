<?php
include 'koneksi.php';
include 'Navbar_1.php';

if ($role_login === 'siswa') {
    echo "<script>alert('Akses ditolak!'); window.location.href='mapel.php';</script>";
    exit();
}

$id = $_GET['id'] ?? '';
$query = mysqli_query($koneksi, "SELECT * FROM mapel WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='mapel.php';</script>";
    exit();
}

if (isset($_POST['update_mapel'])) {
    $kode_mapel = $_POST['kode_mapel'];
    $nama_mapel = $_POST['nama_mapel'];
    $kelompok   = $_POST['kelompok'];

    $update = mysqli_query($koneksi, "UPDATE mapel SET kode_mapel='$kode_mapel', nama_mapel='$nama_mapel', kelompok='$kelompok' WHERE id='$id'");
    if ($update) {
        echo "<script>alert('Mata Pelajaran Berhasil Diperbarui'); window.location.href='mapel.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui mapel!');</script>";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">Edit Mata Pelajaran</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Kode Mapel</label>
                            <input type="text" name="kode_mapel" class="form-control" value="<?= htmlspecialchars($data['kode_mapel']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Mata Pelajaran</label>
                            <input type="text" name="nama_mapel" class="form-control" value="<?= htmlspecialchars($data['nama_mapel']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelompok</label>
                            <select name="kelompok" class="form-select" required>
                                <option value="A" <?= ($data['kelompok'] == 'A') ? 'selected' : ''; ?>>Kelompok A</option>
                                <option value="B" <?= ($data['kelompok'] == 'B') ? 'selected' : ''; ?>>Kelompok B</option>
                                <option value="C" <?= ($data['kelompok'] == 'C') ? 'selected' : ''; ?>>Kelompok C</option>
                            </select>
                        </div>
                        <button type="submit" name="update_mapel" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="mapel.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'Footer.php'; ?>