<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'], $_POST['id'], $_POST['action'])) {
    exit("Invalid request");
}

$user_id = (int)$_SESSION['user_id'];
$id      = (int)$_POST['id'];
$action  = $_POST['action'];

$status = ($action === 'accept') ? 'accepted' : 'rejected';

$stmt = $conn->prepare("
    UPDATE connections
    SET status = ?
    WHERE id = ?
      AND receiver_id = ?
      AND status = 'pending'
");
$stmt->bind_param("sii", $status, $id, $user_id);
$stmt->execute();

header("Location: interests_receive.php");
exit;
?>