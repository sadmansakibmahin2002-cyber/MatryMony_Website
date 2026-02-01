<?php include 'includes/header.php'; ?>

<div class="container my-5">
<div class="card shadow p-4">
<h4 class="text-center mb-4">Family Details</h4>

<form action="family_submit.php" method="POST">

<div class="mb-3">
<label>Total Family Members</label>
<input type="number" name="total_members" class="form-control" required>
</div>

<select name="father_alive" class="form-select mb-3" required>
<option value="">Father Alive?</option>
<option>Yes</option>
<option>No</option>
</select>

<select name="mother_alive" class="form-select mb-3" required>
<option value="">Mother Alive?</option>
<option>Yes</option>
<option>No</option>
</select>

<select name="live_with_parents" class="form-select mb-3" required>
<option value="">Live with Parents?</option>
<option>Yes</option>
<option>No</option>
</select>

<button class="btn btn-danger w-100">Next</button>
</form>
</div>
</div>

<?php include 'includes/footer.php'; ?>
