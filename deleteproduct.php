<?php
// deleteproduct.php
require 'db.php';

// cek apakah ada id di URL
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$id = $_GET['id'];

// hapus produk berdasarkan ID
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// balik ke halaman kelola menu
header("Location: products.php");
exit;
