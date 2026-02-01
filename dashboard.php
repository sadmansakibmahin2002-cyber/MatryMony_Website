<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

/* ---------------- PROTECT DASHBOARD ---------------- */
if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* ---------------- USER BASIC INFO ---------------- */
$stmt = $conn->prepare("
    SELECT 
        u.full_name,
        u.email,
        u.profile_id,
        u.status,
        u.account_status,
        u.restriction_reason,
        u.membership_expiry,
        u.membership_status,
        p.name AS package_name,
        p.duration_days
    FROM users u
    LEFT JOIN membership_packages p ON p.id = u.membership_package_id
    WHERE u.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die('User not found');
}

/* ===================================================
   MEMBERSHIP DURATION AUTO-DECREASE LOGIC (ADDED)
   =================================================== */
$remainingDays = 0;

if ($user['membership_status'] === 'ACTIVE' && !empty($user['membership_expiry'])) {

    $today = new DateTime(date('Y-m-d'));
    $expiry = new DateTime($user['membership_expiry']);

    if ($expiry < $today) {

        // Membership expired
        $conn->query("
            UPDATE users 
            SET membership_status = 'EXPIRED',
                membership_expiry = NULL,
                membership_package_id = NULL
            WHERE id = $user_id
        ");

        $user['membership_status'] = 'EXPIRED';
        $user['membership_expiry'] = null;
        $user['package_name'] = 'Free';
        $remainingDays = 0;

    } else {
        $remainingDays = $today->diff($expiry)->days;
    }
}

/* ---------------- ACCOUNT STATUS HANDLING ---------------- */

/* BLOCKED USER */
if ($user['account_status'] === 'blocked') {

    $conn->query("
        UPDATE users 
        SET membership_status = 'EXPIRED',
            membership_expiry = NULL,
            membership_package_id = NULL
        WHERE id = $user_id
    ");
    ?>
    <div class="container mt-5">
        <div class="alert alert-danger text-center">
            <h4>🚫 Your Account Has Been Blocked</h4>
            <p>
                <?= !empty($user['restriction_reason'])
                    ? htmlspecialchars($user['restriction_reason'])
                    : 'Your account has been blocked by the administrator.'; ?>
            </p>
            <p class="fw-bold">You cannot review or access your account.</p>
        </div>
    </div>
    <?php
    include 'includes/footer.php';
    exit();
}

/* RESTRICTED USER FLAG */
$isRestricted = ($user['account_status'] === 'restricted');

/* ---------------- PACKAGE INFO ---------------- */
$packageName      = $user['package_name'] ?? 'Free';
$durationDays     = $remainingDays; // 🔥 remaining days shown
$expiryDate       = $user['membership_expiry'];
$membershipStatus = $user['membership_status'];

/* ---------------- PROFILE COMPLETION ---------------- */
$completion = 0;

/* Address */
$q = $conn->prepare("SELECT id FROM user_addresses WHERE user_id = ?");
$q->bind_param("i", $user_id);
$q->execute();
if ($q->get_result()->num_rows > 0) $completion += 25;

/* Education */
$q = $conn->prepare("SELECT id FROM user_education WHERE user_id = ?");
$q->bind_param("i", $user_id);
$q->execute();
if ($q->get_result()->num_rows > 0) $completion += 25;

/* Family */
$q = $conn->prepare("SELECT id FROM user_family WHERE user_id = ?");
$q->bind_param("i", $user_id);
$q->execute();
if ($q->get_result()->num_rows > 0) $completion += 25;

/* Personal */
$q = $conn->prepare("SELECT profile_photo FROM user_personal_info WHERE user_id = ?");
$q->bind_param("i", $user_id);
$q->execute();
$p = $q->get_result()->fetch_assoc();
if ($p) $completion += 25;

/* Mark completed */
if ($completion == 100) {
    $conn->query("UPDATE users SET profile_completed = 1 WHERE id = $user_id");
}

/* ---------------- PROFILE IMAGE ---------------- */
$profile_img = (!empty($p['profile_photo']) && file_exists("uploads/" . $p['profile_photo']))
    ? "uploads/" . $p['profile_photo'] . "?v=" . time()
    : "assets/profile.png";

/* ---------------- PROFILE STATUS ---------------- */
if ($user['status'] === 'Approved') {
    $profile_status = "Active Profile";
    $badge = "success";
} elseif ($user['status'] === 'Rejected') {
    $profile_status = "Rejected by Admin";
    $badge = "danger";
} else {
    $profile_status = "Under Review";
    $badge = "warning";
}

/* ---------------- CONNECTION COUNTS ---------------- */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM connections
    WHERE sender_id = ? AND status = 'pending'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sent = (int)$stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM connections
    WHERE receiver_id = ? AND status = 'pending'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$received = (int)$stmt->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.sidebar { background:#fff; min-height:100vh; border-right:1px solid #ddd; }
.sidebar a { display:block; padding:12px; color:#333; font-weight:500; }
.sidebar a.active, .sidebar a:hover {
    background:#ff5c5c; color:#fff;
    border-radius:8px; margin:5px;
}
.profile-img {
    width:120px; height:120px;
    border-radius:50%;
    border:3px solid #ff5c5c;
    object-fit:cover;
}
.card { border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.1); }
</style>
</head>

<body>
<div class="container-fluid">
<div class="row">

<!-- Sidebar -->
<div class="col-md-3 sidebar p-3">
<h4 class="text-center mb-4">My Dashboard</h4>
<a class="active">Dashboard</a>
<a href="address.php">Complete Your Profile</a>
<a href="edit_profiles.php">Edit Profiles</a>
<a href="partner_preferences.php">Partner Preferences</a>
<a href="dashboard_shortlist.php">Shortlist Profiles</a>
<a href="interests_send.php">Connection Sent</a>
<a href="interests_receive.php">Connection Received</a>
<a href="connected_profiles.php">Connected Profiles</a>
<a href="block_profile.php">Blocked Users</a>
<a href="logout.php" class="text-danger">Logout</a>
</div>

<!-- Content -->
<div class="col-md-9 p-4">

<?php if ($isRestricted): ?>
<div class="alert alert-warning">
    <h5>⚠ Account Restricted</h5>
    <p>
        <?= !empty($user['restriction_reason'])
            ? htmlspecialchars($user['restriction_reason'])
            : 'Your account is under admin review.'; ?>
    </p>
</div>
<?php endif; ?>

<div class="card p-4 mb-4">
<div class="row align-items-center">

<div class="col-md-3 text-center">
<img src="<?= $profile_img ?>" class="profile-img">
</div>

<div class="col-md-9">
<h3>Welcome, <?= htmlspecialchars($user['full_name']) ?></h3>

<p>
Email: <?= htmlspecialchars($user['email']) ?> |
Profile ID: <?= htmlspecialchars($user['profile_id']) ?>
</p>

<p class="mb-1"><strong>Package:</strong> <?= htmlspecialchars($packageName) ?></p>
<p class="mb-1"><strong>Remaining Days:</strong> <?= $durationDays > 0 ? $durationDays . ' Days' : 'Expired' ?></p>
<p class="mb-1"><strong>Expiry Date:</strong> <?= $expiryDate ? date('d M Y', strtotime($expiryDate)) : 'N/A' ?></p>

<p>
<strong>Membership Status:</strong>
<span class="badge bg-info"><?= htmlspecialchars($membershipStatus) ?></span>
</p>

<span class="badge bg-<?= $badge ?>"><?= $profile_status ?></span>

<label class="fw-bold mt-3">Profile Completion</label>
<div class="progress" style="height:10px;">
<div class="progress-bar bg-success" style="width: <?= $completion ?>%"></div>
</div>
<small><?= $completion ?>% completed</small>
</div>

</div>
</div>

<div class="row">
<div class="col-md-6">
<div class="card text-center p-3">
<h5>Connection Received</h5>
<h3 class="text-danger"><?= $received ?></h3>
</div>
</div>

<div class="col-md-6">
<div class="card text-center p-3">
<h5>Connection Sent</h5>
<h3 class="text-primary"><?= $sent ?></h3>
</div>
</div>
</div>
<div class="card p-4 mt-4 border-success">
<h4 class="text-success">💍 Got Married?</h4>
<p class="mb-3">
If you have found your life partner and want to close your profile,
you can do it here. Your profile will be permanently closed.
</p>

<form action="close_profile.php" method="POST"
onsubmit="return confirm('Are you sure you want to close your profile permanently?');">

<div class="mb-3">
<label class="form-label fw-bold">
Did you find your partner from this website?
</label>
<select name="found_partner" class="form-select" required>
<option value="">-- Select --</option>
<option value="yes">Yes</option>
<option value="no">No</option>
</select>
</div>

<div class="mb-3">
<label class="form-label fw-bold">
Leave a Review (Optional)
</label>
<textarea name="review" class="form-control" rows="3"
placeholder="Share your experience with us..."></textarea>
</div>

<button type="submit" name="close_profile"
class="btn btn-danger">
Close My Profile
</button>
</form>
</div>

</div>
</div>
</div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
