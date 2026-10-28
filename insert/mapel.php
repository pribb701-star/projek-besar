<?php
include 'koneksi.php';
include 'Navbar_1.php';

// Proses Tambah Mapel (Khusus Admin/Guru)
if (isset($_POST['tambah_mapel'])) {
    if ($role_login === 'siswa') {
        echo "<script>alert('Siswa tidak memiliki akses menambah mapel!'); window.location.href='mapel.php';</script>";
        exit();
    }
    
    $kode_mapel = $_POST['kode_mapel'];
    $nama_mapel = $_POST['nama_mapel'];
    $kelompok   = $_POST['kelompok'];

    $insert = mysqli_query($koneksi, "INSERT INTO mapel (kode_mapel, nama_mapel, kelompok) VALUES ('$kode_mapel', '$nama_mapel', '$kelompok')");
    
    if ($insert) {
        echo "<script>alert('Mata Pelajaran Berhasil Ditambahkan'); window.location.href='mapel.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah mapel: " . addslashes(mysqli_error($koneksi)) . "');</script>";
    }
}

$query_mapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY id DESC");
?>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Data Mata Pelajaran</h2>
        <?php if ($role_login !== 'siswa') : ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahMapel">+ Tambah Mapel</button>
        <?php endif; ?>
    </div>

    <div class="table-responsive shadow-sm">
        <table class="table table-bordered table-striped text-center align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Kode Mapel</th>
                    <th>Nama Mata Pelajaran</th>
                    <th>Kelompok</th>
                    <?php if ($role_login !== 'siswa') : ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($query_mapel)) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['kode_mapel']); ?></td>
                    <td><?= htmlspecialchars($row['nama_mapel']); ?></td>
                    <td><span class="badge bg-info text-dark">Kelompok <?= $row['kelompok']; ?></span></td>
                    <?php if ($role_login !== 'siswa') : ?>
                    <td>
                        <a href="form_edit_mapel.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="hapus_mapel.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus mapel ini?');">Hapus</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Mapel -->
<div class="modal fade" id="modalTambahMapel" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Mata Pelajaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kode Mapel</label>
            <input type="text" name="kode_mapel" class="form-control" placeholder="Contoh: MP001" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Mata Pelajaran</label>
            <input type="text" name="nama_mapel" class="form-control" placeholder="Contoh: Pemrograman Web" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Kelompok</label>
            <select name="kelompok" class="form-select" required>
                <option value="A">Kelompok A (Muatan Nasional)</option>
                <option value="B">Kelompok B (Muatan Kewilayahan)</option>
                <option value="C">Kelompok C (Muatan Peminatan Kejuruan)</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="tambah_mapel" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'Footer.php'; ?>