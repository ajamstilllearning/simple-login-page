<?php
// login.php
session_start();

// Jika user sudah login, langsung lempar ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; }
        .box { width: 400px; margin: 80px auto; background: #fff; padding: 25px;
               border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.15); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        .input-group { position: relative; }
        input { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;
                border: 1px solid #ccc; border-radius: 4px; }
        input.with-toggle { padding-right: 40px; }
        .toggle-password { position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
                          cursor: pointer; font-size: 20px; color: #666; user-select: none; }
        .toggle-password:hover { color: #007BFF; }
        button { margin-top: 18px; width: 100%; padding: 10px; background: #007BFF;
                 color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .pesan { padding: 10px; margin-bottom: 10px; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; }
        .sukses { background: #d4edda; color: #155724; }
        .link { text-align: center; margin-top: 15px; font-size: 14px; }
        .link a { color: #007BFF; text-decoration: none; }
        .link a:hover { text-decoration: underline; }
        .captcha-container { margin-top: 15px; }
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
                <div class="g-recaptcha" data-sitekey="6LfvHeYtAAAAAGYCgd71-YZQIpFc5B5sCASkKiib"></div>
            </div>

            <button type="submit" name="submit">Login</button>
        </form>

        <div class="link">
            Belum punya akun? <a href="register.php">Daftar di sini</a><br><br>
            <a href="forgot_password.php">Lupa Password?</a>
        </div>
    </div>

    <script>
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