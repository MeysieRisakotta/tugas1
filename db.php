<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "greenmart";
$port = 3307;

$conn = mysqli_connect($host, $user, $pass, $db, $port);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>