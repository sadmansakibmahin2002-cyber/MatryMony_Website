<?php
session_start();
include 'includes/db_connect.php';
include 'includes/validate.php';

$errors = [];

$full_name = clean($_POST['full_name']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$contact = clean($_POST['contact']);
$gender = $_POST['gender'];
$marital_status = $_POST['marital_status'];
$dob = $_POST['dob'];
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

/* VALIDATION */
if (strlen($full_name) < 3) $errors[] = "Invalid name";
if (!$email) $errors[] = "Invalid email";
if (!validPhone($contact)) $errors[] = "Contact must be 11 digits";
if (!in_array($gender, ['Male','Female'])) $errors[] = "Invalid gender";
if (!in_array($marital_status, ['Single','Married','Separated','Divorced','Widowed'])) $errors[] = "Invalid marital status";

$age = calculateAge($dob);
if ($age < 18 || $age > 80) $errors[] = "Age must be 18–80";

if (strlen($password) < 6) $errors[] = "Weak password";
if ($password !== $confirm) $errors[] = "Passwords do not match";

if ($errors) {
    foreach ($errors as $e) {
        echo "<p style='color:red'>$e</p>";
    }
    exit;
}

/* ✅ CHECK DUPLICATE EMAIL */
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "<script>
        alert('Email already registered. Please login.');
        window.location.href = 'log_in.php';
    </script>";
    exit;
}

/* INSERT */
$hash = password_hash($password, PASSWORD_DEFAULT);
$profile_id = 'MAT' . rand(10000,99999);

$stmt = $conn->prepare("
    INSERT INTO users (profile_id, full_name, email, contact, gender, dob, password,marital_status)
    VALUES (?, ?, ?,?, ?, ?, ?, ?)
");
$stmt->bind_param("ssssssss", $profile_id, $full_name, $email, $contact, $gender, $dob, $hash,$marital_status);
$stmt->execute();

$_SESSION['user_id'] = $stmt->insert_id;
header("Location: log_in.php");
exit;
?>
