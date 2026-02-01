<?php
session_start();
include 'includes/db_connect.php';

/* ===============================
   LOGIN CHECK
================================ */
if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

/* ===============================
   INPUT VALIDATION
================================ */
if (!isset($_POST['shortlisted_user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$shortlisted_user_id = (int) $_POST['shortlisted_user_id'];

if ($shortlisted_user_id <= 0) {
    header("Location: dashboard.php");
    exit();
}

/* ===============================
   PREVENT SELF SHORTLIST
================================ */
if ($user_id === $shortlisted_user_id) {
    echo "<script>
        alert('You cannot shortlist your own profile!');
        window.location.href='profile_details.php?id=$shortlisted_user_id';
    </script>";
    exit();
}

/* ===============================
   CHECK ALREADY SHORTLISTED
================================ */
$check = $conn->prepare("
    SELECT id FROM shortlists
    WHERE user_id = ? AND shortlisted_user_id = ?
");
$check->bind_param("ii", $user_id, $shortlisted_user_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "<script>
        alert('You have already shortlisted this profile!');
        window.location.href='profile_details.php?id=$shortlisted_user_id';
    </script>";
    exit();
}

/* ===============================
   INSERT SHORTLIST
================================ */
$insert = $conn->prepare("
    INSERT INTO shortlists (user_id, shortlisted_user_id)
    VALUES (?, ?)
");
$insert->bind_param("ii", $user_id, $shortlisted_user_id);

if ($insert->execute()) {
    echo "<script>
        alert('Profile shortlisted successfully!');
        window.location.href='profile_details.php?id=$shortlisted_user_id';
    </script>";
    exit();
} else {
    echo "<script>
        alert('Something went wrong. Please try again.');
        window.location.href='profile_details.php?id=$shortlisted_user_id';
    </script>";
    exit();
}
?>
