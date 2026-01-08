<?php
require 'db.php';

// Ambil semua data penjualan
$result = $conn->query("
    SELECT id, sale_date, total
    FROM sales
    ORDER BY sale_date DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Penjualan – Kasir Elsya</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-logo">KE</div>
            <div class="brand-text">
                <h1>Riwayat Penjualan</h1>
                <span>Daftar transaksi yang sudah tersimpan</span>
            </div>
        </div>
        <nav class="nav-links">
            <a href="index.php">🏠 Beranda</a>
            <a href="transaksibaru.php">🧾 Transaksi Baru</a>
        </nav>
    </header>

    <h2 style="margin-top:16px;">Daftar Transaksi</h2>
    <p style="font-size:13px; color:#9a3412; margin-top:0;">
        Di bawah ini adalah daftar semua transaksi yang tersimpan pada tabel <code>sales</code>.
        Kamu bisa klik <b>Lihat Struk</b> untuk melihat detail dan mencetak nota.
    </p>

    <div class="table-wrapper">
        <table>
            <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Total (Rp)</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['id']; ?></td>
                        <td><?= $row['sale_date']; ?></td>
                        <td>Rp<?= number_format($row['total'], 0, ',', '.'); ?></td>
                        <td>
                            <a class="btn btn-secondary"
                               href="receipt.php?sale_id=<?= $row['id']; ?>"
                               target="_blank">
                                Lihat Struk
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Belum ada transaksi.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
