<?php
include 'auth.php';
include '../includes/db_connect.php';

/* -------- DATE FILTER -------- */
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

$where = "WHERE p.status = 'SUCCESS'";

if (!empty($from) && !empty($to)) {
    $where .= " AND DATE(p.created_at) BETWEEN '$from' AND '$to'";
}

/* -------- TOTAL SALES -------- */
$total_sql = "
SELECT 
    COUNT(*) AS total_orders,
    SUM(p.amount) AS total_amount
FROM payments p
$where
";
$total = mysqli_fetch_assoc(mysqli_query($conn, $total_sql));

$totalOrders = $total['total_orders'] ?? 0;
$totalAmount = $total['total_amount'] ?? 0;

/* -------- SALES DETAILS -------- */
$sql = "
SELECT 
    p.tran_id,
    p.amount,
    p.currency,
    p.payment_method,
    p.created_at,
    u.full_name,
    u.profile_id
FROM payments p
JOIN users u ON u.id = p.user_id
$where
ORDER BY p.created_at DESC
";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f4f6f9;}
.sidebar{height:100vh;background:#1f2937;padding-top:20px;}
.sidebar a{
    display:block;
    padding:12px 20px;
    color:#cbd5e1;
    text-decoration:none;
    font-weight:500;
}
.sidebar a:hover,
.sidebar a.active{
    background:#ef4444;
    color:#fff;
    border-radius:8px;
    margin:3px;
}
.card{
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,.1);
    border:none;
}
</style>
</head>

<body>
<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<div class="col-md-2 sidebar">
    <h4 class="text-center text-white mb-4">Admin Panel</h4>

    <a href="dashboard.php">Pending Profiles</a>
    <a href="approve_profile.php">Approved Profiles</a>
    <a href="reject_profile.php">Reject Profile</a>
    <a href="blocked_user.php">Blocked Users</a>
    <a href="alluser.php">All Users</a>
    <a href="admin_details.php">Admins</a>
    <a href="sales_report.php" class="active">Sales Report</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- CONTENT -->
<div class="col-md-10 p-4">

<h3 class="mb-4">📊 Sales Report</h3>

<!-- FILTER -->
<div class="card p-3 mb-4">
<form method="GET" class="row g-3">
<div class="col-md-3">
<label class="form-label">From Date</label>
<input type="date" name="from" value="<?= $from ?>" class="form-control">
</div>

<div class="col-md-3">
<label class="form-label">To Date</label>
<input type="date" name="to" value="<?= $to ?>" class="form-control">
</div>

<div class="col-md-3 align-self-end">
<button class="btn btn-primary">Filter</button>
<a href="sales_report.php" class="btn btn-secondary">Reset</a>
</div>
</form>
</div>

<!-- SUMMARY -->
<div class="row mb-4">
<div class="col-md-6">
<div class="card p-3 text-center">
<h5>Total Successful Payments</h5>
<h2><?= $totalOrders ?></h2>
</div>
</div>

<div class="col-md-6">
<div class="card p-3 text-center">
<h5>Total Sales</h5>
<h2 class="text-success">
<?= number_format($totalAmount, 2) ?> BDT
</h2>
</div>
</div>
</div>

<!-- TABLE -->
<div class="card p-3">
<table class="table table-bordered table-hover text-center align-middle">
<thead class="table-dark">
<tr>
<th>#</th>
<th>Profile ID</th>
<th>Name</th>
<th>Transaction ID</th>
<th>Payment Method</th>
<th>Amount</th>
<th>Date</th>
</tr>
</thead>

<tbody>
<?php if (mysqli_num_rows($result) > 0): $i=1; while($row=mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $i++ ?></td>
<td><?= htmlspecialchars($row['profile_id']) ?></td>
<td><?= htmlspecialchars($row['full_name']) ?></td>
<td><?= htmlspecialchars($row['tran_id']) ?></td>
<td><?= htmlspecialchars($row['payment_method']) ?></td>
<td class="fw-bold text-success">
<?= number_format($row['amount'],2) ?> <?= htmlspecialchars($row['currency']) ?>
</td>
<td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
</tr>
<?php endwhile; else: ?>
<tr>
<td colspan="7" class="text-muted">No sales found</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
<a 
    href="download_sales_report.php?from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>" 
    target="_blank"
    class="btn btn-success mb-3"
>
    ⬇ Download Sales Report (PDF)
</a>


</div>
</div>
</div>
</body>
</html>
