<?php
include 'auth.php'; // admin auth
include '../includes/db_connect.php';

/*
 LOGIC FIX:
 - Removed user_profiles table
 - Pending profiles come from users.status = 'pending'
 - profile_completed = 1 ensures full submission
*/

$sql = "
SELECT 
    u.id AS user_id,
    u.full_name,
    u.email,
    pi.profile_photo,
    pi.nid_photo
FROM users u
JOIN user_personal_info pi ON pi.user_id = u.id
WHERE u.profile_completed = 1
AND u.status = 'pending'
ORDER BY u.id DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Pending Profiles</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
}
.sidebar {
    height: 100vh;
    background: #1f2937;
    padding-top: 20px;
}
.sidebar a {
    display: block;
    padding: 12px 20px;
    color: #cbd5e1;
    text-decoration: none;
    font-weight: 500;
}
.sidebar a:hover,
.sidebar a.active {
    background: #ef4444;
    color: #fff;
    border-radius: 8px;
    margin: 3px;
}
.card {
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,.1);
    border: none;
}
.profile-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #ef4444;
}
</style>
</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR (UNCHANGED) -->
<div class="col-md-2 sidebar">
    <h4 class="text-center text-white mb-4">Admin Panel</h4>

    <a href="dashboard.php" class="active">Pending Profiles</a>
    <a href="approve_profile.php">Approved Profiles</a>
    <a href="reject_profile.php">Reject Profile</a>
    <a href="blocked_user.php">Blocked Users</a>
    <a href="alluser.php">All Users</a>
    <a href="admin_details.php">Admins</a>
    <a href="sales_report.php">Sales Report</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="col-md-10 p-4">

<div class="card p-4">
<h4 class="text-danger mb-3">
Pending User Profiles
<span class="badge bg-danger"><?= $result->num_rows ?></span>
</h4>

<?php if ($result->num_rows === 0): ?>
    <div class="alert alert-success">No pending profiles 🎉</div>
<?php else: ?>

<table class="table table-bordered align-middle text-center">
<thead class="table-light">
<tr>
<th>Name</th>
<th>Email</th>
<th>Profile Photo</th>
<th>NID / Birth Certificate</th>
<th>Action</th>
</tr>
</thead>

<tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>

<td>
<strong><?= htmlspecialchars($row['full_name']); ?></strong>
</td>

<td>
<?= htmlspecialchars($row['email']); ?>
</td>

<td>
<img src="../uploads/<?= htmlspecialchars($row['profile_photo']); ?>" 
     class="profile-img">
</td>

<td>
<a href="../uploads/<?= htmlspecialchars($row['nid_photo']); ?>" 
   target="_blank"
   class="btn btn-secondary btn-sm">
    View Document
</a>
</td>

<td>
<a href="veiw_profile.php?id=<?= $row['user_id']; ?>" 
   class="btn btn-primary btn-sm mb-1">
   View Profile
</a>
</td>

</tr>
<?php endwhile; ?>
</tbody>

</table>
<?php endif; ?>

</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
