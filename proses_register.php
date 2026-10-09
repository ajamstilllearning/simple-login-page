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
$password_confirm = trim($_POST['password_confirm']);
$nama     = trim(mysqli_real_escape_string($koneksi, $_POST['nama']));
$email    = trim(mysqli_real_escape_string($koneksi, $_POST['email']));

// Validasi ReCAPTCHA
$recaptcha_secret = "6LfvHeYtAAAAAFZE_iylDQOjzES7xKg5GYdCe-w0";
$recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

if (empty($recaptcha_response)) {
    $_SESSION['pesan'] = "Harap lengkapi reCAPTCHA!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

// Verifikasi ReCAPTCHA dengan Google
$recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
$recaptcha_data = [
    'secret' => $recaptcha_secret,
    'response' => $recaptcha_response,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

$recaptcha_options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($recaptcha_data)
    ]
];

$recaptcha_context = stream_context_create($recaptcha_options);
$recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
$recaptcha_json = json_decode($recaptcha_result);

if (!$recaptcha_json->success) {
    $_SESSION['pesan'] = "Verifikasi reCAPTCHA gagal!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

// Validasi sederhana
if (empty($username) || empty($password) || empty($password_confirm) || empty($nama) || empty($email)) {
    $_SESSION['pesan'] = "Semua field wajib diisi!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

// Validasi username minimal 3 karakter
if (strlen($username) < 3) {
    $_SESSION['pesan'] = "Username minimal 3 karakter!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

// Validasi password minimal 6 karakter, mengandung huruf, angka, karakter khusus, dan 1 huruf kapital
$password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/";
if (!preg_match($password_regex, $password)) {
    $_SESSION['pesan'] = "Password harus minimal 6 karakter, mengandung huruf, angka, karakter khusus (@$!%*?&), dan 1 huruf kapital!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

// Validasi password konfirmasi
if ($password !== $password_confirm) {
    $_SESSION['pesan'] = "Password konfirmasi tidak cocok!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

// Validasi nama minimal 1 huruf
if (strlen($nama) < 1) {
    $_SESSION['pesan'] = "Nama minimal 1 huruf!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

// Validasi email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['pesan'] = "Format email tidak valid!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

// Cek username sudah terdaftar atau belum
$cek_username = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($cek_username, "s", $username);
mysqli_stmt_execute($cek_username);
$result_username = mysqli_stmt_get_result($cek_username);

if (mysqli_num_rows($result_username) > 0) {
    $_SESSION['pesan'] = "Username sudah terdaftar!";
    $_SESSION['tipe']  = "error";
    mysqli_stmt_close($cek_username);
    header("Location: register.php");
    exit;
}
mysqli_stmt_close($cek_username);

// Cek email sudah terdaftar atau belum
$cek_email = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($cek_email, "s", $email);
mysqli_stmt_execute($cek_email);
$result_email = mysqli_stmt_get_result($cek_email);

if (mysqli_num_rows($result_email) > 0) {
    $_SESSION['pesan'] = "Email sudah terdaftar!";
    $_SESSION['tipe']  = "error";
    mysqli_stmt_close($cek_email);
    header("Location: register.php");
    exit;
}
mysqli_stmt_close($cek_email);

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