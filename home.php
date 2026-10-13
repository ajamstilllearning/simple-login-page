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

// Hapus form data session untuk keamanan
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
if (isset($_SESSION['forgot_data'])) {
    unset($_SESSION['forgot_data']);
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated gradient background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%),
                        rgba(138, 43, 226, 0.3) 0%,
                        rgba(75, 0, 130, 0.2) 30%,
                        transparent 70%);
            pointer-events: none;
            transition: background 0.3s ease;
            z-index: 0;
        }

        /* Mouse follower */
        .mouse-follower {
            position: fixed;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(138, 43, 226, 0.4) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            transform: translate(-50%, -50%);
            transition: transform 0.1s ease-out;
            z-index: 0;
            filter: blur(40px);
        }

        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            color: #fff;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        .navbar span {
            font-size: 24px;
            font-weight: 600;
            text-shadow: 0 2px 10px rgba(138, 43, 226, 0.5);
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            background: linear-gradient(135deg, #8a2be2 0%, #4b0082 100%);
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(138, 43, 226, 0.4);
        }

        .navbar a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(138, 43, 226, 0.6);
        }

        .container {
            max-width: 800px;
            width: 90%;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 28px;
            text-shadow: 0 2px 10px rgba(138, 43, 226, 0.5);
        }

        p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 30px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.9);
        }

        td:first-child {
            font-weight: 600;
            width: 200px;
            color: #8a2be2;
        }

        tr:hover td {
            background: rgba(138, 43, 226, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .navbar span {
                font-size: 20px;
            }

            .navbar a {
                padding: 8px 15px;
                font-size: 14px;
            }

            .container {
                width: 90%;
                padding: 30px 20px;
                margin: 30px auto;
            }

            h2 {
                font-size: 24px;
            }

            p {
                font-size: 15px;
            }

            td {
                padding: 12px;
                font-size: 14px;
            }

            td:first-child {
                width: 150px;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 12px 15px;
                flex-direction: column;
                gap: 15px;
            }

            .navbar span {
                font-size: 18px;
            }

            .navbar a {
                width: 100%;
                text-align: center;
                padding: 10px;
            }

            .container {
                width: 95%;
                padding: 25px 15px;
                margin: 20px auto;
            }

            h2 {
                font-size: 22px;
            }

            p {
                font-size: 14px;
            }

            table {
                font-size: 13px;
            }

            td {
                padding: 10px;
                font-size: 13px;
            }

            td:first-child {
                width: 120px;
                font-size: 12px;
            }

            .mouse-follower {
                width: 200px;
                height: 200px;
            }
        }
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

    <div class="mouse-follower" id="mouseFollower"></div>

    <script>
        // Mouse follower and interactive gradient
        const mouseFollower = document.getElementById('mouseFollower');
        const body = document.body;

        document.addEventListener('mousemove', (e) => {
            const x = e.clientX;
            const y = e.clientY;

            // Move mouse follower
            mouseFollower.style.left = x + 'px';
            mouseFollower.style.top = y + 'px';

            // Update gradient position
            const xPercent = (x / window.innerWidth) * 100;
            const yPercent = (y / window.innerHeight) * 100;
            body.style.setProperty('--mouse-x', xPercent + '%');
            body.style.setProperty('--mouse-y', yPercent + '%');
        });

        // Hide mouse follower when mouse leaves window
        document.addEventListener('mouseleave', () => {
            mouseFollower.style.opacity = '0';
        });

        document.addEventListener('mouseenter', () => {
            mouseFollower.style.opacity = '1';
        });
    </script>
</body>
</html>