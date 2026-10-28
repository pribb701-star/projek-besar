<?php
include 'koneksi.php';
include 'Navbar_1.php'; 

// Jika yang login Siswa, dia hanya bisa lihat data dirinya sendiri
if ($role_login === 'siswa') {
    $query_users = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '{$_SESSION['user_id']}'");
    $query_siswa = mysqli_query($koneksi, "SELECT siswa.*, users.username FROM siswa JOIN users ON siswa.user_id = users.id WHERE users.id = '{$_SESSION['user_id']}'");
    $query_guru  = mysqli_query($koneksi, "SELECT guru.*, users.username FROM guru JOIN users ON guru.user_id = users.id WHERE 1=0"); // Kosongkan data guru untuk siswa
} else {
    $query_users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id DESC");
    $query_siswa = mysqli_query($koneksi, "SELECT siswa.*, users.username FROM siswa JOIN users ON siswa.user_id = users.id ORDER BY siswa.id DESC");
    $query_guru  = mysqli_query($koneksi, "SELECT guru.*, users.username FROM guru JOIN users ON guru.user_id = users.id ORDER BY guru.id DESC");
}
?>

<div class="container mt-4 mb-5">
    <h2 class="text-center mb-4 fw-bold">Data Sistem Informasi Akademik</h2>

    <ul class="nav nav-pills justify-content-center mb-4" id="pills-tab" role="tablist">
        <?php if ($role_login === 'admin') : ?>
        <li class="nav-item">
            <button class="nav-link active" id="pills-users-tab" data-bs-toggle="pill" data-bs-target="#pills-users" type="button" role="tab">Tabel Semua Akun</button>
        </li>
        <?php endif; ?>
        <li class="nav-item">
            <button class="nav-link <?= ($role_login !== 'admin') ? 'active' : '' ?>" id="pills-siswa-tab" data-bs-toggle="pill" data-bs-target="#pills-siswa" type="button" role="tab">Tabel Data Siswa</button>
        </li>
        <?php if ($role_login !== 'siswa') : ?>
        <li class="nav-item">
            <button class="nav-link" id="pills-guru-tab" data-bs-toggle="pill" data-bs-target="#pills-guru" type="button" role="tab">Tabel Data Guru</button>
        </li>
        <?php endif; ?>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        
        <?php if ($role_login === 'admin') : ?>
        <!-- Tabel Users (Khusus Admin) -->
        <div class="tab-pane fade show active" id="pills-users" role="tabpanel">
            <div class="table-responsive shadow-sm">
                <table class="table table-bordered table-striped text-center align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created at</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query_users)) : ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><span class="badge bg-secondary"><?= $row['role']; ?></span></td>
                            <td><?= $row['created_at']; ?></td>
                            <td>
                                <a href="form_edit.php?id=<?= $row['id']; ?>&role=<?= $row['role']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="hapus.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?');">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabel Siswa -->
        <div class="tab-pane fade <?= ($role_login !== 'admin') ? 'show active' : '' ?>" id="pills-siswa" role="tabpanel">
            <div class="table-responsive shadow-sm">
                <table class="table table-bordered table-striped text-center align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th>L/P</th>
                            <th>Username Akun</th>
                            <?php if ($role_login === 'admin') : ?><th>Aksi</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_siswa)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nis']); ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['kelas']); ?></td>
                            <td><?= htmlspecialchars($row['jenis_kelamin']); ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <?php if ($role_login === 'admin') : ?>
                            <td>
                                <a href="form_edit.php?id=<?= $row['user_id']; ?>&role=siswa" class="btn btn-sm btn-warning">Edit</a>
                                <a href="hapus.php?id=<?= $row['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?');">Hapus</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($role_login !== 'siswa') : ?>
        <!-- Tabel Guru -->
        <div class="tab-pane fade" id="pills-guru" role="tabpanel">
            <div class="table-responsive shadow-sm">
                <table class="table table-bordered table-striped text-center align-middle mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Lengkap</th>
                            <th>L/P</th>
                            <th>Username Akun</th>
                            <?php if ($role_login === 'admin') : ?><th>Aksi</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($query_guru)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nip']); ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['jenis_kelamin']); ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <?php if ($role_login === 'admin') : ?>
                            <td>
                                <a href="form_edit.php?id=<?= $row['user_id']; ?>&role=guru" class="btn btn-sm btn-warning">Edit</a>
                                <a href="hapus.php?id=<?= $row['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?');">Hapus</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'Footer.php'; ?>