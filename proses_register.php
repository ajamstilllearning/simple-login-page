<?php
// proses_register.php
session_start();
require_once "koneksi.php";

// Cek apakah form disubmit
if (!isset($_POST['submit'])) {
    header("Location: register.php");
    exit;
}

// Ambil & bersihkan input
$username = trim(mysqli_real_escape_string($koneksi, $_POST['username']));
$password = trim($_POST['password']);
$nama     = trim(mysqli_real_escape_string($koneksi, $_POST['nama']));
$email    = trim(mysqli_real_escape_string($koneksi, $_POST['email']));

// Validasi sederhana
if (empty($username) || empty($password) || empty($nama) || empty($email)) {
    $_SESSION['pesan'] = "Semua field wajib diisi!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['pesan'] = "Format email tidak valid!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

// Cek username / email sudah terdaftar atau belum
$cek = mysqli_query($koneksi,
    "SELECT id_user FROM users WHERE username='$username' OR email='$email' LIMIT 1");
if (mysqli_num_rows($cek) > 0) {
    $_SESSION['pesan'] = "Username atau Email sudah terdaftar!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

// Hash password (PENTING: password tidak disimpan sebagai teks biasa)
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Simpan ke database (prepared statement)
$stmt = mysqli_prepare($koneksi,
    "INSERT INTO users (username, password, nama, email) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssss", $username, $password_hash, $nama, $email);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan'] = "Registrasi berhasil! Silakan login.";
    $_SESSION['tipe']  = "sukses";
    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
    header("Location: register.php");
    exit;
} else {
    $_SESSION['pesan'] = "Registrasi gagal: " . mysqli_error($koneksi);
    $_SESSION['tipe']  = "error";
    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
    header("Location: register.php");
    exit;
}
?>