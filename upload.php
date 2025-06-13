<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

include 'db.php'; // koneksi ke database

$uploadSuccess = '';
$uploadError = '';

// Proses Upload File (misalnya gambar produk)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["file"]["name"]);

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $uploadSuccess = "File berhasil diupload.";
    } else {
        $uploadError = "Terjadi kesalahan saat mengupload file.";
    }
}

// Ambil data produk dari database
$produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload & Daftar Produk</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        input, button { margin: 10px 0; padding: 10px; width: 100%; }
        .success { color: green; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        img { max-width: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Produk</h2>

        <?php if ($uploadSuccess): ?>
            <p class="success"><?= $uploadSuccess ?></p>
        <?php endif; ?>

        <?php if ($uploadError): ?>
            <p class="error"><?= $uploadError ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required><br>
            <button type="submit">Upload</button>
        </form>

        <h3>Daftar Produk</h3>
        <table>
            <tr>
                <th>Nama</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Kategori</th>
                <th>Gambar</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($produk)) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['NAMA_PRODUK']) ?></td>
                    <td>Rp<?= number_format($row['HARGA'], 0, ',', '.') ?></td>
                    <td><?= $row['STOK'] ?></td>
                    <td><?= $row['KATEGORI'] ?></td>
                    <td>
                        <?php if ($row['GAMBAR']) : ?>
                            <img src="uploads/<?= $row['GAMBAR'] ?>" alt="GAMBAR">
                        <?php else : ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <br><a href="logout.php">Logout</a>
    </div>
</body>
</html>