<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'], $_POST['receiver_id'])) {
    exit("Invalid request");
}

$sender_id   = (int) $_SESSION['user_id'];
$receiver_id = (int) $_POST['receiver_id'];

/* ✅ NEW: CHECK IF BLOCKED */
$blk = $conn->prepare("
    SELECT id FROM blocked_users
    WHERE blocker_id = ? AND blocked_id = ?
");
$blk->bind_param("ii", $receiver_id, $sender_id);
$blk->execute();

if ($blk->get_result()->num_rows > 0) {
    echo "<script>
        alert('You cannot send request. You are blocked.');
        window.location.href = 'profile_details.php?id=$receiver_id';
    </script>";
    exit();
}

/* ===== CHECK SENDER MEMBERSHIP ===== */
$stmt = $conn->prepare("
    SELECT membership_status, membership_expiry
    FROM users
    WHERE id = ?
      AND membership_status = 'ACTIVE'
      AND membership_expiry >= CURDATE()
");
$stmt->bind_param("i", $sender_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    header("Location: payment.php");
    exit();
}
$stmt->close();

/* ===== PREVENT DUPLICATE REQUEST ===== */
$stmt = $conn->prepare("
    SELECT id FROM connections
    WHERE sender_id = ? AND receiver_id = ?
");
$stmt->bind_param("ii", $sender_id, $receiver_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>
        alert('You have already sent a connection request to this profile.');
        window.location.href = 'profile_details.php?id=$receiver_id';
    </script>";
    exit();
}
$stmt->close();

/* ===== INSERT CONNECTION REQUEST ===== */
$stmt = $conn->prepare("
    INSERT INTO connections (sender_id, receiver_id, status)
    VALUES (?, ?, 'pending')
");
$stmt->bind_param("ii", $sender_id, $receiver_id);

if ($stmt->execute()) {
    echo "<script>
        alert('Connection request sent successfully!');
        window.location.href = 'profile_details.php?id=$receiver_id';
    </script>";
} else {
    echo "<script>
        alert('Something went wrong. Please try again.');
        window.location.href = 'profile_details.php?id=$receiver_id';
    </script>";
}
exit();
