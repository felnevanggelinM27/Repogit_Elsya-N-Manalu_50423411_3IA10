<?php
require 'auth.php';
require 'db.php';

$user_id = $_SESSION['user_id'];

// Ambil riwayat penjualan MILIK USER LOGIN
$stmt = $conn->prepare("
    SELECT id, sale_date, total
    FROM sales
    WHERE user_id = ?
    ORDER BY sale_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container mt-4">
    <h3 class="mb-3">🧾 Riwayat Penjualan</h3>

    <?php if ($result->num_rows === 0): ?>
        <!-- USER BARU / BELUM ADA TRANSAKSI -->
        <div class="alert alert-info">
            Belum ada riwayat transaksi.
        </div>
    <?php else: ?>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d M Y H:i', strtotime($row['sale_date'])) ?></td>
                    <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                    <td>
                        <a href="receipt.php?sale_id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Lihat Struk</a>
                           class="btn btn-secondary btn-sm">
                            Lihat Struk
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mt-3">⬅ Kembali</a>
</div>

</body>
</html>
