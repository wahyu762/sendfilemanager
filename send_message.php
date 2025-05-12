<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['message'])) {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$room_code = $_SESSION['room_code'];
$message = trim($_POST['message']);

if ($message !== '') {
    $stmt = mysqli_prepare($conn, "INSERT INTO chat_messages (room_code, user_id, message) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sis", $room_code, $user_id, $message);
    mysqli_stmt_execute($stmt);
}

header("Location: dashboard.php#chat");
exit;
