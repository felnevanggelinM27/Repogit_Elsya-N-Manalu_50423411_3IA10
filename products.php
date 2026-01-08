<?php
require 'db.php';

// Handle tambah produk baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = $_POST['name'] ?? '';
    $price       = $_POST['price'] ?? 0;
    $stock       = $_POST['stock'] ?? 0;
    $category    = $_POST['category'] ?? 'Makanan';
    $description = $_POST['description'] ?? '';

    if ($name !== '' && $price >= 0 && $stock >= 0) {
        $stmt = $conn->prepare(
            "INSERT INTO products (name, price, stock, category, description)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sdiss", $name, $price, $stock, $category, $description);
        $stmt->execute();
        $stmt->close();
        header("Location: products.php");
        exit;
    }
}

// Ambil semua produk
$result = $conn->query("SELECT * FROM products ORDER BY category, name");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Elsya – Kelola Menu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-logo">KE</div>
            <div class="brand-text">
                <h1>Kelola Menu</h1>
                <span>Makanan &amp; minuman yang dijual di kasir</span>
            </div>
        </div>
        <nav class="nav-links">
            <a href="index.php">🏠 Beranda</a>
            <a href="transaksibaru.php">🧾 Transaksi</a>
        </nav>
    </header>

    <!-- FORM TAMBAH MENU -->
    <section style="margin-top:10px;">
        <h2>Tambah Menu Baru</h2>
        <p style="font-size:13px; color:#9a3412; margin-top:0;">
            Isi form di bawah untuk menambah makanan atau minuman baru.
        </p>

        <form method="post">
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" name="name" required placeholder="Contoh: Nasi Goreng Spesial">
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="category" style="width:100%; padding:7px 9px; border-radius:10px; border:1px solid #fed7aa; background:#fffbeb;">
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                </select>
            </div>

            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="price" step="0.01" required placeholder="15000">
            </div>

            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stock" required placeholder="30">
            </div>

            <div class="form-group">
                <label>Keterangan / Deskripsi</label>
                <input type="text" name="description" placeholder="Contoh: pedas, manis, topping keju, dll.">
            </div>

            <button type="submit" class="btn btn-primary">Simpan Menu</button>
        </form>
    </section>

    <!-- DAFTAR MENU -->
    <section style="margin-top:22px;">
        <h2>Daftar Menu</h2>
        <p style="font-size:13px; color:#9a3412; margin-top:0;">
            Tabel di bawah ini menampilkan semua makanan dan minuman lengkap dengan harga, stok, dan keterangannya.
        </p>

        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['category']); ?></td>
                            <td>Rp<?= number_format($row['price'], 0, ',', '.'); ?></td>
                            <td><?= $row['stock']; ?></td>
                            <td><?= htmlspecialchars($row['description']); ?></td>
                            <td>
                                <a class="btn btn-secondary" href="editproduct.php?id=<?= $row['id']; ?>">Edit</a>
                                <a class="btn btn-danger"
                                   href="deleteproduct.php?id=<?= $row['id']; ?>"
                                   onclick="return confirm('Yakin hapus menu ini?');">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">Belum ada menu. Silakan tambahkan menu baru di atas.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>
</body>
</html>
