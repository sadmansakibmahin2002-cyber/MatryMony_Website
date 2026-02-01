<?php
include 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f4f6f9;}
.sidebar{height:100vh;background:#1f2937;padding-top:20px;}
.sidebar a{display:block;padding:12px 20px;color:#cbd5e1;text-decoration:none;font-weight:500;}
.sidebar a:hover,.sidebar a.active{background:#ef4444;color:#fff;border-radius:8px;margin:3px;}
.card{border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.1);border:none;}
.btn-primary-custom { background: #ef4444; color: #fff; border: none; }
.btn-primary-custom:hover { background: #dc2626; color: #fff; }
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
<a href="blocked_user.php">Blocked Users</a>
<a href="alluser.php">All Users</a>
<a href="admin_details.php" class="active">Admins</a>
<a href="sales_report.php">Sales Report</a>
<a href="logout.php" class="text-danger">Logout</a>
</div>

<div class="col-md-10 p-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="m-0">Add New Admin</h4>
                    <a href="admin_details.php" class="btn btn-sm btn-secondary">Back</a>
                </div>


                <form action="add_admin_process.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Create password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary-custom">Create Admin Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</body>
</html>