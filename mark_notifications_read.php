<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = $_SESSION['user_id'];
$room_code = $_SESSION['room_code'];

$stmt = mysqli_prepare($conn, "UPDATE notifications SET is_read = 1 WHERE room_code = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "si", $room_code, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
?>
