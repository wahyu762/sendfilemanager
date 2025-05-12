<?php
$host = "localhost";
$user = "root";
$pass = ""; // sesuaikan dengan MySQL lo
$db   = "filemanager_db";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
