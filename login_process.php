<?php
session_start();
include 'includes/db_connect.php';

/* ---------------- HELPER FUNCTIONS ---------------- */

function redirectError($msg) {
    header("Location: log_in.php?error=" . urlencode($msg));
    exit();
}

function loginSuccess($user) {
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['profile_id'] = $user['profile_id'];
    $_SESSION['full_name']  = $user['full_name'];

    header("Location: dashboard.php");
    exit();
}

/* ---------------- INPUT VALIDATION ---------------- */

$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    redirectError("Email and password are required!");
}

/* ---------------- FETCH USER ---------------- */

$sql = "SELECT id, profile_id, full_name, password 
        FROM users 
        WHERE email = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    redirectError("Invalid email or password!");
}

$user = $result->fetch_assoc();
$dbPassword = $user['password'];

/* ---------------- PASSWORD CHECK ---------------- */

// ✅ CASE 1: Password is HASHED (correct & secure)
if (password_verify($password, $dbPassword)) {

    loginSuccess($user);

}
// ✅ CASE 2: Password is PLAIN TEXT (old users)
elseif ($password === $dbPassword) {

    // 🔐 Upgrade plain password to hashed
    $newHash = password_hash($password, PASSWORD_DEFAULT);

    $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->bind_param("si", $newHash, $user['id']);
    $update->execute();

    loginSuccess($user);

}
// ❌ WRONG PASSWORD
else {
    redirectError("Invalid email or password!");
}
?>