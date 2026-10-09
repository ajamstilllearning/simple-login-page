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

// Validasi ReCAPTCHA
$recaptcha_secret = "6LfvHeYtAAAAAFZE_iylDQOjzES7xKg5GYdCe-w0";
$recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

if (empty($recaptcha_response)) {
    $_SESSION['pesan'] = "Harap lengkapi reCAPTCHA!";
    $_SESSION['tipe']  = "error";
    header("Location: login.php");
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
    header("Location: login.php");
    exit;
}

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