<?php
include 'auth.php';
include '../includes/db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Request");
}

$user_id = (int) $_GET['id'];

/* -------- FIRST-TIME PROFILE APPROVAL -------- */
$stmt = $conn->prepare("
    UPDATE users 
    SET 
        status = 'Approved',
        account_status = 'active',
        membership_status = 
            CASE 
                WHEN membership_status = 'FREE' THEN 'FREE'
                ELSE 'ACTIVE'
            END
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: dashboard.php?msg=approved");
    exit;
} else {
    die("Approval failed");
}
?>
