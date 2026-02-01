<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$blocker_id = (int)$_SESSION['user_id'];
$blocked_id = (int)($_POST['user_id'] ?? 0);

if ($blocked_id <= 0) {
    die("Invalid request");
}

/* Remove block only */
$stmt = $conn->prepare("
    DELETE FROM blocked_users
    WHERE blocker_id = ? AND blocked_id = ?
");
$stmt->bind_param("ii", $blocker_id, $blocked_id);
$stmt->execute();

header("Location: blocked_user.php");
exit();
?>