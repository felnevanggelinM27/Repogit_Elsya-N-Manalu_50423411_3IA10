<?php
// checkout.php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: transaksibaru.php");
    exit;
}

$qtys  = $_POST['qty'] ?? [];
$total = 0;
$items = [];

// 1. Validasi & hitung total
foreach ($qtys as $product_id => $qty) {
    $qty = (int)$qty;
    if ($qty <= 0) continue;

    // ambil data produk
    $stmt = $conn->prepare("SELECT name, price, stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($name, $price, $stock);
    $stmt->fetch();
    $stmt->close();

    if ($name === null) {
        continue; // produk tidak ditemukan
    }

    if ($qty > $stock) {
        // stok kurang → tampilkan pesan
        $error = "Stok untuk menu <b>" . htmlspecialchars($name) . "</b> tidak mencukupi. "
               . "Stok tersedia: {$stock}, diminta: {$qty}.";
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Stok Tidak Cukup</title>
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
        <div class="container">
            <header class="app-header">
                <div class="brand">
                    <div class="brand-logo">KE</div>
                    <div class="brand-text">
                        <h1>Transaksi Gagal</h1>
                        <span>Stok menu tidak mencukupi</span>
                    </div>
                </div>
                <nav class="nav-links">
                    <a href="transaksibaru.php">🔁 Kembali ke Transaksi</a>
                    <a href="products.php">🍱 Kelola Menu</a>
                </nav>
            </header>

            <p style="margin-top:16px; font-size:14px; color:#b91c1c; background:#fee2e2; padding:10px 12px; border-radius:10px;">
                <?= $error; ?>
            </p>
        </div>
        </body>
        </html>
        <?php
        exit;
    }

    $subtotal = $price * $qty;
    $total   += $subtotal;

    $items[] = [
        'product_id' => $product_id,
        'name'       => $name,
        'qty'        => $qty,
        'price'      => $price,
        'subtotal'   => $subtotal
    ];
}

if (empty($items)) {
    // tidak ada item dipilih
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Tidak Ada Item</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="container">
        <header class="app-header">
            <div class="brand">
                <div class="brand-logo">KE</div>
                <div class="brand-text">
                    <h1>Tidak Ada Menu</h1>
                    <span>Belum ada qty yang diisi</span>
                </div>
            </div>
            <nav class="nav-links">
                <a href="transaksibaru.php">🔁 Kembali ke Transaksi</a>
            </nav>
        </header>

        <p style="margin-top:16px; font-size:14px;">
            Tidak ada menu yang dipilih. Silakan isi jumlah (Qty) minimal pada satu menu.
        </p>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// 2. Simpan ke tabel sales
$stmt = $conn->prepare("INSERT INTO sales (total) VALUES (?)");
$stmt->bind_param("d", $total);
$stmt->execute();
$sale_id = $stmt->insert_id;
$stmt->close();

// 3. Simpan ke sale_items + update stok
foreach ($items as $item) {
    // insert detail
    $stmt = $conn->prepare(
        "INSERT INTO sale_items (sale_id, product_id, qty, price, subtotal)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "iiidd",
        $sale_id,
        $item['product_id'],
        $item['qty'],
        $item['price'],
        $item['subtotal']
    );
    $stmt->execute();
    $stmt->close();

    // kurangi stok
    $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $stmt->bind_param("ii", $item['qty'], $item['product_id']);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Berhasil – Kasir Elsya</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-logo">KE</div>
            <div class="brand-text">
                <h1>Transaksi Berhasil</h1>
                <span>Data penjualan sudah tersimpan di sistem</span>
            </div>
        </div>
        <nav class="nav-links">
            <a href="transaksibaru.php">🔁 Transaksi Baru</a>
            <a href="index.php">🏠 Beranda</a>
        </nav>
    </header>

    <h2 style="margin-top:16px;">Ringkasan Transaksi</h2>
    <p style="font-size:13px; color:#9a3412; margin-top:0;">
        ID Transaksi: <b>#<?= $sale_id; ?></b>
    </p>

    <div class="table-wrapper">
        <table>
            <thead>
            <tr>
                <th>Menu</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']); ?></td>
                    <td><?= $item['qty']; ?></td>
                    <td>Rp<?= number_format($item['price'], 0, ',', '.'); ?></td>
                    <td>Rp<?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px; display:flex; justify-content:flex-end;">
        <div>
            <span style="font-size:13px; color:#9a3412;">Total Bayar</span>
            <div style="font-size:22px; font-weight:700; color:#7c2d12;">
                Rp <?= number_format($total, 0, ',', '.'); ?>
                <div style="margin-top:16px; display:flex; justify-content:space-between; align-items:center;">
                <a href="transaksibaru.php" class="btn btn-secondary">🔁 Transaksi Baru</a>
                <div>
                    <a href="receipt.php?sale_id=<?= $sale_id; ?>" target="_blank" class="btn btn-secondary">
                        Cetak Struk
                    </a>
                </div>
            </div>

            </div>
        </div>
    </div>

    <p style="margin-top:18px; font-size:13px; color:#9a3412;">
    
    </p>

</div>

</body>
</html>
