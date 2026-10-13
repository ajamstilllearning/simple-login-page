<?php
// lupa_password.php
session_start();

// Hapus form data session saat halaman dimuat untuk keamanan
if (isset($_SESSION['forgot_data'])) {
    unset($_SESSION['forgot_data']);
}
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
if (isset($_SESSION['form_error'])) {
    unset($_SESSION['form_error']);
}

// Hapus login data saat masuk ke forgot password
if (isset($_SESSION['login_password'])) {
    unset($_SESSION['login_password']);
}
if (isset($_SESSION['login_username'])) {
    unset($_SESSION['login_username']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            position: relative;
            padding: 20px 0;
        }

        /* Animated gradient background */
        body::before {
            content: '';
            position: absolute;
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

        .box {
            width: 450px;
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
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

        /* Custom scrollbar */
        .box::-webkit-scrollbar {
            width: 8px;
        }

        .box::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .box::-webkit-scrollbar-thumb {
            background: rgba(138, 43, 226, 0.5);
            border-radius: 10px;
        }

        .box::-webkit-scrollbar-thumb:hover {
            background: rgba(138, 43, 226, 0.7);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 10px;
            font-size: 28px;
            text-shadow: 0 2px 10px rgba(138, 43, 226, 0.5);
        }

        p.desc {
            text-align: center;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #e0e0e0;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            margin-top: 8px;
            box-sizing: border-box;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #8a2be2;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 20px rgba(138, 43, 226, 0.3);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #8a2be2 0%, #4b0082 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(138, 43, 226, 0.4);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(138, 43, 226, 0.6);
        }

        button:active {
            transform: translateY(0);
        }

        .pesan {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-size: 14px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .error {
            background: rgba(220, 53, 69, 0.2);
            color: #ff6b6b;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .sukses {
            background: rgba(40, 167, 69, 0.2);
            color: #51cf66;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        .link a {
            color: #8a2be2;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .link a:hover {
            color: #a855f7;
            text-shadow: 0 0 10px rgba(138, 43, 226, 0.5);
        }

        .password-hint {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .box {
                width: 90%;
                padding: 30px 20px;
                margin: 20px auto;
            }

            h2 {
                font-size: 24px;
            }

            p.desc {
                font-size: 12px;
            }

            label {
                font-size: 13px;
            }

            input {
                font-size: 14px;
                padding: 10px 12px;
            }

            button {
                font-size: 15px;
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .box {
                width: 95%;
                padding: 25px 15px;
                margin: 15px auto;
            }

            h2 {
                font-size: 22px;
                margin-bottom: 15px;
            }

            p.desc {
                font-size: 11px;
                margin-bottom: 20px;
            }

            label {
                margin-top: 12px;
                font-size: 12px;
            }

            input {
                padding: 10px;
                font-size: 13px;
            }

            button {
                margin-top: 20px;
                padding: 12px;
                font-size: 14px;
            }

            .mouse-follower {
                width: 200px;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Reset Password</h2>
        <p class="desc">Masukkan Username dan Email Anda yang terdaftar untuk mengubah password.</p>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="pesan <?= $_SESSION['tipe'] ?>">
                <?= $_SESSION['pesan']; ?>
            </div>
            <?php
                unset($_SESSION['pesan']);
                unset($_SESSION['tipe']);
            ?>
        <?php endif; ?>

        <form action="proses_forgot_passwd.php" method="POST">
            <label>Username</label>
            <input type="text" name="username" required
                   value="<?= isset($_SESSION['forgot_data']['username']) ? htmlspecialchars($_SESSION['forgot_data']['username']) : '' ?>">

            <label>Email Terdaftar</label>
            <input type="email" name="email" required
                   value="<?= isset($_SESSION['forgot_data']['email']) ? htmlspecialchars($_SESSION['forgot_data']['email']) : '' ?>">

            <label>Password Baru</label>
            <input type="password" name="password_baru" required minlength="6">
            <div class="password-hint">Minimal 6 karakter, mengandung huruf, angka, karakter khusus, dan 1 huruf kapital</div>

            <button type="submit" name="submit">Reset Password</button>
        </form>

        <div class="link">
            Ingat password? <a href="login.php">Kembali ke Login</a>
        </div>
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