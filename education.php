<?php
session_start();
include 'includes/header.php';
?>

<div class="container my-5">
<div class="card shadow p-4">
<h4 class="text-center mb-4">Educational Qualification</h4>

<form action="education_submit.php" method="POST">

<div class="mb-3">
<label class="fw-bold">Study Medium</label>
<select name="study_medium" class="form-select" required>
<option value="">Select</option>
<option>General</option>
<option>English Medium</option>
<option>Madrasa</option>
</select>
</div>

<div class="row">
<div class="col-md-6 mb-3">
<label>SSC Group</label>
<input name="ssc_group" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>SSC Passing Year</label>
<input type="number" name="ssc_year" class="form-control" required>
</div>
</div>

<div class="row">
<div class="col-md-6 mb-3">
<label>HSC Group</label>
<input name="hsc_group" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>HSC Passing Year</label>
<input type="number" name="hsc_year" class="form-control" required>
</div>
</div>

<div class="mb-3">
<label>Graduation Subject</label>
<input name="graduation_subject" class="form-control">
</div>

<div class="mb-3">
<label>Graduation Passing / Running Year</label>
<input type="number" name="graduation_year" class="form-control">
</div>

<button class="btn btn-danger w-100">Next</button>
</form>
</div>
</div>

<?php include 'includes/footer.php'; ?>
