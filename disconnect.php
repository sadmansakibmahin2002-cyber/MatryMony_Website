<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$other_id = (int)($_POST['user_id'] ?? 0);

if ($other_id <= 0) {
    die("Invalid request");
}

$stmt = $conn->prepare("
    DELETE FROM connections
    WHERE 
        (sender_id = ? AND receiver_id = ?)
        OR
        (sender_id = ? AND receiver_id = ?)
");
$stmt->bind_param("iiii", $user_id, $other_id, $other_id, $user_id);
$stmt->execute();

header("Location: connected_profiles.php");
exit();
?>