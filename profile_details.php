<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

$profile_id = (int)($_GET['id'] ?? 0);
$viewer_id  = $_SESSION['user_id'] ?? 0;
$isLoggedIn = isset($_SESSION['user_id']);

if ($profile_id <= 0) {
    die("Invalid profile");
}

/* ================= FETCH BIODATA ================= */
$sql = "
SELECT 
    u.full_name,
    u.email,

    p.age,
    p.height,
    p.contact_number,
    p.whatsapp_number,
    p.profile_photo,
    p.nid_number,
    p.nid_photo,

    a.division,
    a.permanent_address,

    e.study_medium,
    e.ssc_group,
    e.ssc_year,
    e.hsc_group,
    e.hsc_year,
    e.graduation_subject,
    e.graduation_year,

    f.total_members

FROM users u
LEFT JOIN user_personal_info p ON p.user_id = u.id
LEFT JOIN user_addresses a ON a.user_id = u.id
LEFT JOIN user_education e ON e.user_id = u.id
LEFT JOIN user_family f ON f.user_id = u.id
WHERE u.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) {
    die("Profile not found");
}

/* ================= CONNECTION CHECK ================= */
$isConnected = false;

if ($isLoggedIn) {
    $chk = $conn->prepare("
        SELECT id FROM connections 
        WHERE 
        (
            sender_id = ? AND receiver_id = ?
            OR
            sender_id = ? AND receiver_id = ?
        )
        AND status = 'accepted'
        LIMIT 1
    ");
    $chk->bind_param("iiii", $viewer_id, $profile_id, $profile_id, $viewer_id);
    $chk->execute();
    $isConnected = $chk->get_result()->num_rows > 0;
}

/* ================= IMAGE LOGIC ================= */
$imgPath = "assets/profile.png";

if ($isConnected && !empty($data['profile_photo'])) {
    $imgPath = "uploads/" . $data['profile_photo'];
}
?>

<div class="container my-5">
<div class="card p-4">

<h3><?= htmlspecialchars($data['full_name']) ?></h3>

<img src="<?= htmlspecialchars($imgPath) ?>" width="180" class="mb-3">

<hr>

<h5>Personal Information</h5>
<p><strong>Age:</strong> <?= $data['age'] ?></p>
<p><strong>Height:</strong> <?= $data['height'] ?></p>

<hr>

<h5>Address</h5>
<p><?= htmlspecialchars($data['permanent_address']) ?>, <?= htmlspecialchars($data['division']) ?></p>

<hr>

<h5>Education</h5>
<p><strong>Medium:</strong> <?= $data['study_medium'] ?></p>
<p><strong>SSC:</strong> <?= $data['ssc_group'] ?> (<?= $data['ssc_year'] ?>)</p>
<p><strong>HSC:</strong> <?= $data['hsc_group'] ?> (<?= $data['hsc_year'] ?>)</p>
<p><strong>Graduation:</strong> <?= $data['graduation_subject'] ?> (<?= $data['graduation_year'] ?>)</p>

<hr>

<h5>Family</h5>
<p>Total Members: <?= $data['total_members'] ?></p>

<hr>

<?php if ($isConnected): ?>
    <h5>Contact Information</h5>
    <p>Email: <?= htmlspecialchars($data['email']) ?></p>
    <p>Contact: <?= htmlspecialchars($data['contact_number']) ?></p>
    <p>WhatsApp: <?= htmlspecialchars($data['whatsapp_number']) ?></p>

    <!-- ✅ NEW: DISCONNECT & BLOCK -->
    <hr>

    <form method="post" action="disconnect.php" class="d-inline">
        <input type="hidden" name="user_id" value="<?= $profile_id ?>">
        <button class="btn btn-warning">Disconnect</button>
    </form>

    <form method="post" action="blocked_user.php" class="d-inline">
        <input type="hidden" name="user_id" value="<?= $profile_id ?>">
        <button class="btn btn-danger">Block</button>
    </form>

<?php else: ?>
    <div class="alert alert-warning">
        🔒 Connect to see photo & contact details
    </div>
    <?php if (!$isLoggedIn): ?>
        <div>Please <a href="register.php" class="btn btn-sm btn-primary ms-2">Sign Up</a> or <a href="log_in.php" class="btn btn-sm btn-primary ms-2">Log In</a> to connect.</div>  
    <?php endif; ?>
<?php endif; ?>

<?php if ($isLoggedIn && !$isConnected): ?>
    <form method="post" action="send_connection.php" class="d-inline">
        <input type="hidden" name="receiver_id" value="<?= $profile_id ?>">
        <button class="btn btn-danger">Send Connection</button>
    </form>

    <form method="post" action="shortlist.php" class="d-inline">
        <input type="hidden" name="shortlisted_user_id" value="<?= $profile_id ?>">
        <button class="btn btn-outline-danger">Shortlist</button>
    </form>
<?php endif; ?>

<?php if ($isLoggedIn): ?>
    <a 
        href="download_biodata.php?id=<?= (int)$profile_id ?>" 
        target="_blank"
        class="btn btn-success mt-3"
    >
        🖨 Download Biodata (PDF)
    </a>
<?php else: ?>
    <div class="alert alert-info mt-3">
        🔐 Please <a href="log_in.php">log in</a> to download biodata.
    </div>
<?php endif; ?>


</div>
</div>

<?php include 'includes/footer.php'; ?>
