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

/* Remove connection */
$del = $conn->prepare("
    DELETE FROM connections
    WHERE 
        (sender_id = ? AND receiver_id = ?)
        OR
        (sender_id = ? AND receiver_id = ?)
");
$del->bind_param("iiii", $blocker_id, $blocked_id, $blocked_id, $blocker_id);
$del->execute();

/* Block user */
$blk = $conn->prepare("
    INSERT IGNORE INTO blocked_users (blocker_id, blocked_id)
    VALUES (?, ?)
");
$blk->bind_param("ii", $blocker_id, $blocked_id);
$blk->execute();

header("Location: connected_profiles.php");
exit();
?>