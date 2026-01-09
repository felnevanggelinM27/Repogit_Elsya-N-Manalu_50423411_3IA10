<?php
// checkout.php
require 'auth.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: transaksibaru.php");
  exit;
}

$userId = (int)($_SESSION['user_id'] ?? 0);
if ($userId <= 0) {
  die("Silakan login terlebih dahulu.");
}

$qtys  = $_POST['qty'] ?? [];
$total = 0;
$items = [];

/**
 * Helper tampilin halaman error sederhana
 */
function renderErrorPage($title, $subtitle, $message) {
  ?>
  <!DOCTYPE html>
  <html lang="id">
  <head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
  <div class="container">
    <header class="app-header">
      <div class="brand">
        <div class="brand-logo">KE</div>
        <div class="brand-text">
          <h1><?= htmlspecialchars($title) ?></h1>
          <span><?= htmlspecialchars($subtitle) ?></span>
        </div>
      </div>
      <nav class="nav-links">
        <a href="transaksibaru.php">🔁 Kembali ke Transaksi</a>
        <a href="products.php">🍱 Kelola Menu</a>
      </nav>
    </header>

    <p style="margin-top:16px; font-size:14px; color:#b91c1c; background:#fee2e2; padding:10px 12px; border-radius:10px;">
      <?= $message ?>
    </p>
  </div>
  </body>
  </html>
  <?php
}

// 1) Validasi & hitung total + kumpulkan item
foreach ($qtys as $product_id => $qty) {
  $product_id = (int)$product_id;
  $qty = (int)$qty;
  if ($product_id <= 0) continue;
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

  if ($qty > (int)$stock) {
    $msg = "Stok untuk menu <b>" . htmlspecialchars($name) . "</b> tidak mencukupi. "
         . "Stok tersedia: <b>{$stock}</b>, diminta: <b>{$qty}</b>.";
    renderErrorPage("Transaksi Gagal", "Stok menu tidak mencukupi", $msg);
    exit;
  }

  $price = (float)$price;
  $subtotal = $price * $qty;
  $total += $subtotal;

  $items[] = [
    'product_id' => $product_id,
    'name'       => $name,
    'qty'        => $qty,
    'price'      => $price,
    'subtotal'   => $subtotal
  ];
}

if (empty($items)) {
  renderErrorPage(
    "Tidak Ada Menu",
    "Belum ada qty yang diisi",
    "Tidak ada menu yang dipilih. Silakan isi jumlah (Qty) minimal pada satu menu."
  );
  exit;
}

// 2) Simpan ke DB pakai TRANSACTION biar aman
$conn->begin_transaction();

try {
  // Insert header sales (WAJIB simpan user_id)
  // Pastikan tabel sales kamu punya kolom user_id, sale_date, total
  // sale_date bisa auto NOW() atau default timestamp
  $stmt = $conn->prepare("INSERT INTO sales (user_id, sale_date, total) VALUES (?, NOW(), ?)");
  $stmt->bind_param("id", $userId, $total);
  $stmt->execute();
  $stmt->close();

  // Ambil ID transaksi yang BENAR (pasti bukan 0)
  $sale_id = (int)$conn->insert_id;
  if ($sale_id <= 0) {
    throw new Exception("Gagal membuat transaksi (sale_id kosong).");
  }

  // Insert sale_items + update stok
  foreach ($items as $item) {
    // Insert detail
    $stmt = $conn->prepare("
      INSERT INTO sale_items (sale_id, product_id, qty, price, subtotal)
      VALUES (?, ?, ?, ?, ?)
    ");
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

    // Kurangi stok (biar tidak minus, amanin dengan stock >= qty)
    $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
    $stmt->bind_param("iii", $item['qty'], $item['product_id'], $item['qty']);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
      $stmt->close();
      throw new Exception("Stok berubah saat transaksi diproses. Coba ulangi transaksi.");
    }
    $stmt->close();
  }

  $conn->commit();

} catch (Exception $e) {
  $conn->rollback();
  renderErrorPage("Transaksi Gagal", "Terjadi kesalahan", htmlspecialchars($e->getMessage()));
  exit;
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
    ID Transaksi: <b>#<?= (int)$sale_id; ?></b>
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
            <td><?= (int)$item['qty']; ?></td>
            <td>Rp <?= number_format((float)$item['price'], 0, ',', '.'); ?></td>
            <td>Rp <?= number_format((float)$item['subtotal'], 0, ',', '.'); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="margin-top:16px; display:flex; justify-content:flex-end;">
    <div style="text-align:right;">
      <span style="font-size:13px; color:#9a3412;">Total Bayar</span>
      <div style="font-size:22px; font-weight:700; color:#7c2d12;">
        Rp <?= number_format((float)$total, 0, ',', '.'); ?>
      </div>

      <div style="margin-top:14px; display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
        <a href="transaksibaru.php" class="btn btn-secondary">🔁 Transaksi Baru</a>

        <!-- INI YANG PASTI BENER -->
        <a href="receipt.php?sale_id=<?= (int)$sale_id; ?>" target="_blank" class="btn btn-secondary">
          🖨️ Cetak Struk
        </a>
      </div>
    </div>
  </div>

</div>

</body>
</html>
