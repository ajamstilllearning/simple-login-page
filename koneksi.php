<?php
// koneksi.php
$host     = "localhost";
$user     = "root";       // Default XAMPP/Laragon
$password = "";           // Default XAMPP/Laragon biasanya kosong
$database = "test";       // Sesuai dengan nama database di screenshot Anda

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($koneksi, "utf8mb4");
?>