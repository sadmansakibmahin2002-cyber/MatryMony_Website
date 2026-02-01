<?php
session_start();

/* If already logged in */
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container mt-5">
<div class="col-md-4 mx-auto">
<div class="card p-4 shadow">

<h4 class="text-center mb-3">Admin Login</h4>

<?php if ($error): ?>
<div class="alert alert-danger text-center">
    Invalid Email or Password
</div>
<?php endif; ?>

<form method="POST" action="login_process.php">
    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
    <button class="btn btn-danger w-100">Login</button>
</form>

</div>
</div>
</div>
</body>
</html>
