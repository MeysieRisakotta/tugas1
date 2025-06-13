<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Ambil input pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Siapkan query dan filter dinamis
$sql = "SELECT * FROM produk WHERE 1=1";
$params = [];
$types = '';

if ($search !== '') {
    $sql .= " AND NAMA_PRODUK LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if ($kategori !== '') {
    $sql .= " AND KATEGORI = ?";
    $params[] = $kategori;
    $types .= 's';
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Ambil kategori unik untuk filter
$kategori_result = $conn->query("SELECT DISTINCT KATEGORI FROM produk");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda - GreenMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5fff5;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background-color: #2e8b57;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-img-top {
            border-radius: 20px 20px 0 0;
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">GreenMart</a>
        <form class="d-flex me-auto ms-3" method="GET" action="">
            <input class="form-control me-2" type="search" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search); ?>">
            <select class="form-select me-2" name="kategori">
                <option value="">Semua Kategori</option>
                <?php while ($row = $kategori_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['KATEGORI']); ?>" <?= $kategori === $row['KATEGORI'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($row['KATEGORI']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-light" type="submit">Cari</button>
        </form>
        <div class="d-flex align-items-center">
            <a href="riwayat.php" class="nav-link">Riwayat Pesanan</a>
            <a href="detail_produk.php" class="btn btn-light btn-sm me-3">Lihat Keranjang</a>
            <span class="me-3 text-white">Halo, <?= $_SESSION['nama']; ?>!</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<!-- Konten Produk -->
<div class="container">
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="uploads/<?= $row['GAMBAR']; ?>" class="card-img-top" alt="Produk">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="detail_produk.php?id=<?= $row['ID']; ?>" class="text-decoration-none text-dark">
                                    <?= $row['NAMA_PRODUK']; ?>
                                </a>
                            </h5>
                            <p class="card-text"><?= $row['DESKRIPSI']; ?></p>
                            <p class="card-text">Kategori: <?= $row['KATEGORI']; ?></p>
                            <p class="card-text">Harga: Rp <?= number_format($row['HARGA'], 0, ',', '.'); ?></p>
                            <form action="keranjang.php" method="POST">
                                <input type="hidden" name="produk_id" value="<?= $row['ID']; ?>">
                                <button type="submit" class="btn btn-success w-100">Tambah ke Keranjang</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted">
                <p>Tidak ada produk ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
