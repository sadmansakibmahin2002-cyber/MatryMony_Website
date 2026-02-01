<?php
session_start();
include 'includes/db_connect.php';

/* ================= LOGIN REQUIRED ================= */
if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$viewer_id  = (int) $_SESSION['user_id'];
$profile_id = (int) ($_GET['id'] ?? 0);

if ($profile_id <= 0) {
    die("Invalid request");
}

/* ================= FETCH BIODATA ================= */
$sql = "
SELECT 
    u.*,
    p.*,
    a.*,
    e.*,
    f.*
FROM users u
LEFT JOIN user_personal_info p ON p.user_id = u.id
LEFT JOIN user_addresses a ON a.user_id = u.id
LEFT JOIN user_education e ON e.user_id = u.id
LEFT JOIN user_family f ON f.user_id = u.id
WHERE u.id = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$d = $stmt->get_result()->fetch_assoc();

if (!$d) {
    die("Profile not found");
}

/* ================= CONNECTION CHECK ================= */
$isConnected = false;

$chk = $conn->prepare("
    SELECT id FROM connections
    WHERE 
    (
        sender_id = ? AND receiver_id = ?
        OR
        sender_id = ? AND receiver_id = ?
    )
    AND status = 'accepted'
    LIMIT 1
");
$chk->bind_param("iiii", $viewer_id, $profile_id, $profile_id, $viewer_id);
$chk->execute();
$isConnected = $chk->get_result()->num_rows > 0;

/* ================= IMAGE ================= */
$imgPath = '';

if ($isConnected && !empty($d['profile_photo'])) {
    $imgPath = 'uploads/' . $d['profile_photo'];
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Biodata</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #fff;
    margin: 0;
    padding: 30px;
}

h1 {
    color: #c0392b;
    border-bottom: 3px solid #c0392b;
    padding-bottom: 10px;
}

.photo img {
    width: 150px;
    height: 180px;
    object-fit: cover;
    border: 1px solid #ccc;
}

.section {
    margin-top: 25px;
}

.section h3 {
    background: #c0392b;
    color: #fff;
    padding: 6px 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

td {
    padding: 8px;
    border-bottom: 1px solid #ddd;
}

.label {
    width: 35%;
    font-weight: bold;
}

/* 🔒 Hide buttons on print */
@media print {
    .no-print {
        display: none;
    }
}
</style>

</head>

<body onload="window.print()">

<h1>PerfectMatch Biodata</h1>

<?php if ($imgPath): ?>
<div class="photo">
    <img src="<?= htmlspecialchars($imgPath) ?>">
</div>
<?php endif; ?>

<h2><?= htmlspecialchars($d['full_name']) ?></h2>

<div class="section">
<h3>Personal Information</h3>
<table>
<tr><td class="label">Marital Status</td><td><?= $d['marital_status'] ?></td></tr>
<tr><td class="label">Age</td><td><?= $d['age'] ?></td></tr>
<tr><td class="label">Height</td><td><?= $d['height'] ?></td></tr>
</table>
</div>

<div class="section">
<h3>Address</h3>
<table>
<tr>
<td class="label">Permanent Address</td>
<td><?= htmlspecialchars($d['permanent_address']) ?>, <?= htmlspecialchars($d['division']) ?></td>
</tr>
</table>
</div>

<div class="section">
<h3>Education</h3>
<table>
<tr><td class="label">Medium</td><td><?= $d['study_medium'] ?></td></tr>
<tr><td class="label">SSC</td><td><?= $d['ssc_group'] ?> (<?= $d['ssc_year'] ?>)</td></tr>
<tr><td class="label">HSC</td><td><?= $d['hsc_group'] ?> (<?= $d['hsc_year'] ?>)</td></tr>
<tr><td class="label">Graduation</td><td><?= $d['graduation_subject'] ?> (<?= $d['graduation_year'] ?>)</td></tr>
</table>
</div>

<div class="section">
<h3>Family Information</h3>
<table>
<tr><td class="label">Total Members</td><td><?= $d['total_members'] ?></td></tr>
</table>
</div>

<?php if ($isConnected): ?>
<div class="section">
<h3>Contact Information</h3>
<table>
<tr><td class="label">Email</td><td><?= htmlspecialchars($d['email']) ?></td></tr>
<tr><td class="label">Contact</td><td><?= htmlspecialchars($d['contact_number']) ?></td></tr>
<tr><td class="label">WhatsApp</td><td><?= htmlspecialchars($d['whatsapp_number']) ?></td></tr>
</table>
</div>
<?php endif; ?>

<div class="no-print" style="margin-top:30px;">
    <button onclick="window.print()">🖨 Print / Save as PDF</button>
</div>

</body>
</html>
