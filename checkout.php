<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil isi keranjang
$cart_query = $conn->query("SELECT cart.*, produk.nama_produk, produk.harga 
                            FROM cart 
                            JOIN produk ON cart.produk_id = produk.id 
                            WHERE cart.user_id = $user_id");

$total = 0;
$items = [];
while ($item = $cart_query->fetch_assoc()) {
    $subtotal = $item['harga'] * $item['JUMLAH'];
    $total += $subtotal;
    $items[] = $item;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metode = $_POST['metode'];
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    // Simpan ke tabel pesanan
    $stmt1 = $conn->prepare("INSERT INTO pesanan (user_id, total_harga, alamat, status, created_at) VALUES (?, ?, ?, 'DIBAYAR', NOW())");
    $stmt1->bind_param("ids", $user_id, $total, $alamat);
    $stmt1->execute();
    $pesanan_id = $stmt1->insert_id;

    // Simpan detail pesanan
    $stmt2 = $conn->prepare("INSERT INTO detail_pesanan (pesanan_id, produk_id, harga, jumlah) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $pid = $item['PRODUK_ID'];
        $harga = $item['harga'];
        $jumlah = $item['JUMLAH'];
    }

    // Simpan transaksi
    $stmt3 = $conn->prepare("INSERT INTO transaksi (user_id, pesanan_id, metode_pembayaran, status_pembayaran, tanggal_transaksi) 
                             VALUES (?, ?, ?, 'Sudah Dibayar', NOW())");
    $stmt3->bind_param("iis", $user_id, $pesanan_id, $metode);
    $stmt3->execute();

    // Hapus isi keranjang
    $conn->query("DELETE FROM cart WHERE user_id = $user_id");

    // Redirect ke halaman riwayat
    header("Location: riwayat.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - GreenMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f0fff0;">
<div class="container mt-5">
    <h2 class="mb-4">Checkout</h2>

    <form method="POST">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): 
                    $subtotal = $item['harga'] * $item['JUMLAH'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                        <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td><?= $item['JUMLAH'] ?></td>
                        <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h5>Total Pembayaran: <strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></h5>

        <div class="mb-3 mt-4">
            <label for="alamat" class="form-label">Alamat Pengiriman</label>
            <textarea class="form-control" name="alamat" id="alamat" required></textarea>
        </div>

        <div class="mb-3">
            <label for="metode" class="form-label">Metode Pembayaran</label>
            <select class="form-select" name="metode" id="metode" required>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="COD">Bayar di Tempat (COD)</option>
                <option value="E-Wallet">E-Wallet</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Proses Pembayaran</button>
        <a href="beranda.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
