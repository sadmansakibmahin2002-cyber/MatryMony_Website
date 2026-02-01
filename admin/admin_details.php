<?php
include 'auth.php';
include '../includes/db_connect.php';

/* Updated query to fetch from the 'admin' table as per your database structure */
$sql = "SELECT id, name, email FROM admins";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Details</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f4f6f9;}
.sidebar{height:100vh;background:#1f2937;padding-top:20px;}
.sidebar a{display:block;padding:12px 20px;color:#cbd5e1;text-decoration:none;font-weight:500;}
.sidebar a:hover,.sidebar a.active{background:#ef4444;color:#fff;border-radius:8px;margin:3px;}
.card{border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.1);border:none;}
/* Specific style for the header layout */
.table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
</style>
</head>

<body>
<div class="container-fluid">
<div class="row">

<div class="col-md-2 sidebar">
<h4 class="text-center text-white mb-4">Admin Panel</h4>
<a href="dashboard.php">Pending Profiles</a>
<a href="approve_profile.php">Approved Profiles</a>
<a href="reject_profile.php">Rejected Profiles</a>
<a href="#">Blocked Users</a>
<a href="alluser.php">All Users</a>
<a href="admin_details.php" class="active">Admins</a>
<a href="logout.php" class="text-danger">Logout</a>
</div>

<div class="col-md-10 p-4">
<div class="card p-4">
    
    <div class="table-header">
        <h4 class="m-0">Admin Details</h4>
        <a href="add_admin.php" class="btn btn-danger text-white" style="font-weight: 500;">+ Add New Admin</a>
    </div>

    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>

        <?php if($result && mysqli_num_rows($result) > 0): $i=1; while($row=mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['name'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="3">No admins found</td></tr>
        <?php endif; ?>

        </tbody>
    </table>

</div>
</div>
</div>
</div>
</body>
</html>