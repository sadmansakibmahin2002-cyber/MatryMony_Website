<?php
session_start();
include 'includes/header.php';
?>

<div class="container my-5">
<div class="row justify-content-center">
<div class="col-md-6">

<div class="card shadow p-4">
<h4 class="text-center mb-4">Create Your Account</h4>

<form action="register_process.php" method="POST">

<div class="mb-3">
<label class="fw-bold">Full Name</label>
<input type="text" name="full_name" class="form-control" required>
</div>

<div class="mb-3">
<label class="fw-bold">Email Address</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label class="fw-bold">Mobile Number</label>
<input type="text" name="contact" class="form-control" required>
</div>

<div class="mb-3">
<label class="fw-bold">Gender</label>
<select name="gender" class="form-select" required>
<option value="">Select</option>
<option>Male</option>
<option>Female</option>
</select>
</div>

<div class="mb-3">
<label class="fw-bold">Marital Status</label>
<select name="marital_status" class="form-select" required>
<option value="">Select</option>
<option>Single</option>
<option>Married</option>
<option>Separated</option>
<option>Divorced</option>
<option>Widowed</option>
</select>
</div>

<div class="mb-3">
<label class="fw-bold">Date of Birth</label>
<input type="date" name="dob" class="form-control" required>
</div>

<div class="mb-3">
<label class="fw-bold">Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
<label class="fw-bold">Confirm Password</label>
<input type="password" name="confirm_password" class="form-control" required>
</div>

<button class="btn btn-danger w-100">Register</button>

<p class="text-center mt-3">
Already have an account?
<a href="log_in.php">Login</a>
</p>

</form>
</div>

</div>
</div>
</div>

<?php include 'includes/footer.php'; ?>
