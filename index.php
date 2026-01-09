<?php
require 'auth.php'; // proteksi halaman (wajib login)
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kasir by Elsya</title>

  <!-- BOOTSTRAP -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >

  <!-- STYLE -->
  <!-- pakai versioning biar CSS ga ke-cache -->
  <link rel="stylesheet" href="style.css?v=2">
</head>

<body>
  <div class="container mt-5 p-4 shadow rounded bg-light">

    <!-- HEADER -->
    <header class="app-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">

      <!-- BRAND -->
      <div class="brand d-flex align-items-center gap-3">
        <div class="brand-logo">KE</div>

        <div class="brand-text">
          <h1 class="m-0">Kasir by Elsya</h1>
          <span>Sistem kasir makanan & minuman</span>
        </div>
      </div>

      <!-- NAVIGATION (BUTTON) -->
      <div class="nav-actions">
        <a href="products.php" class="nav-btn">Kelola Menu</a>
        <a href="transaksibaru.php" class="nav-btn">Transaksi Baru</a>
        <a href="saleshistory.php" class="nav-btn">Riwayat Pemesanan</a>

        <a href="logout.php"
           class="nav-btn nav-btn-logout"
           onclick="return confirm('Yakin ingin logout?')">
          Logout
        </a>
      </div>

    </header>

    <!-- CONTENT -->
    <section class="main-section mt-4">
      <h2 style="color:#1e3a8a; font-size:26px;">WELCOME</h2>

      <p style="font-size:14px; color:#1e40af;">
        Selamat datang di <b>Kasir by Elsya</b>.
        Aplikasi ini digunakan untuk mengelola menu makanan & minuman,
        serta mencatat transaksi pelanggan dengan cepat.
      </p>

      <ul class="menu-list">
        <li><a href="products.php">Kelola menu makanan & minuman</a></li>
        <li><a href="transaksibaru.php">Mulai transaksi kasir (pilih menu & hitung total)</a></li>
        <li><a href="saleshistory.php">Riwayat penjualan (lihat struk)</a></li>
      </ul>
    </section>

  </div>

  <!-- BOOTSTRAP JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
