<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'], $_POST['id'])) {
    exit("Invalid request");
}

$user_id = (int)$_SESSION['user_id'];
$id = (int)$_POST['id'];

$stmt = $conn->prepare("
    DELETE FROM connections
    WHERE id = ?
      AND sender_id = ?
      AND status = 'pending'
");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

header("Location: interests_send.php");
exit;
?>