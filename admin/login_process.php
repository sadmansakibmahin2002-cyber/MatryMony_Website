<?php
session_start();
include '../includes/db_connect.php';

/* Only POST allowed */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header("Location: index.php?error=1");
    exit();
}

/* Fetch admin */
$sql = "SELECT id, password FROM admins WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

/* Admin exists? */
if ($result->num_rows !== 1) {
    header("Location: index.php?error=1");
    exit();
}

$admin = $result->fetch_assoc();
$db_password = $admin['password'];

$login_success = false;

// 1. Check if it's a Hashed Password
if (password_verify($password, $db_password)) {
    $login_success = true;
} 
// 2. Check if it's a Plain Text Password (Fallback)
else if ($password === $db_password) {
    $login_success = true;
    
    /* OPTIONAL: Update the plain text password to a hash automatically */
    // $new_hash = password_hash($password, PASSWORD_BCRYPT);
    // $conn->query("UPDATE admins SET password = '$new_hash' WHERE id = " . $admin['id']);
}

/* Final Verification */
if ($login_success) {
    /* Login success */
    $_SESSION['admin_id'] = $admin['id'];
    session_regenerate_id(true);

    header("Location: dashboard.php");
    exit();
} else {
    /* Login failed */
    header("Location: index.php?error=1");
    exit();
}
?>