<?php
session_start();
include 'includes/db_connect.php';

$user_id = $_SESSION['user_id'];

$total = (int)$_POST['total_members'];
$father = $_POST['father_alive'];
$mother = $_POST['mother_alive'];
$live = $_POST['live_with_parents'];

if ($total < 1 || $total > 30) die("Invalid family size");
if (!in_array($father,['Yes','No'])) die("Invalid father status");
if (!in_array($mother,['Yes','No'])) die("Invalid mother status");
if (!in_array($live,['Yes','No'])) die("Invalid living status");

$stmt = $conn->prepare("
INSERT INTO user_family (user_id, total_members, father_alive, mother_alive, live_with_parents)
VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("iisss", $user_id, $total, $father, $mother, $live);
$stmt->execute();

header("Location: personal.php");
?>