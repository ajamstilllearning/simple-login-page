<?php
// register.php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Registrasi</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; }
        .box { width: 450px; margin: 60px auto; background: #fff; padding: 25px;
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
        .error-msg { color: #dc3545; font-size: 12px; margin-top: 3px; display: none; }
        .input-error { border-color: #dc3545 !important; }
        .password-hint { font-size: 11px; color: #666; margin-top: 5px; }
        .captcha-container { margin-top: 15px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Registrasi User</h2>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="pesan <?= $_SESSION['tipe'] ?>">
                <?= $_SESSION['pesan']; ?>
            </div>
            <?php
                unset($_SESSION['pesan']);
                unset($_SESSION['tipe']);
            ?>
        <?php endif; ?>

        <form action="proses_register.php" method="POST" id="registerForm" onsubmit="return validateForm()">
            <label>Username</label>
            <input type="text" name="username" id="username" required minlength="3">
            <div class="error-msg" id="username-error">Username minimal 3 karakter</div>

            <label>Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password" required class="with-toggle">
                <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
            </div>
            <div class="password-hint">Minimal 6 karakter, mengandung huruf, angka, karakter khusus, dan 1 huruf kapital</div>
            <div class="error-msg" id="password-error">Password tidak memenuhi syarat</div>

            <label>Konfirmasi Password</label>
            <div class="input-group">
                <input type="password" name="password_confirm" id="password_confirm" required class="with-toggle">
                <span class="toggle-password" onclick="togglePassword('password_confirm', this)">👁️</span>
            </div>
            <div class="error-msg" id="password-confirm-error">Password tidak cocok</div>

            <label>Nama Lengkap</label>
            <input type="text" name="nama" id="nama" required>
            <div class="error-msg" id="nama-error">Nama minimal 1 huruf</div>

            <label>Email</label>
            <input type="email" name="email" id="email" required>
            <div class="error-msg" id="email-error">Format email tidak valid</div>

            <div class="captcha-container">
                <div class="g-recaptcha" data-sitekey="6LebdewtAAAAANPqSfHWK7h1ZxAhh6-SzuZg_PuB"></div>
            </div>

            <button type="submit" name="submit">Daftar</button>
        </form>

        <div class="link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
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
            let isValid = true;

            // Username validation
            const username = document.getElementById('username');
            const usernameError = document.getElementById('username-error');
            if (username.value.length < 3) {
                username.classList.add('input-error');
                usernameError.style.display = 'block';
                isValid = false;
            } else {
                username.classList.remove('input-error');
                usernameError.style.display = 'none';
            }

            // Password validation
            const password = document.getElementById('password');
            const passwordError = document.getElementById('password-error');
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
            if (!passwordRegex.test(password.value)) {
                password.classList.add('input-error');
                passwordError.style.display = 'block';
                isValid = false;
            } else {
                password.classList.remove('input-error');
                passwordError.style.display = 'none';
            }

            // Password confirmation
            const passwordConfirm = document.getElementById('password_confirm');
            const passwordConfirmError = document.getElementById('password-confirm-error');
            if (password.value !== passwordConfirm.value) {
                passwordConfirm.classList.add('input-error');
                passwordConfirmError.style.display = 'block';
                isValid = false;
            } else {
                passwordConfirm.classList.remove('input-error');
                passwordConfirmError.style.display = 'none';
            }

            // Name validation
            const nama = document.getElementById('nama');
            const namaError = document.getElementById('nama-error');
            if (nama.value.length < 1) {
                nama.classList.add('input-error');
                namaError.style.display = 'block';
                isValid = false;
            } else {
                nama.classList.remove('input-error');
                namaError.style.display = 'none';
            }

            // Email validation
            const email = document.getElementById('email');
            const emailError = document.getElementById('email-error');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                email.classList.add('input-error');
                emailError.style.display = 'block';
                isValid = false;
            } else {
                email.classList.remove('input-error');
                emailError.style.display = 'none';
            }

            // ReCAPTCHA validation
            const captchaResponse = grecaptcha.getResponse();
            if (captchaResponse.length === 0) {
                alert('Please complete the reCAPTCHA');
                isValid = false;
            }

            return isValid;
        }

        // Real-time validation
        document.getElementById('username').addEventListener('input', function() {
            if (this.value.length >= 3) {
                this.classList.remove('input-error');
                document.getElementById('username-error').style.display = 'none';
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
            if (passwordRegex.test(this.value)) {
                this.classList.remove('input-error');
                document.getElementById('password-error').style.display = 'none';
            }
        });

        document.getElementById('password_confirm').addEventListener('input', function() {
            if (this.value === document.getElementById('password').value) {
                this.classList.remove('input-error');
                document.getElementById('password-confirm-error').style.display = 'none';
            }
        });

        document.getElementById('nama').addEventListener('input', function() {
            if (this.value.length >= 1) {
                this.classList.remove('input-error');
                document.getElementById('nama-error').style.display = 'none';
            }
        });

        document.getElementById('email').addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailRegex.test(this.value)) {
                this.classList.remove('input-error');
                document.getElementById('email-error').style.display = 'none';
            }
        });
    </script>
</body>
</html>