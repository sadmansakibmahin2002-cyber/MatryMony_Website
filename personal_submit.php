<?php
session_start();
include 'includes/db_connect.php';
include 'includes/validate.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$errors = [];

/* ---------------- FETCH DOB ---------------- */
$stmt = $conn->prepare("SELECT dob FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($dob);
$stmt->fetch();
$stmt->close();

if (!$dob) {
    die("DOB not found");
}

/* ---------------- AGE AUTO CALCULATION ---------------- */
$age = calculateAge($dob);
if ($age < 18 || $age > 80) {
    $errors[] = "Age must be between 18 and 80";
}

/* ---------------- HEIGHT ---------------- */
$height = (float)$_POST['height'];
if ($height < 3 || $height > 8) {
    $errors[] = "Height must be between 3 and 8 feet";
}

/* ---------------- SKIN COLOR ---------------- */
$skin_color = clean($_POST['skin_color']);
if (strlen($skin_color) < 3) {
    $errors[] = "Invalid skin color";
}

/* ---------------- CONTACT NUMBER ---------------- */
$contact_number = $_POST['contact_number'];
if (!validPhone($contact_number)) {
    $errors[] = "Contact number must be exactly 11 digits";
}

/* ---------------- WHATSAPP NUMBER ---------------- */
$whatsapp_number = $_POST['whatsapp_number'];
if (!empty($whatsapp_number) && !validPhone($whatsapp_number)) {
    $errors[] = "Whatsapp number must be exactly 11 digits";
}

/* ---------------- NID NUMBER ---------------- */
$nid_number = clean($_POST['nid_number']);
if (strlen($nid_number) < 8) {
    $errors[] = "Invalid NID / Birth Certificate number";
}

/* ---------------- IMAGE VALIDATION ---------------- */
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];

if (
    $_FILES['profile_photo']['error'] !== 0 ||
    !in_array($_FILES['profile_photo']['type'], $allowed_types)
) {
    $errors[] = "Invalid profile photo";
}

if (
    $_FILES['nid_photo']['error'] !== 0 ||
    !in_array($_FILES['nid_photo']['type'], $allowed_types)
) {
    $errors[] = "Invalid NID photo";
}

/* ---------------- STOP IF ERRORS ---------------- */
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>$error</p>";
    }
    exit();
}

/* ---------------- IMAGE UPLOAD (YOUR LOGIC) ---------------- */
$profile = time().'_profile.jpg';
$nid = time().'_nid.jpg';

move_uploaded_file($_FILES['profile_photo']['tmp_name'], "uploads/".$profile);
move_uploaded_file($_FILES['nid_photo']['tmp_name'], "uploads/".$nid);

/* ---------------- INSERT INTO DATABASE ---------------- */
$stmt = $conn->prepare("
INSERT INTO user_personal_info
(user_id, age, height, skin_color, contact_number, whatsapp_number, nid_number, profile_photo, nid_photo)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iidssssss",
    $user_id,
    $age,
    $height,
    $skin_color,
    $contact_number,
    $whatsapp_number,
    $nid_number,
    $profile,
    $nid
);

$stmt->execute();
$stmt->close();

/* ---------------- UPDATE PROFILE STATUS ---------------- */
$conn->query("UPDATE users SET profile_completed = 1 WHERE id = $user_id");

/* ---------------- REDIRECT ---------------- */
header("Location: dashboard.php");
exit();
?>