<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$role_login = $_SESSION['role'] ?? '';
$username_login = $_SESSION['username'] ?? '';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIACAD SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="daftar_terpadu.php">SIACAD SMK</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="daftar_terpadu.php">Daftar Terpadu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="mapel.php">Data Mapel</a>
        </li>
        
        <?php if ($role_login === 'admin') : ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">Tambah User</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="form_admin.php">Tambah Admin</a></li>
            <li><a class="dropdown-item" href="form.php">Tambah Siswa</a></li>
            <li><a class="dropdown-item" href="form_guru.php">Tambah Guru</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="form_every.php">Tambah Semua User</a></li>
          </ul>
        </li>
        <?php endif; ?>
      </ul>
      
      <div class="d-flex align-items-center gap-3">
        <span class="badge bg-primary text-capitalize">Halo, <?= htmlspecialchars($username_login); ?> (<?= $role_login; ?>)</span>
        <a href="logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin logout?')">Logout</a>
      </div>
    </div>
  </div>
</nav>