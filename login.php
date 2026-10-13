<?php
// login.php
session_start();

// Jika user sudah login, langsung lempar ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: home.php");
    exit;
}

// Hapus form data session saat halaman dimuat untuk keamanan
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
if (isset($_SESSION['forgot_data'])) {
    unset($_SESSION['forgot_data']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
            width: 420px;
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
            margin-bottom: 30px;
            font-size: 28px;
            text-shadow: 0 2px 10px rgba(138, 43, 226, 0.5);
        }

        label {
            display: block;
            margin-top: 20px;
            font-weight: 600;
            color: #e0e0e0;
            font-size: 14px;
        }

        .input-group { position: relative; }

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

        input.with-toggle { padding-right: 50px; }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 22px;
            color: rgba(255, 255, 255, 0.7);
            user-select: none;
            transition: all 0.3s ease;
        }

        .toggle-password:hover {
            color: #8a2be2;
            transform: translateY(-50%) scale(1.2);
        }

        button {
            margin-top: 30px;
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

        .captcha-container {
            margin-top: 20px;
        }

        .captcha-container iframe {
            border-radius: 10px;
            overflow: hidden;
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

            .toggle-password {
                font-size: 20px;
                right: 12px;
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
        <h2>Login User</h2>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="pesan <?= $_SESSION['tipe'] ?>">
                <?= $_SESSION['pesan']; ?>
            </div>
            <?php
                unset($_SESSION['pesan']);
                unset($_SESSION['tipe']);
            ?>
        <?php endif; ?>

        <form action="proses_login.php" method="POST" id="loginForm" onsubmit="return validateForm()">
            <label>Username</label>
            <input type="text" name="username" required autofocus>

            <label>Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password" required class="with-toggle">
                <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
            </div>

            <div class="captcha-container">
                <div class="g-recaptcha" data-sitekey="6LebdewtAAAAANPqSfHWK7h1ZxAhh6-SzuZg_PuB"></div>
            </div>

            <button type="submit" name="submit">Login</button>
        </form>

        <div class="link">
            Belum punya akun? <a href="register.php">Daftar di sini</a><br><br>
            <a href="forgot_password.php">Lupa Password?</a>
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

        function togglePassword(inputId, toggleIcon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                input.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }

        function validateForm() {
            const username = document.querySelector('input[name="username"]').value;
            const password = document.querySelector('input[name="password"]').value;

            if (username.trim() === '' || password.trim() === '') {
                alert('Username dan Password wajib diisi!');
                return false;
            }

            // ReCAPTCHA validation
            const captchaResponse = grecaptcha.getResponse();
            if (captchaResponse.length === 0) {
                alert('Harap lengkapi reCAPTCHA!');
                return false;
            }

            return true;
        }

        // Show alert if there's an error message from server
        window.onload = function() {
            const errorDiv = document.querySelector('.pesan.error');
            if (errorDiv) {
                alert(errorDiv.textContent.trim());
            }
        };
    </script>
</body>
</html>