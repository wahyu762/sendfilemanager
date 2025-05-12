<?php
session_start();
require 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['upload'])) {
    $user_id = $_SESSION['user_id'];
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    
    // Tentukan lokasi folder penyimpanan file
    $upload_dir = 'upload/';
    $file_path = $upload_dir . basename($file_name);

    // Cek apakah file berhasil diupload
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Masukkan data file ke database
        $query = "INSERT INTO files (user_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isss", $user_id, $file_name, $file_path, $file_type);

        if (mysqli_stmt_execute($stmt)) {
            // Dapatkan ID file yang baru di-upload
            $file_id = mysqli_insert_id($conn);

            // Log aktivitas
            $action = "Mengupload file: $file_name";
            $activity_query = "INSERT INTO activities (user_id, action, file_id) VALUES (?, ?, ?)";
            $activity_stmt = mysqli_prepare($conn, $activity_query);
            mysqli_stmt_bind_param($activity_stmt, "isi", $user_id, $action, $file_id);

            if (mysqli_stmt_execute($activity_stmt)) {
                $_SESSION['message'] = "File berhasil diupload!";
            } else {
                $_SESSION['message'] = "Gagal mencatat aktivitas!";
            }
        } else {
            $_SESSION['message'] = "Gagal menyimpan informasi file ke database!";
        }
    } else {
        $_SESSION['message'] = "Gagal upload file!";
    }

    header("Location: dashboard.php");
    exit;
}
?>
