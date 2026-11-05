<?php
// proses_register.php
session_start();
require_once "koneksi.php";

if (!isset($_POST['submit'])) {
    header("Location: register.php");
    exit;
}

$username = trim(mysqli_real_escape_string($koneksi, $_POST['username']));
$password = trim($_POST['password']);
$password_confirm = trim($_POST['password_confirm']);
$nama     = trim(mysqli_real_escape_string($koneksi, $_POST['nama']));
$email    = trim(mysqli_real_escape_string($koneksi, $_POST['email']));

$_SESSION['form_data'] = [
    'username' => $username,
    'password' => $password,
    'password_confirm' => $password_confirm,
    'nama' => $nama,
    'email' => $email
];

$_SESSION['form_error'] = true;

$recaptcha_secret = "6LebdewtAAAAAKaRXnONKh02yJ2rq6ftpfmQt22o";
$recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

if (empty($recaptcha_response)) {
    $_SESSION['pesan'] = "Harap lengkapi reCAPTCHA!";
    $_SESSION['tipe']  = "error";
    $_SESSION['form_error'] = true;
    header("Location: register.php");
    exit;
}

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
    $_SESSION['form_error'] = true;
    header("Location: register.php");
    exit;
}

if (empty($username) || empty($password) || empty($password_confirm) || empty($nama) || empty($email)) {
    $_SESSION['pesan'] = "Semua field wajib diisi!";
    $_SESSION['tipe']  = "error";
    header("Location: register.php");
    exit;
}

if (strlen($username) < 3) {
    $_SESSION['pesan'] = "Username minimal 3 karakter!";
    $_SESSION['tipe']  = "error";
    if (isset($_SESSION['form_data'])) unset($_SESSION['form_data']['username']);
    $_SESSION['form_error'] = true;
    header("Location: register.php");
    exit;
}

$password_regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/";
if (!preg_match($password_regex, $password)) {
    $_SESSION['pesan'] = "Password harus minimal 6 karakter, mengandung huruf, angka, karakter khusus (@$!%*?&), dan 1 huruf kapital!";
    $_SESSION['tipe']  = "error";
    if (isset($_SESSION['form_data'])) {
        unset($_SESSION['form_data']['password']);
        unset($_SESSION['form_data']['password_confirm']);
    }
    $_SESSION['form_error'] = true;
    header("Location: register.php");
    exit;
}

if ($password !== $password_confirm) {
    $_SESSION['pesan'] = "Password konfirmasi tidak cocok!";
    $_SESSION['tipe']  = "error";
    if (isset($_SESSION['form_data'])) {
        unset($_SESSION['form_data']['password']);
        unset($_SESSION['form_data']['password_confirm']);
    }
    $_SESSION['form_error'] = true;
    header("Location: register.php");
    exit;
}

if (strlen($nama) < 1) {
    $_SESSION['pesan'] = "Nama minimal 1 huruf!";
    $_SESSION['tipe']  = "error";
    if (isset($_SESSION['form_data'])) unset($_SESSION['form_data']['nama']);
    $_SESSION['form_error'] = true;
    header("Location: register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['pesan'] = "Format email tidak valid!";
    $_SESSION['tipe']  = "error";
    if (isset($_SESSION['form_data'])) unset($_SESSION['form_data']['email']);
    $_SESSION['form_error'] = true;
    header("Location: register.php");
    exit;
}

// DIUBAH: tabel 'users' menjadi 'user'
$cek_username = mysqli_prepare($koneksi, "SELECT id_user FROM user WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($cek_username, "s", $username);
mysqli_stmt_execute($cek_username);
$result_username = mysqli_stmt_get_result($cek_username);

if (mysqli_num_rows($result_username) > 0) {
    $_SESSION['pesan'] = "Username sudah terdaftar!";
    $_SESSION['tipe']  = "error";
    if (isset($_SESSION['form_data'])) unset($_SESSION['form_data']['username']);
    $_SESSION['form_error'] = true;
    mysqli_stmt_close($cek_username);
    header("Location: register.php");
    exit;
}
mysqli_stmt_close($cek_username);

// DIUBAH: tabel 'users' menjadi 'user'
$cek_email = mysqli_prepare($koneksi, "SELECT id_user FROM user WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($cek_email, "s", $email);
mysqli_stmt_execute($cek_email);
$result_email = mysqli_stmt_get_result($cek_email);

if (mysqli_num_rows($result_email) > 0) {
    $_SESSION['pesan'] = "Email sudah terdaftar!";
    $_SESSION['tipe']  = "error";
    if (isset($_SESSION['form_data'])) unset($_SESSION['form_data']['email']);
    $_SESSION['form_error'] = true;
    mysqli_stmt_close($cek_email);
    header("Location: register.php");
    exit;
}
mysqli_stmt_close($cek_email);

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// DIUBAH: tabel 'users' menjadi 'user'
$stmt = mysqli_prepare($koneksi,
    "INSERT INTO user (username, password, nama, email) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssss", $username, $password_hash, $nama, $email);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan'] = "Registrasi berhasil! Silakan login.";
    $_SESSION['tipe']  = "sukses";
    unset($_SESSION['form_data']);
    unset($_SESSION['form_error']);
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