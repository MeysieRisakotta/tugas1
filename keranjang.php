<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fungsi: Hapus item keranjang jika ada parameter id
if (isset($_GET['hapus'])) {
    $cart_id = (int)$_GET['hapus'];

    // Pastikan item keranjang milik user ini
    $cek = $conn->query("SELECT * FROM cart WHERE ID = $cart_id AND USER_ID = $user_id");
    if ($cek->num_rows > 0) {
        $conn->query("DELETE FROM cart WHERE ID = $cart_id");
    }

    // Redirect agar tidak mengulang hapus saat refresh
    header("Location: keranjang.php");
    exit();
}

// Fungsi: Tambah produk ke keranjang (jika dikirim via POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produk_id'])) {
    $produk_id = (int)$_POST['produk_id'];

    $cek = $conn->query("SELECT * FROM cart WHERE USER_ID = $user_id AND PRODUK_ID = $produk_id");

    if ($cek->num_rows > 0) {
        $conn->query("UPDATE cart SET JUMLAH = JUMLAH + 1 WHERE USER_ID = $user_id AND PRODUK_ID = $produk_id");
    } else {
        $conn->query("INSERT INTO cart (USER_ID, PRODUK_ID, JUMLAH) VALUES ($user_id, $produk_id, 1)");
    }

    header("Location: keranjang.php");
    exit();
}

// Ambil isi keranjang user
$query = "SELECT cart.ID AS cart_id, cart.JUMLAH, produk.nama_produk, produk.harga 
          FROM cart 
          JOIN produk ON cart.PRODUK_ID = produk.id 
          WHERE cart.USER_ID = $user_id";
$result = $conn->query($query);

$total = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - GreenMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: #f0fff0">
<div class="container mt-5">
    <h2 class="mb-4">Keranjang Belanja Anda</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()):
                    $subtotal = $row['harga'] * $row['JUMLAH'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><?= $row['JUMLAH'] ?></td>
                    <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    <td>
                        <a href="keranjang.php?hapus=<?= $row['cart_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini dari keranjang?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <h4>Total: Rp <?= number_format($total, 0, ',', '.') ?></h4>
        <a href="checkout.php" class="btn btn-success">Checkout</a>
    <?php else: ?>
        <div class="alert alert-info">Keranjang Anda kosong.</div>
    <?php endif; ?>

    <a href="beranda.php" class="btn btn-outline-success mt-4">Kembali ke Beranda</a>
</div>
</body>
</html>
