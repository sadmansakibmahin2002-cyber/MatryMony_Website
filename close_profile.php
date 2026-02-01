<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'], $_POST['found_partner'])) {
    header("Location: dashboard.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$found_partner = $_POST['found_partner'] === 'yes' ? 'yes' : 'no';
$review = trim($_POST['review'] ?? '');

/* -------- SAVE REVIEW -------- */
$stmt = $conn->prepare("
    INSERT INTO marriage_reviews (user_id, found_partner, review)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iss", $user_id, $found_partner, $review);
$stmt->execute();

/* -------- CLOSE PROFILE -------- */
$stmt = $conn->prepare("
    UPDATE users 
    SET 
        status = 'Rejected',
        membership_status = 'EXPIRED',
        membership_expiry = NULL
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

/* -------- LOGOUT USER -------- */
session_destroy();

header("Location: log_in.php?msg=profile_closed");
exit;
?>