<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Sistem Perusahaan</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #f4f6f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-wrapper {
      background: white;
      padding: 48px;
      border-radius: 16px;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
      width: 100%;
      max-width: 420px;
    }

    .login-wrapper img {
      display: block;
      margin: 0 auto 24px auto;
      width: 80px;
    }

    .login-wrapper h2 {
      text-align: center;
      margin-bottom: 24px;
      color: #1a202c;
      font-weight: 600;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group input {
      width: 100%;
      padding: 14px 16px;
      border: 1px solid #d1d5db;
      border-radius: 10px;
      font-size: 14px;
      transition: border-color 0.3s;
    }

    .form-group input:focus {
      outline: none;
      border-color: #4f46e5;
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    button {
      width: 100%;
      padding: 14px;
      background: #4f46e5;
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #4338ca;
    }

    .message {
      background: #fee2e2;
      color: #b91c1c;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
      text-align: center;
    }

    .register-link {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
    }

    .register-link a {
      color: #4f46e5;
      text-decoration: none;
      font-weight: 500;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .login-wrapper {
        padding: 32px 20px;
      }
    }
  </style>
</head>
<body>

  <div class="login-wrapper">
  <img src="logo.png" alt="Logo Perusahaan" style="width: 140px; margin-bottom: 24px;">
    <h2>Masuk ke Sistem</h2>

    <?php if (isset($_SESSION['message'])): ?>
      <div class="message"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <form method="POST" action="login_process.php">
      <div class="form-group">
        <input type="email" name="email" placeholder="Email" required>
      </div>
      <div class="form-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button type="submit">Login</button>
    </form>

    <div class="register-link">
      Belum punya akun? <a href="register.php">Daftar Sekarang</a>
    </div>
  </div>

</body>
</html>
