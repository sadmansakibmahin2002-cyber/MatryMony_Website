<?php
session_start();
include 'includes/db_connect.php';
include 'includes/validate.php';

$user_id = $_SESSION['user_id'];
$errors = [];

$medium = $_POST['study_medium'];
$ssc_group = clean($_POST['ssc_group']);
$ssc_year = $_POST['ssc_year'];
$hsc_group = clean($_POST['hsc_group']);
$hsc_year = $_POST['hsc_year'];
$grad_subject = clean($_POST['graduation_subject']);
$grad_year = $_POST['graduation_year'];

if (!in_array($medium,['General','English Medium','Madrasa'])) $errors[] = "Invalid medium";
if (!validYear($ssc_year)) $errors[] = "Invalid SSC year";
if (!validYear($hsc_year)) $errors[] = "Invalid HSC year";

if ($grad_year && !validYear($grad_year)) $errors[] = "Invalid graduation year";

if ($errors) {
    foreach ($errors as $e) echo "<p style='color:red'>$e</p>";
    exit;
}

$stmt = $conn->prepare("
INSERT INTO user_education 
(user_id, study_medium, ssc_group, ssc_year, hsc_group, hsc_year, graduation_subject, graduation_year)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    "issisisi",
    $user_id,
    $medium,
    $ssc_group,
    $ssc_year,
    $hsc_group,
    $hsc_year,
    $grad_subject,
    $grad_year
);
$stmt->execute();

header("Location: family.php");
?>