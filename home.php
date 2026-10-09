<?php
// home.php
session_start();

// Proteksi halaman: hanya bisa diakses jika sudah login
// (Anda perlu membuat login.php yang menset $_SESSION['id_user'] agar file ini bisa diakses)
if (!isset($_SESSION['id_user'])) {
    // Jika belum login, tendang ke halaman login (buat file login.php nanti)
    header("Location: login.php");
    exit;
}

require_once "koneksi.php";

$id_user = $_SESSION['id_user'];
$query   = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id_user'");
$user    = mysqli_fetch_assoc($query);

// Hapus session untuk memaksa login ulang pada akses berikutnya
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; margin: 0; }
        .navbar { background: #007BFF; color: #fff; padding: 15px 25px;
                  display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: #fff; text-decoration: none; background: #dc3545;
                    padding: 8px 15px; border-radius: 4px; }
        .container { max-width: 800px; margin: 40px auto; background: #fff;
                     padding: 30px; border-radius: 8px;
                     box-shadow: 0 2px 8px rgba(0,0,0,.15); }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        td:first-child { font-weight: bold; width: 180px; color: #555; }
    </style>
</head>
<body>
    <div class="navbar">
        <span>Dashboard Aplikasi</span>
        <!-- Pastikan Anda membuat file logout.php -->
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Selamat datang, <?= htmlspecialchars($user['nama']); ?> 👋</h2>
        <p>Anda berhasil login ke sistem.</p>

        <table>
            <tr><td>ID User</td><td><?= htmlspecialchars($user['id_user']); ?></td></tr>
            <tr><td>Username</td><td><?= htmlspecialchars($user['username']); ?></td></tr>
            <tr><td>Nama</td><td><?= htmlspecialchars($user['nama']); ?></td></tr>
            <tr><td>Email</td><td><?= htmlspecialchars($user['email']); ?></td></tr>
        </table>
    </div>
</body>
</html>