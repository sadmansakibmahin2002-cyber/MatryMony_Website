<?php
session_start();
include 'includes/db_connect.php';

/* ================= LOGIN REQUIRED ================= */
if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$user_id = (int)$_SESSION['user_id'];
$tran_id = $_GET['tran_id'] ?? '';

if ($tran_id === '') {
    die("Invalid request");
}

/* ================= FETCH PAYMENT ================= */
$stmt = $conn->prepare("
    SELECT 
        p.tran_id,
        p.amount,
        p.currency,
        p.status,
        p.updated_at,
        mp.name AS package_name,
        mp.duration_days,
        u.full_name,
        u.email,
        u.membership_expiry
    FROM payments p
    JOIN users u ON u.id = p.user_id
    JOIN membership_packages mp ON mp.id = p.package_id
    WHERE p.tran_id = ?
      AND p.user_id = ?
      AND p.status = 'SUCCESS'
    LIMIT 1
");

$stmt->bind_param("si", $tran_id, $user_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Receipt not found");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payment Receipt</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #fff;
    margin: 0;
    padding: 30px;
}

.receipt {
    max-width: 700px;
    margin: auto;
    border: 1px solid #ddd;
    padding: 30px;
}

.header {
    display: flex;
    align-items: center;
    gap: 15px;
    border-bottom: 3px solid #c0392b;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.header img {
    width: 60px;
}

.header h1 {
    margin: 0;
    color: #c0392b;
}

.section {
    margin-top: 25px;
}

.section h3 {
    background: #c0392b;
    color: #fff;
    padding: 6px 10px;
    font-size: 16px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

td {
    padding: 8px;
    border-bottom: 1px solid #ddd;
}

.label {
    width: 40%;
    font-weight: bold;
}

.footer {
    margin-top: 30px;
    text-align: center;
    font-size: 12px;
    color: #777;
}

@media print {
    .no-print {
        display: none;
    }
}
</style>

</head>

<body onload="window.print()">

<div class="receipt">

    <!-- HEADER -->
    <div class="header">
        <img src="assets/logo.png" alt="Logo">
        <div>
            <h1>PerfectMatch Matrimony</h1>
            <small>Payment Receipt</small>
        </div>
    </div>

    <!-- USER INFO -->
    <div class="section">
        <h3>Customer Information</h3>
        <table>
            <tr><td class="label">Full Name</td><td><?= htmlspecialchars($data['full_name']) ?></td></tr>
            <tr><td class="label">Email</td><td><?= htmlspecialchars($data['email']) ?></td></tr>
        </table>
    </div>

    <!-- PAYMENT INFO -->
    <div class="section">
        <h3>Payment Details</h3>
        <table>
            <tr><td class="label">Transaction ID</td><td><?= htmlspecialchars($data['tran_id']) ?></td></tr>
            <tr><td class="label">Package</td><td><?= htmlspecialchars($data['package_name']) ?></td></tr>
            <tr><td class="label">Amount Paid</td><td>৳ <?= number_format($data['amount'], 2) ?> <?= htmlspecialchars($data['currency']) ?></td></tr>
            <tr><td class="label">Payment Date</td><td><?= date('d M Y, h:i A', strtotime($data['updated_at'])) ?></td></tr>
            <tr><td class="label">Status</td><td><strong>SUCCESS</strong></td></tr>
        </table>
    </div>

    <!-- MEMBERSHIP INFO -->
    <div class="section">
        <h3>Membership Information</h3>
        <table>
            <tr><td class="label">Validity</td><td><?= $data['duration_days'] ?> Days</td></tr>
            <tr><td class="label">Expires On</td><td><?= date('d M Y', strtotime($data['membership_expiry'])) ?></td></tr>
        </table>
    </div>

    <div class="footer">
        This is a system generated receipt.<br>
        © <?= date('Y') ?> PerfectMatch Matrimony Platform
    </div>

    <div class="no-print" style="margin-top:20px;text-align:center;">
        <button onclick="window.print()">🖨 Print / Save as PDF</button>
    </div>

</div>

</body>
</html>
