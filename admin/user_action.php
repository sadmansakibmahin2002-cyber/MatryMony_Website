<?php
session_start();
include 'auth.php';
include '../includes/db_connect.php';

/* -------- SECURITY CHECK -------- */
if (!isset($_POST['user_id'])) {
    header("Location: approve_profile.php");
    exit;
}

$user_id = (int) $_POST['user_id'];

/* -------- BLOCK USER -------- */
if (isset($_POST['block_user'])) {

    $stmt = $conn->prepare("
        UPDATE users 
        SET 
            account_status = 'blocked',
            status = 'Rejected',
            membership_status = 'EXPIRED',
            membership_expiry = NULL
        WHERE id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    header("Location: approve_profile.php?success=blocked");
    exit;
}

/* -------- RESTRICT USER -------- */
if (isset($_POST['restrict_user'])) {

    $stmt = $conn->prepare("
        UPDATE users 
        SET 
            account_status = 'restricted',
            status = 'Pending',
            membership_status = 'HOLD'
        WHERE id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    header("Location: approve_profile.php?success=restricted");
    exit;
}

/* -------- UNBLOCK / APPROVE RESTRICTED USER -------- */
if (isset($_POST['approve_restricted'])) {

    $stmt = $conn->prepare("
        UPDATE users 
        SET 
            account_status = 'active',
            status = 'Approved',
            membership_status = 'EXPIRED',
            membership_expiry = NULL,
            restriction_reason = NULL
        WHERE id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    header("Location: blocked_user.php?success=unblocked");
    exit;
}

/* -------- FALLBACK -------- */
header("Location: approve_profile.php");
exit;
?>
