<?php
// proses_lupa_password.php
session_start();
require_once "koneksi.php";

if (!isset($_POST['submit'])) {
    header("Location: forgot_password.php");
    exit;
}

$username      = trim(mysqli_real_escape_string($koneksi, $_POST['username']));
$email         = trim(mysqli_real_escape_string($koneksi, $_POST['email']));
$password_baru = trim($_POST['password_baru']);

if (empty($username) || empty($email) || empty($password_baru)) {
    $_SESSION['pesan'] = "Semua field wajib diisi!";
    $_SESSION['tipe']  = "error";
    header("Location: forgot_password.php");
    exit;
}

// Cek apakah Username dan Email cocok di database
$stmt = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE username = ? AND email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $username, $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
    // Jika cocok, hash password baru
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

    // Update password di database
    $stmt_update = mysqli_prepare($koneksi, "UPDATE users SET password = ? WHERE username = ? AND email = ?");
    mysqli_stmt_bind_param($stmt_update, "sss", $password_hash, $username, $email);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['pesan'] = "Password berhasil direset! Silakan login dengan password baru Anda.";
        $_SESSION['tipe']  = "sukses";
        mysqli_stmt_close($stmt_update);
        mysqli_stmt_close($stmt);
        mysqli_close($koneksi);
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['pesan'] = "Gagal mereset password: " . mysqli_error($koneksi);
        $_SESSION['tipe']  = "error";
    }
    mysqli_stmt_close($stmt_update);
} else {
    $_SESSION['pesan'] = "Username dan Email tidak cocok / tidak ditemukan!";
    $_SESSION['tipe']  = "error";
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
header("Location: forgot_password.php");
exit;
?>