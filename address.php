<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}
?>

<div class="container my-5">
<div class="card shadow p-4">
<h4 class="text-center mb-4">Address Information</h4>

<form action="address_submit.php" method="POST">


<div class="mb-3">
<label class="fw-bold">Present Address</label>
<textarea name="present_address" class="form-control" required></textarea>
</div>

<div class="mb-3">
<label class="fw-bold">Permanent Address</label>
<textarea name="permanent_address" class="form-control" required></textarea>
</div>

<div class="mb-3">
<label class="fw-bold">Where did you grow up?</label>
<input type="text" name="grew_up_place" class="form-control" required>
</div>

<div class="mb-3">
<label class="fw-bold">Division</label>
<select name="division" class="form-select" required>
    <option value="">Select Division</option>
    <option value="Dhaka">Dhaka</option>
    <option value="Chittagong">Chittagong</option>
    <option value="Khulna">Khulna</option>
    <option value="Rajshahi">Rajshahi</option>
    <option value="Sylhet">Sylhet</option>
    <option value="Barishal">Barishal</option>
    <option value="Rangpur">Rangpur</option>
    <option value="Mymensingh">Mymensingh</option>
</select>
</div>

<button class="btn btn-danger w-100">Next</button>
</form>
</div>
</div>

<?php include 'includes/footer.php'; ?>
