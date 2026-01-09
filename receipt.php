<?php
require 'auth.php';
require 'db.php';

$userId = (int)($_SESSION['user_id'] ?? 0);

// Terima id dari ?sale_id= atau ?id=
$saleId = (int)($_GET['sale_id'] ?? ($_GET['id'] ?? 0));
if ($saleId <= 0) {
  die("Akses ditolak / transaksi tidak ditemukan.");
}

// Ambil header transaksi (pastikan sesuai user yang login)
$stmt = $conn->prepare("SELECT id, user_id, sale_date, total FROM sales WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $saleId, $userId);
$stmt->execute();
$sale = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sale) {
  die("Akses ditolak / transaksi tidak ditemukan.");
}

$saleDate = $sale['sale_date'];
$total    = (float)$sale['total'];

// Ambil item transaksi
$stmt = $conn->prepare("
  SELECT p.name, si.qty, si.price, si.subtotal
  FROM sale_items si
  JOIN products p ON si.product_id = p.id
  WHERE si.sale_id = ?
  ORDER BY p.name ASC
");
$stmt->bind_param("i", $saleId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
  $items[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Struk #<?= (int)$saleId; ?> – Kasir Elsya</title>
  <style>
    body{margin:0;font-family:Menlo,Consolas,monospace;background:#f9fafb}
    .receipt{width:320px;max-width:100%;margin:12px auto;background:#fff;padding:12px 14px;border:1px dashed #9ca3af}
    .store-name{text-align:center;font-weight:700;font-size:16px}
    .store-info{text-align:center;font-size:11px;margin-bottom:8px}
    .line{border-top:1px dashed #9ca3af;margin:6px 0}
    .meta{font-size:11px;margin-bottom:4px}
    .meta span{display:block}
    .items{font-size:11px;margin-top:4px}
    .items-header,.item-row{display:flex;justify-content:space-between}
    .items-header{font-weight:700;margin-bottom:4px}
    .item-name{flex:1;padding-right:4px}
    .item-qty{width:36px;text-align:right}
    .item-price,.item-subtotal{width:70px;text-align:right}
    .totals{font-size:12px;margin-top:6px}
    .totals-row{display:flex;justify-content:space-between}
    .thanks{text-align:center;font-size:11px;margin-top:10px}
    .actions{text-align:center;margin-top:10px;display:flex;gap:8px;justify-content:center;flex-wrap:wrap}
    .btn{padding:6px 12px;font-size:11px;border-radius:999px;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
    .btn-print{background:#111827;color:#f9fafb}
    .btn-back{background:#e5e7eb;color:#111827}
    @media print{.actions{display:none}body{background:#fff}}
  </style>
</head>
<body>

<div class="receipt">
  <div class="store-name">KASIR ELSYA</div>
  <div class="store-info">
    Jl. Contoh No. 123, Samarinda<br>
    Telp: 08xx-xxxx-xxxx
  </div>

  <div class="line"></div>

  <div class="meta">
    <span>ID Transaksi : #<?= (int)$saleId; ?></span>
    <span>Tanggal      : <?= htmlspecialchars($saleDate); ?></span>
  </div>

  <div class="line"></div>

  <div class="items">
    <div class="items-header">
      <span class="item-name">Item</span>
      <span class="item-qty">Qty</span>
      <span class="item-price">Harga</span>
      <span class="item-subtotal">Subtotal</span>
    </div>

    <?php if (count($items) > 0): ?>
      <?php foreach ($items as $item): ?>
        <div class="item-row">
          <span class="item-name"><?= htmlspecialchars($item['name']); ?></span>
          <span class="item-qty"><?= (int)$item['qty']; ?></span>
          <span class="item-price"><?= number_format((float)$item['price'], 0, ',', '.'); ?></span>
          <span class="item-subtotal"><?= number_format((float)$item['subtotal'], 0, ',', '.'); ?></span>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="item-row">
        <span class="item-name">Tidak ada item.</span>
        <span class="item-qty">-</span>
        <span class="item-price">-</span>
        <span class="item-subtotal">-</span>
      </div>
    <?php endif; ?>
  </div>

  <div class="line"></div>

  <div class="totals">
    <div class="totals-row">
      <span>Total</span>
      <span>Rp <?= number_format($total, 0, ',', '.'); ?></span>
    </div>
  </div>

  <div class="line"></div>

  <div class="thanks">
    Terima kasih 🙏<br>
    Silakan datang kembali
  </div>

  <div class="actions">
    <button class="btn btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
    <a class="btn btn-back" href="saleshistory.php">↩️ Kembali</a>
  </div>
</div>

</body>
</html>
