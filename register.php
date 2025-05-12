<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - File Manager</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .register-wrapper {
      background: #fff;
      padding: 40px 32px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }
    .register-wrapper img {
      display: block;
      margin: 0 auto 24px;
      width: 140px; /* Ukuran besar logo */
    }
    h2 {
      margin-bottom: 24px;
      font-size: 24px;
      color: #333;
    }
    input {
      width: 95%;
      padding: 12px 14px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 16px;
    }
    button:hover {
      background-color: #0056b3;
    }
    p {
      margin-top: 20px;
    }
    a {
      color: #007bff;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-wrapper">
    <img src="logo.png" alt="Logo Perusahaan" style="width: 140px; margin-bottom: 24px;">
    <h2>Register</h2>
    <?php if (isset($_SESSION['message'])): ?>
      <p style="color:red;"><?= $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>
    <form method="POST" action="register_process.php">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="text" name="room_code_optional" placeholder="Masukkan Kode Tim (opsional)">
      <button type="submit">Daftar</button>
    </form>
    <div class="register-link">
      sudah punya akun? <a href="index.php">login</a>
    </div>
  </div>
</body>
</html>
