<?php
session_start();
require 'db.php';

$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$room_code_input = strtoupper(trim($_POST['room_code_optional'])); // Optional kode room dari form

// Jika user isi kode tim → cek keberadaan kode tersebut
if (!empty($room_code_input)) {
    $check = mysqli_prepare($conn, "SELECT 1 FROM users WHERE room_code = ?");
    mysqli_stmt_bind_param($check, "s", $room_code_input);
    mysqli_stmt_execute($check);
    $result = mysqli_stmt_get_result($check);

    if (mysqli_num_rows($result) > 0) {
        $room_code = $room_code_input; // gunakan kode room yang dimasukkan
        $show_room = false; // tidak perlu tampilkan kode
    } else {
        echo "<p style='color:red;'>Kode tim tidak ditemukan. Silakan coba lagi.</p>";
        echo "<a href='register.php'>Kembali</a>";
        exit;
    }
} else {
    // Generate room code baru yang unik
    do {
        $room_code = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
        $check = mysqli_prepare($conn, "SELECT 1 FROM users WHERE room_code = ?");
        mysqli_stmt_bind_param($check, "s", $room_code);
        mysqli_stmt_execute($check);
        $result = mysqli_stmt_get_result($check);
    } while (mysqli_num_rows($result) > 0);

    $show_room = true; // tampilkan kode karena dia buat tim baru
}

// Simpan user ke database
$stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, room_code) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password, $room_code);

if (mysqli_stmt_execute($stmt)) {
    echo "<h2>Registrasi Berhasil!</h2>";

    if ($show_room) {
        echo "<p>Kode Room Tim Anda: <strong style='font-size: 20px;'>$room_code</strong></p>";
        echo "<p>Bagikan kode ini agar anggota lain bisa bergabung ke tim Anda.</p>";
    } else {
        echo "<p>Anda berhasil bergabung ke tim dengan kode: <strong>$room_code</strong></p>";
    }

    echo "<a href='index.php'>Login Sekarang</a>";
} else {
    echo "<p>Terjadi kesalahan saat registrasi: " . mysqli_error($conn) . "</p>";
}
?>

