<?php
require 'auth.php';
require 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: products.php");
    exit;
}

// Ambil data produk
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    echo "Produk tidak ditemukan.";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;

    if ($name !== '' && $price >= 0 && $stock >= 0) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?");
        $stmt->bind_param("sdii", $name, $price, $stock, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: products.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Edit Produk</h1>
    <p><a href="products.php">&laquo; Kembali ke Daftar Produk</a></p>

    <form method="post">
        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Harga (Rp)</label>
            <input type="number" name="price" step="0.01" value="<?= $product['price']; ?>" required>
        </div>
        <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stock" value="<?= $product['stock']; ?>" required>
        </div>
        <button type="submit">Update</button>
    </form>
</div>
</body>
</html>
