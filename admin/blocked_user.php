<?php
include 'auth.php';
include '../includes/db_connect.php';

/* -------- FETCH BLOCKED USERS -------- */
$sql = "
    SELECT 
        id,
        profile_id,
        full_name,
        email,
        status,
        account_status,
        membership_status,
        restriction_reason,
        created_at
    FROM users
    WHERE account_status = 'blocked'
    ORDER BY id DESC
";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Blocked Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f4f6f9;}
.sidebar{height:100vh;background:#1f2937;padding-top:20px;}
.sidebar a{display:block;padding:12px 20px;color:#cbd5e1;text-decoration:none;font-weight:500;}
.sidebar a:hover,.sidebar a.active{
    background:#ef4444;color:#fff;border-radius:8px;margin:3px;
}
.card{border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.1);border:none;}
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
<a href="reject_profile.php">Rejected Profiles</a>
<a href="blocked_user.php" class="active">Blocked Users</a>
<a href="alluser.php">All Users</a>
<a href="admin_details.php">Admins</a>
<a href="sales_report.php">Sales Report</a>
<a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- CONTENT -->
<div class="col-md-10 p-4">

<div class="card p-3 mb-3">
<h4 class="text-danger">🚫 Blocked Users</h4>
<span class="badge bg-danger fs-6">
Total Blocked: <?= mysqli_num_rows($result) ?>
</span>
</div>

<div class="card p-3">
<table class="table table-bordered table-hover text-center align-middle">
<thead class="table-danger">
<tr>
<th>#</th>
<th>Profile ID</th>
<th>Name</th>
<th>Email</th>
<th>Membership</th>
<th>Block Reason</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php if (mysqli_num_rows($result) > 0): $i=1; while($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $i++ ?></td>
<td><?= htmlspecialchars($row['profile_id']) ?></td>
<td><?= htmlspecialchars($row['full_name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>

<td>
<span class="badge bg-secondary">
<?= htmlspecialchars($row['membership_status']) ?>
</span>
</td>

<td>
<?= !empty($row['restriction_reason']) 
    ? htmlspecialchars($row['restriction_reason']) 
    : '<span class="text-muted">No reason provided</span>'; ?>
</td>

<td>
<a href="veiw_profile.php?id=<?= $row['id'] ?>" 
   class="btn btn-sm btn-primary mb-1">
View
</a>

<form action="user_action.php" method="POST" style="display:inline;">
<input type="hidden" name="user_id" value="<?= $row['id'] ?>">
<button type="submit" name="approve_restricted"
class="btn btn-sm btn-success"
onclick="return confirm('Unblock and restore this user?');">
Unblock
</button>
</form>
</td>
</tr>
<?php endwhile; else: ?>
<tr>
<td colspan="7" class="text-muted">No blocked users found</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>

</div>
</div>
</div>
</body>
</html>
