<?php
require 'db.php';

if (!isset($_GET['sale_id'])) {
    echo "ID transaksi tidak ditemukan.";
    exit;
}

$sale_id = (int)$_GET['sale_id'];

// Ambil data header transaksi
$stmt = $conn->prepare("SELECT sale_date, total FROM sales WHERE id = ?");
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$stmt->bind_result($sale_date, $total);
$stmt->fetch();
$stmt->close();

if ($sale_date === null) {
    echo "Transaksi tidak ditemukan.";
    exit;
}

// Ambil item transaksi
$stmt = $conn->prepare("
    SELECT p.name, si.qty, si.price, si.subtotal
    FROM sale_items si
    JOIN products p ON si.product_id = p.id
    WHERE si.sale_id = ?
");
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$result = $stmt->get_result();
$items  = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #<?= $sale_id; ?> – Kasir Elsya</title>
    <style>
        /* Layout kecil ala nota kasir */
        body {
            margin: 0;
            font-family: "Menlo", "Consolas", monospace;
            background: #f9fafb;
        }
        .receipt {
            width: 320px;
            max-width: 100%;
            margin: 12px auto;
            background: #ffffff;
            padding: 12px 14px;
            border: 1px dashed #9ca3af;
        }
        .store-name {
            text-align: center;
            font-weight: 700;
            font-size: 16px;
        }
        .store-info {
            text-align: center;
            font-size: 11px;
            margin-bottom: 8px;
        }
        .line {
            border-top: 1px dashed #9ca3af;
            margin: 6px 0;
        }
        .meta {
            font-size: 11px;
            margin-bottom: 4px;
        }
        .meta span {
            display: block;
        }
        .items {
            font-size: 11px;
            margin-top: 4px;
        }
        .items-header {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
        }
        .item-name {
            flex: 1;
            padding-right: 4px;
        }
        .item-qty {
            width: 36px;
            text-align: right;
        }
        .item-price,
        .item-subtotal {
            width: 70px;
            text-align: right;
        }
        .totals {
            font-size: 12px;
            margin-top: 6px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
        }
        .thanks {
            text-align: center;
            font-size: 11px;
            margin-top: 10px;
        }
        .actions {
            text-align: center;
            margin-top: 10px;
        }
        .btn-print {
            padding: 6px 12px;
            font-size: 11px;
            border-radius: 999px;
            border: none;
            background: #111827;
            color: #f9fafb;
            cursor: pointer;
        }

        @media print {
            .actions {
                display: none;
            }
            body {
                background: #ffffff;
            }
        }
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
        <span>ID Transaksi : #<?= $sale_id; ?></span>
        <span>Tanggal      : <?= $sale_date; ?></span>
    </div>

    <div class="line"></div>

    <div class="items">
        <div class="items-header">
            <span class="item-name">Item</span>
            <span class="item-qty">Qty</span>
            <span class="item-price">Harga</span>
            <span class="item-subtotal">Subtotal</span>
        </div>
        <?php foreach ($items as $item): ?>
            <div class="item-row">
                <span class="item-name"><?= htmlspecialchars($item['name']); ?></span>
                <span class="item-qty"><?= $item['qty']; ?></span>
                <span class="item-price"><?= number_format($item['price'], 0, ',', '.'); ?></span>
                <span class="item-subtotal"><?= number_format($item['subtotal'], 0, ',', '.'); ?></span>
            </div>
        <?php endforeach; ?>
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
        <button class="btn-print" onclick="window.print()">Cetak Struk</button>
    </div>
</div>

</body>
</html>
