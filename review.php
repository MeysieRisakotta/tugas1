<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil produk dari pesanan user
$produk_result = $conn->query("
    SELECT DISTINCT p.id, p.nama_produk 
    FROM detail_pesanan dp 
    JOIN pesanan ps ON dp.pesanan_id = ps.id 
    JOIN produk p ON dp.produk_id = p.id 
    WHERE ps.user_id = $user_id
");

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produk_id = $_POST['produk_id'];
    $rating = $_POST['rating'];
    $komentar = $conn->real_escape_string($_POST['komentar']);

    $conn->query("INSERT INTO review (user_id, produk_id, rating, komentar) 
                  VALUES ($user_id, $produk_id, $rating, '$komentar')");

    echo "<script>alert('Terima kasih atas ulasannya!'); window.location='review.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Review Produk - GreenMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: #f9fff9">
<div class="container mt-5">
    <h2 class="mb-4">Berikan Penilaian Produk</h2>
    <form method="POST" class="mb-5">
        <div class="mb-3">
            <label for="produk_id" class="form-label">Pilih Produk</label>
            <select name="produk_id" class="form-select" required>
                <?php while ($p = $produk_result->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>"><?= $p['nama_produk'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select" required>
                <option value="5">5 - Sangat Baik</option>
                <option value="4">4 - Baik</option>
                <option value="3">3 - Cukup</option>
                <option value="2">2 - Buruk</option>
                <option value="1">1 - Sangat Buruk</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Komentar</label>
            <textarea name="komentar" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Kirim Review</button>
    </form>

    <a href="beranda.php" class="btn btn-outline-secondary">Kembali ke Beranda</a>
</div>
</body>
</html>