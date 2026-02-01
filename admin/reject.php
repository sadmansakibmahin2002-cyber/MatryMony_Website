<?php
include '../includes/auth.php';
include '../includes/db_connect.php';

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$user_id = (int) $_GET['id'];

$sql = "UPDATE users SET status='rejected' WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: reject_profile.php");
exit;
?>