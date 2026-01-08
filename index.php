<?php
// index.php - beranda
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir by Elsya</title>
    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- STYLE  -->
<link rel="stylesheet" href="style.css">

</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<body>
<div class="container mt-5 p-4 shadow rounded bg-light">


    <!-- Header / Brand -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-logo">KE</div>
            <div class="brand-text">
                <h1>Kasir by Elsya</h1>
                <span>Sistem kasir makanan & minuman</span>
            </div>
        </div>

        <nav class="nav-links">
            <a href="products.php" class="btn btn-primary">Kelola Menu</a>
            <a href="transaksibaru.php" class="btn btn-success">Transaksi Baru</a>
            <a href="saleshistory.php" class="btn btn-info">Riwayat Penjualan</a>
    
        </nav>
    </header>

    <!-- Content -->
    <section class="main-section">
        <h2 style="color:#1e3a8a; font-size:26px;">WELCOME</h2>
        <p style="font-size:14px; color:#1e40af;">
            Selamat datang di <b>Kasir by Elsya</b>.  
            Gunakan aplikasi ini untuk mengelola menu makanan & minuman, 
            serta mencatat transaksi pelanggan dengan cepat.
        </p>

        <ul class="menu-list">
            <li><a href="products.php">Kelola menu makanan & minuman</a></li>
            <li><a href="transaksibaru.php">Mulai transaksi kasir (pilih menu & hitung total)</a></li>
            <li><a href="saleshistory.php">Riwayat penjualan (lihat struk)</a></li>
        </ul>
    </section>

</div>
</body>
</html>
