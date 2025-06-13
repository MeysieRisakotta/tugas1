<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: beranda.php");
    exit();
}

$produk_id = intval($_GET['id']);
$produk = $conn->query("SELECT * FROM produk WHERE id = $produk_id")->fetch_assoc();
if (!$produk) {
    echo "Produk tidak ditemukan.";
    exit();
}

// Ambil review
$reviews = $conn->query("SELECT r.*, u.nama FROM review r 
                         JOIN user u ON r.user_id = u.id 
                         WHERE r.produk_id = $produk_id 
                         ORDER BY r.created_at DESC");

// Proses kirim review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating']);
    $comment = $conn->real_escape_string($_POST['komentar']);

    $conn->query("INSERT INTO review (produk_id, user_id, rating, comment, created_at) 
                  VALUES ($produk_id, $user_id, $rating, '$comment', NOW())");

    header("Location: detail_produk.php?id=$produk_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($produk['nama_produk']) ?> - GreenMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: #f9fff9">
<div class="container mt-5">
    <div class="row g-4">
        <div class="col-lg-5 col-md-6">
            <img src="uploads/<?= htmlspecialchars($produk['GAMBAR']) ?>" class="img-fluid rounded shadow-sm" alt="Gambar Produk">
        </div>
        <div class="col-lg-7 col-md-6">
            <h2 class="mb-3"><?= htmlspecialchars($produk['NAMA_PRODUK']) ?></h2>
            <h4 class="text-success">Rp <?= number_format($produk['HARGA'], 0, ',', '.') ?></h4>
            <p class="mt-3"><?= nl2br(htmlspecialchars($produk['DESKRIPSI'])) ?></p>
            <a href="beranda.php" class="btn btn-outline-success mt-4">Kembali ke Beranda</a>
        </div>
    </div>

    <hr class="my-5">
    <h4>Ulasan Pembeli</h4>
    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($r = $reviews->fetch_assoc()): ?>
            <div class="border rounded p-3 mb-3 bg-light">
                <strong><?= htmlspecialchars($r['nama']) ?></strong> - Rating: <?= $r['RATING'] ?>/5<br>
                <small><?= date('d-m-Y H:i', strtotime($r['CREATED_AT'])) ?></small>
                <p class="mb-0 mt-2"><?= nl2br(htmlspecialchars($r['COMMENT'])) ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-muted">Belum ada ulasan.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <hr>
        <h5>Berikan Ulasan</h5>
        <form method="POST">
            <div class="mb-2">
                <label for="rating" class="form-label">Rating (1-5)</label>
                <select name="rating" id="rating" class="form-select" required>
                    <option value="5">5 - Sangat Baik</option>
                    <option value="4">4 - Baik</option>
                    <option value="3">3 - Cukup</option>
                    <option value="2">2 - Kurang</option>
                    <option value="1">1 - Buruk</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="komentar" class="form-label">Komentar</label>
                <textarea name="komentar" id="komentar" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Review</button>
        </form>
    <?php else: ?>
        <p class="mt-4 text-muted">Silakan <a href="index.php">login</a> untuk memberi ulasan.</p>
    <?php endif; ?>
</div>
</body>
</html>