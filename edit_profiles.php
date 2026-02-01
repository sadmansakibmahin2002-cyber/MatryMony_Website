<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* ================= CHECK IF ROW EXISTS ================= */
$check = $conn->prepare("SELECT id FROM user_personal_info WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$exists = $check->get_result()->num_rows > 0;

/* ================= SAVE / UPDATE INFO ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $height   = $_POST['height'];
    $skin     = $_POST['skin_color'];
    $age      = (int)$_POST['age'];
    $contact  = $_POST['contact_number'];
    $whatsapp = $_POST['whatsapp_number'];
    $nid      = $_POST['nid_number'];

    /* ---------- FETCH OLD IMAGES ---------- */
    $old = $conn->prepare("SELECT profile_photo, nid_photo FROM user_personal_info WHERE user_id = ?");
    $old->bind_param("i", $user_id);
    $old->execute();
    $oldData = $old->get_result()->fetch_assoc();

    $profile_photo = $oldData['profile_photo'] ?? null;
    $nid_photo     = $oldData['nid_photo'] ?? null;

    /* ---------- PROFILE PHOTO ---------- */
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $profile_photo = time() . "_profile." . $ext;
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], "uploads/" . $profile_photo);
    }

    /* ---------- NID PHOTO ---------- */
    if (isset($_FILES['nid_photo']) && $_FILES['nid_photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['nid_photo']['name'], PATHINFO_EXTENSION);
        $nid_photo = time() . "_nid." . $ext;
        move_uploaded_file($_FILES['nid_photo']['tmp_name'], "uploads/" . $nid_photo);
    }

    /* ---------- UPDATE OR INSERT ---------- */
    if ($exists) {
        // UPDATE
        $stmt = $conn->prepare("
            UPDATE user_personal_info
            SET height = ?, skin_color = ?, age = ?, contact_number = ?,
                whatsapp_number = ?, nid_number = ?, profile_photo = ?, nid_photo = ?
            WHERE user_id = ?
        ");
        $stmt->bind_param(
            "ssisssssi",
            $height,
            $skin,
            $age,
            $contact,
            $whatsapp,
            $nid,
            $profile_photo,
            $nid_photo,
            $user_id
        );
    } else {
        // INSERT (first time only)
        $stmt = $conn->prepare("
            INSERT INTO user_personal_info
            (user_id, height, skin_color, age, contact_number, whatsapp_number, nid_number, profile_photo, nid_photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ississsss",
            $user_id,
            $height,
            $skin,
            $age,
            $contact,
            $whatsapp,
            $nid,
            $profile_photo,
            $nid_photo
        );
    }

    $stmt->execute();

    // Redirect to reload fresh data
    header("Location: dashboard.php");
    exit();
}

/* ================= FETCH UPDATED INFO ================= */
$stmt = $conn->prepare("SELECT * FROM user_personal_info WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$info = $stmt->get_result()->fetch_assoc();
?>

<div class="container my-5">
<div class="card p-4">

<h4>Edit Personal Information</h4>

<form method="post" enctype="multipart/form-data">

<div class="row">

<div class="col-md-4">
<label>Height</label>
<input type="text" name="height" class="form-control"
value="<?= htmlspecialchars($info['height'] ?? '') ?>" required>
</div>

<div class="col-md-4">
<label>Skin Color</label>
<input type="text" name="skin_color" class="form-control"
value="<?= htmlspecialchars($info['skin_color'] ?? '') ?>" required>
</div>

<div class="col-md-4">
<label>Age</label>
<input type="number" name="age" class="form-control"
value="<?= htmlspecialchars($info['age'] ?? '') ?>" required>
</div>

<div class="col-md-6 mt-3">
<label>Contact Number</label>
<input type="text" name="contact_number" class="form-control"
value="<?= htmlspecialchars($info['contact_number'] ?? '') ?>" required>
</div>

<div class="col-md-6 mt-3">
<label>WhatsApp Number</label>
<input type="text" name="whatsapp_number" class="form-control"
value="<?= htmlspecialchars($info['whatsapp_number'] ?? '') ?>">
</div>

<div class="col-md-12 mt-3">
<label>NID Number</label>
<input type="text" name="nid_number" class="form-control"
value="<?= htmlspecialchars($info['nid_number'] ?? '') ?>" required>
</div>

<div class="col-md-6 mt-3">
<label>Profile Photo</label>
<input type="file" name="profile_photo" class="form-control">
</div>

<div class="col-md-6 mt-3">
<label>NID Photo</label>
<input type="file" name="nid_photo" class="form-control">
</div>

</div>

<button class="btn btn-danger mt-4">Save Information</button>

</form>

</div>
</div>

<?php include 'includes/footer.php'; ?>
