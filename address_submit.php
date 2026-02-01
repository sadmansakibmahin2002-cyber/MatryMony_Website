<?php
session_start();
include 'includes/db_connect.php';
include 'includes/validate.php';

$user_id = $_SESSION['user_id'];
$errors = [];

$present = clean($_POST['present_address']);
$permanent = clean($_POST['permanent_address']);
$grew = clean($_POST['grew_up_place']);
$division = $_POST['division'];

if (strlen($present) < 10) $errors[] = "Invalid present address";
if (strlen($permanent) < 10) $errors[] = "Invalid permanent address";
if (strlen($grew) < 3) $errors[] = "Invalid grew up place";

$allowed = ['Dhaka','Chittagong','Khulna','Rajshahi','Sylhet','Barishal','Rangpur','Mymensingh'];
if (!in_array($division, $allowed)) $errors[] = "Invalid division";

if ($errors) {
    foreach ($errors as $e) echo "<p style='color:red'>$e</p>";
    exit;
}

$stmt = $conn->prepare("
INSERT INTO user_addresses (user_id, present_address, permanent_address, grew_up_place, division)
VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("issss", $user_id, $present, $permanent, $grew, $division);
$stmt->execute();

header("Location: education.php");
?>