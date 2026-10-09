<?php
// proses_login.php
session_start();
require_once "koneksi.php";

if (!isset($_POST['submit'])) {
    header("Location: login.php");
    exit;
}

$username = trim(mysqli_real_escape_string($koneksi, $_POST['username']));
$password = trim($_POST['password']);

if (empty($username) || empty($password)) {
    $_SESSION['pesan'] = "Username dan Password wajib diisi!";
    $_SESSION['tipe']  = "error";
    header("Location: login.php");
    exit;
}

// Ambil data user berdasarkan username (menggunakan prepared statement)
$stmt = mysqli_prepare($koneksi, "SELECT id_user, username, password, nama FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password
    // CATATAN: Jika user 'budi123' mencoba login, ini akan GAGAL karena passwordnya masih plain text.
    if (password_verify($password, $user['password'])) {
        // Password benar, buat session
        $_SESSION['id_user']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama']     = $user['nama'];

        mysqli_stmt_close($stmt);
        mysqli_close($koneksi);
        
        header("Location: home.php");
        exit;
    } else {
        $_SESSION['pesan'] = "Username atau password yang Anda masukkan salah!";
        $_SESSION['tipe']  = "error";
    }
} else {
    $_SESSION['pesan'] = "Username atau password yang Anda masukkan salah!";
    $_SESSION['tipe']  = "error";
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
header("Location: login.php");
exit;
?>