<?php
session_start();
$success = $_GET['register'] ?? '';
include 'includes/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow mx-auto" style="max-width:420px;">
        
        <?php if ($success === 'success'): ?>
            <div class="alert alert-success">
                Registration successful! Please log in.
            </div>
        <?php endif; ?>

        <h3 class="text-center mb-3">Login</h3>

        <form action="login_process.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button class="btn btn-danger w-100">Login</button>

        </form>

    </div>
</div>

</body>
</html>
<?php
include 'includes/footer.php';
?>