<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['file'])) {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$file_relative_path = $_GET['file']; // Contoh: upload/fileku.pdf

// Cegah traversal path
if (strpos($file_relative_path, '..') !== false) {
    echo "❌ Akses tidak sah.";
    exit;
}

// Buat path absolut
$upload_dir = realpath("upload");
$full_path = realpath($file_relative_path);

// Validasi file berada di dalam direktori upload
if ($full_path && strpos($full_path, $upload_dir) === 0 && file_exists($full_path)) {
    // Cek di database apakah file ini milik user
    $stmt = mysqli_prepare($conn, "SELECT * FROM files WHERE file_path = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $file_relative_path, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Hapus file fisik
        if (unlink($full_path)) {
            // Hapus dari database
            $stmt = mysqli_prepare($conn, "DELETE FROM files WHERE file_path = ? AND user_id = ?");
            mysqli_stmt_bind_param($stmt, "si", $file_relative_path, $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Catat aktivitas hapus
        if ($file_id) {
            $action = "menghapus file";
            $stmt = mysqli_prepare($conn, "INSERT INTO activities (user_id, file_id, action, created_at) VALUES (?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, "iis", $user_id, $file_id, $action);
            mysqli_stmt_execute($stmt);
        }

            header("Location: dashboard.php?msg=deleted");
            exit;
        } else {
            echo "❌ Gagal menghapus file fisik.";
        }
    } else {
        echo "❌ Anda tidak memiliki izin untuk menghapus file ini.";
    }
} else {
    echo "❌ File tidak ditemukan atau bukan di folder upload.";
}
?>
