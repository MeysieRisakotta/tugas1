<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil semua pesanan milik user
$pesanan_query = $conn->query("SELECT * FROM pesanan WHERE USER_ID = $user_id ORDER BY CREATED_AT DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan - GreenMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: #f5fff5;">
<div class="container mt-5">
    <h2 class="mb-4">Riwayat Pesanan Anda</h2>

    <?php while ($pesanan = $pesanan_query->fetch_assoc()): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                Tanggal: <?= date('d-m-Y H:i', strtotime($pesanan['CREATED_AT'])) ?> | 
                Total: Rp <?= number_format($pesanan['TOTAL_HARGA'], 0, ',', '.') ?>
            </div>
            <div class="card-body">
                <p><strong>Alamat Pengiriman:</strong> <?= htmlspecialchars($pesanan['ALAMAT']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($pesanan['STATUS']) ?></p>
            </div>
        </div>
    <?php endwhile; ?>

    <a href="beranda.php" class="btn btn-outline-success">Kembali ke Beranda</a>
</div>
</body>
</html>
