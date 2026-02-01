<?php include 'includes/header.php'; ?>

<div class="container my-5">
<div class="card shadow p-4">
<h4 class="text-center mb-4">Personal Information</h4>

<form action="personal_submit.php" method="POST" enctype="multipart/form-data">

<!-- AGE AUTO CALCULATED FROM DOB -->
<input class="form-control mb-3" placeholder="Age (Auto calculated from DOB)" disabled>

<input class="form-control mb-3" name="height" placeholder="Height (in feet)" required>
<input class="form-control mb-3" name="skin_color" placeholder="Skin Color" required>

<input class="form-control mb-3" name="contact_number" placeholder="Contact Number" required>
<input class="form-control mb-3" name="whatsapp_number" placeholder="Whatsapp Number">

<input class="form-control mb-3" name="nid_number" placeholder="NID / Birth Certificate No" required>

<label class="fw-bold">Profile Photo</label>
<input type="file" name="profile_photo" class="form-control mb-3" required>

<label class="fw-bold">NID / Birth Certificate Image</label>
<input type="file" name="nid_photo" class="form-control mb-3" required>

<button class="btn btn-danger w-100">Submit Profile</button>
</form>
</div>
</div>

<?php include 'includes/footer.php'; ?>
