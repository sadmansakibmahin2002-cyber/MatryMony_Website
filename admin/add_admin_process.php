<?php
session_start();
include 'auth.php';
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Error: Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // 2. Check if email already exists in 'admins' table
    $checkEmail = "SELECT email FROM admins WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Error: This email is already registered as an admin!'); window.history.back();</script>";
        exit();
    }

    // 3. Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // 4. Insert into the 'admins' table
    // Note: Ensure your table has a 'password' column. 
    // If not, run: ALTER TABLE admins ADD COLUMN password VARCHAR(255);
    $sql = "INSERT INTO admins (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Success: New admin added successfully!'); window.location.href='admin_details.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // If someone tries to access this file directly without posting the form
    header("Location: add_admin.php");
    exit();
}
?>