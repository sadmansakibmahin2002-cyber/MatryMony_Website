<?php
include 'auth.php';
include '../includes/db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Request");
}

$user_id = (int) $_GET['id'];

/* ---------------- IMAGE RESOLVER FUNCTION ---------------- */
function resolveImage($image)
{
    $default = "../images/no-image.png";

    if (empty($image)) return $default;

    if (strpos($image, '/') !== false && file_exists("../" . ltrim($image, '/'))) {
        return "../" . ltrim($image, '/');
    }

    if (file_exists("../uploads/" . $image)) return "../uploads/" . $image;
    if (file_exists("../images/" . $image)) return "../images/" . $image;

    return $default;
}

/* ---------------- FETCH PROFILE ---------------- */
$sql = "
SELECT 
    u.full_name,
    u.email,
    u.profile_id,
    u.status,

    a.present_address,
    a.permanent_address,
    a.grew_up_place,

    e.study_medium,
    e.ssc_group,
    e.ssc_year,
    e.hsc_group,
    e.hsc_year,
    e.graduation_subject,
    e.graduation_year,

    f.total_members,
    f.father_alive,
    f.mother_alive,
    f.live_with_parents,

    p.age,
    p.height,
    p.skin_color,
    p.contact_number,
    p.whatsapp_number,
    p.nid_number,
    p.profile_photo,
    p.nid_photo

FROM users u
LEFT JOIN user_addresses a ON a.user_id = u.id
LEFT JOIN user_education e ON e.user_id = u.id
LEFT JOIN user_family f ON f.user_id = u.id
LEFT JOIN user_personal_info p ON p.user_id = u.id
WHERE u.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Profile not found");
}

$data = $result->fetch_assoc();

/* STATUS NORMALIZATION */
$status = strtolower(trim($data['status']));

$profilePhoto = resolveImage($data['profile_photo']);
$nidPhoto     = resolveImage($data['nid_photo']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.sidebar {
    height:100vh;
    background:#1f2937;
    padding-top:20px;
    position:fixed;
}
.sidebar a {
    display:block;
    padding:12px 20px;
    color:#cbd5e1;
    text-decoration:none;
    font-weight:500;
}
.sidebar a:hover,.sidebar a.active {
    background:#ef4444;
    color:#fff;
    border-radius:8px;
    margin:3px;
}
.profile-img {
    width:130px;height:130px;
    object-fit:cover;
    border-radius:50%;
    border:3px solid #ef4444;
}
.doc-img {
    max-width:220px;
    border-radius:10px;
    border:2px solid #ddd;
}
.section-title {
    border-bottom:2px solid #ef4444;
    margin-bottom:10px;
    padding-bottom:5px;
}
</style>
</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<div class="col-md-2 sidebar">
    <h4 class="text-center text-white mb-4">Admin Panel</h4>
    <a href="dashboard.php">Pending Profiles</a>
    <a href="approve_profile.php">Approved Profiles</a>
    <a href="reject_profile.php">Rejected Profiles</a>
    <a href="alluser.php">All Users</a>
    <a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- CONTENT -->
<div class="col-md-10 offset-md-2 p-4">
<div class="card p-4 shadow">

<!-- BASIC INFO -->
<div class="row mb-4">
    <div class="col-md-3 text-center">
        <img src="<?= $profilePhoto ?>" class="profile-img mb-2">
        <p class="fw-bold"><?= htmlspecialchars($data['profile_id']) ?></p>
    </div>

    <div class="col-md-9">
        <h4><?= htmlspecialchars($data['full_name']) ?></h4>
        <p>Email: <?= htmlspecialchars($data['email']) ?></p>

        <?php
        $badge = match ($status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'warning'
        };
        ?>
        <span class="badge bg-<?= $badge ?>">
            <?= ucfirst($status) ?>
        </span>
    </div>
</div>

<!-- PERSONAL INFO -->
<h5 class="section-title">Personal Information</h5>
<div class="row">
<div class="col-md-6">
<p>Age: <?= htmlspecialchars($data['age']) ?></p>
<p>Height: <?= htmlspecialchars($data['height']) ?></p>
<p>Skin Color: <?= htmlspecialchars($data['skin_color']) ?></p>
</div>
<div class="col-md-6">
<p>Contact: <?= htmlspecialchars($data['contact_number']) ?></p>
<p>WhatsApp: <?= htmlspecialchars($data['whatsapp_number']) ?></p>
<p>NID No: <?= htmlspecialchars($data['nid_number']) ?></p>
</div>
</div>

<!-- ADDRESS -->
<h5 class="section-title mt-3">Address</h5>
<p>Present Address: <?= htmlspecialchars($data['present_address']) ?></p>
<p>Permanent Address: <?= htmlspecialchars($data['permanent_address']) ?></p>
<p>Grew Up Place: <?= htmlspecialchars($data['grew_up_place']) ?></p>

<!-- EDUCATION -->
<h5 class="section-title mt-3">Education</h5>
<p>Study Medium: <?= htmlspecialchars($data['study_medium']) ?></p>
<p>SSC: <?= htmlspecialchars($data['ssc_group']) ?> (<?= htmlspecialchars($data['ssc_year']) ?>)</p>
<p>HSC: <?= htmlspecialchars($data['hsc_group']) ?> (<?= htmlspecialchars($data['hsc_year']) ?>)</p>
<p>Graduation: <?= htmlspecialchars($data['graduation_subject']) ?> (<?= htmlspecialchars($data['graduation_year']) ?>)</p>

<!-- FAMILY -->
<h5 class="section-title mt-3">Family</h5>
<p>Total Members: <?= htmlspecialchars($data['total_members']) ?></p>
<p>Father Alive: <?= htmlspecialchars($data['father_alive']) ?></p>
<p>Mother Alive: <?= htmlspecialchars($data['mother_alive']) ?></p>
<p>Live With Parents: <?= htmlspecialchars($data['live_with_parents']) ?></p>

<!-- DOCUMENT -->
<h5 class="section-title mt-3">NID / Birth Certificate</h5>
<img src="<?= $nidPhoto ?>" class="doc-img mb-3">

<!-- ACTION BUTTONS -->
<div class="mt-4 text-center">

<?php if ($status === 'pending'): ?>
    <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#approveModal">
        Approve
    </button>

    <button class="btn btn-danger px-4" data-bs-toggle="modal" data-bs-target="#rejectModal">
        Reject
    </button>

<?php elseif ($status === 'approved'): ?>
    <p class="fw-bold text-success">This profile is approved and active.</p>

<?php elseif ($status === 'rejected'): ?>
    <p class="fw-bold text-muted">This profile is rejected.</p>
<?php endif; ?>

<a href="javascript:history.back()" class="btn btn-secondary px-4">Back</a>
</div>

</div>
</div>
</div>
</div>

<!-- APPROVE MODAL -->
<div class="modal fade" id="approveModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">Confirm Approval</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to approve this profile?
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="approve.php?id=<?= $user_id ?>" class="btn btn-success">
            Yes, Approve
        </a>
      </div>
    </div>
  </div>
</div>

<!-- REJECT MODAL -->
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">Confirm Rejection</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to reject this profile?
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="reject.php?id=<?= $user_id ?>" class="btn btn-danger">
            Yes, Reject
        </a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
