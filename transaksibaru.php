<?php
require 'db.php';

// ambil data produk
$result = $conn->query("SELECT * FROM products ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Elsya  – Transaksi Baru</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // ------------ MODAL CUSTOMER ------------
        function openCustomerModal() {
            document.getElementById('customerModal').style.display = 'flex';
        }
        function closeCustomerModal() {
            document.getElementById('customerModal').style.display = 'none';
        }
        function addCustomer() {
            // ambil nilai form (kalau nanti mau dipakai)
            const name  = document.getElementById('cust_name').value;
            const email = document.getElementById('cust_email').value;
            const phone = document.getElementById('cust_phone').value;

            // contoh sederhana: tampilkan di console / alert
            console.log("Customer:", name, email, phone);
            // bisa juga dimasukkan ke hidden input form transaksi kalau mau disimpan ke DB

            closeCustomerModal();
        }

        // ------------ HITUNG TOTAL ------------
        function hitungSubtotal() {
            const rows = document.querySelectorAll("tbody tr[data-price]");
            let total = 0;
            rows.forEach(row => {
                const price = parseFloat(row.getAttribute("data-price"));
                const qtyInput = row.querySelector("input[name^='qty']");
                const qty = parseInt(qtyInput.value) || 0;
                const subtotalCell = row.querySelector(".subtotal");
                const subtotal = price * qty;
                subtotalCell.textContent = subtotal.toLocaleString('id-ID');
                total += subtotal;
            });
            document.getElementById("total-display").textContent = total.toLocaleString('id-ID');
        }
    </script>
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <header class="app-header">
        <div class="brand">
            <div class="brand-logo">RR</div>
            <div class="brand-text">
                <h1>Transaksi Kasir</h1>
                <span>Pilih menu makanan & minuman lalu proses pembayaran</span>
            </div>
        </div>
        <nav class="nav-links">
            <a href="index.php">🏠 Beranda</a>
            <a href="products.php">🍱 Kelola Menu</a>
        </nav>
    </header>

    <!-- TOMBOL ADD CUSTOMER -->
    <div style="display:flex; justify-content: space-between; align-items:center; margin-top:4px;">
        <h2 style="margin: 8px 0;">Transaksi Baru</h2>
        <button type="button" class="btn btn-secondary" onclick="openCustomerModal()">
            + Add Customer
        </button>
    </div>
    <p style="font-size:13px; color:#9a3412; margin-top:0;">
        Masukkan jumlah (Qty) untuk setiap menu yang dipesan. Total akan dihitung otomatis.
    </p>

    <!-- FORM TRANSAKSI -->
    <form action="checkout.php" method="post" oninput="hitungSubtotal()">
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Qty</th>
                    <th>Subtotal (Rp)</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-price="<?= $row['price']; ?>">
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td>Rp<?= number_format($row['price'], 0, ',', '.'); ?></td>
                            <td><?= $row['stock']; ?></td>
                            <td>
                                <input type="number" name="qty[<?= $row['id']; ?>]" min="0"
                                       max="<?= $row['stock']; ?>" value="0">
                            </td>
                            <td class="subtotal">0</td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">Belum ada menu yang terdaftar.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="summary-row" style="margin-top:14px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <span style="font-size:13px; color:#9a3412;">Total sementara</span>
                <div style="font-size:22px; font-weight:700; color:#7c2d12;">
                    Rp <span id="total-display">0</span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        </div>
    </form>
</div>

<!-- ============== MODAL ADD CUSTOMER ============== -->
<div id="customerModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span>Add Customer</span>
            <button type="button" class="close-btn" onclick="closeCustomerModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="modal-grid">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" id="cust_name" placeholder="Nama pelanggan">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="text" id="cust_email" placeholder="email@example.com">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" id="cust_phone" placeholder="08xxx">
                </div>
                <div class="form-group">
                    <label>Custom Field 1</label>
                    <input type="text" id="cust_custom1" placeholder="Catatan 1">
                </div>
                <div class="form-group">
                    <label>Custom Field 2</label>
                    <input type="text" id="cust_custom2" placeholder="Catatan 2">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeCustomerModal()">Close</button>
            <button type="button" class="btn btn-primary" onclick="addCustomer()">Add Customer</button>
        </div>
    </div>
</div>

</body>
</html>
