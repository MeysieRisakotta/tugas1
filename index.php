<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM user WHERE NAMA = '$nama'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['PASSWORD']) {
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['nama'] = $user['NAMA'];
            $_SESSION['role'] = $user['ROLE'];
            header("Location: beranda.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "User tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - GreenMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #d4fc79, #96e6a1);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
        }
        .btn-login {
            background-color: #2e8b57;
            color: white;
        }
        .btn-login:hover {
            background-color: #256d46;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4">Login ke GreenMart</h3>
    <?php if ($error) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-login w-100">Masuk</button>
    </form>
</div>

</body>
</html>