<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        u.id,
        u.full_name,
        u.profile_id,
        p.profile_photo,
        c.created_at
    FROM connections c
    JOIN users u 
        ON (
            (c.sender_id = ? AND u.id = c.receiver_id)
            OR
            (c.receiver_id = ? AND u.id = c.sender_id)
        )
    LEFT JOIN user_personal_info p ON p.user_id = u.id
    WHERE c.status = 'accepted'
    ORDER BY c.created_at DESC
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
<h4 class="text-center fw-bold mb-4">Connected Profiles</h4>

<?php if ($result->num_rows > 0): ?>
<div class="row justify-content-center">

<?php while ($row = $result->fetch_assoc()): ?>
<?php
$img = (!empty($row['profile_photo']) && file_exists("uploads/".$row['profile_photo']))
    ? "uploads/".$row['profile_photo']
    : "assets/profile.png";
?>

<div class="col-md-4 col-lg-3 mb-4">
<div class="card text-center p-4 connected-card">

<img src="<?= $img ?>" class="profile-pic rounded-circle mb-3 mx-auto d-block">

<h6 class="fw-bold"><?= htmlspecialchars($row['full_name']) ?></h6>
<small><?= htmlspecialchars($row['profile_id']) ?></small>

<p class="text-muted mt-2">
Connected on <?= date("d M Y", strtotime($row['created_at'])) ?>
</p>

<a href="profile_details.php?id=<?= (int)$row['id'] ?>" 
   class="btn btn-outline-primary btn-sm mb-2">View Profile</a>

<form method="post" action="disconnect.php" class="d-inline">
<input type="hidden" name="user_id" value="<?= (int)$row['id'] ?>">
<button class="btn btn-outline-warning btn-sm">Disconnect</button>
</form>

<form method="post" action="blocked_user.php" class="d-inline">
<input type="hidden" name="user_id" value="<?= (int)$row['id'] ?>">
<button class="btn btn-outline-danger btn-sm">Block</button>
</form>

</div>
</div>
<?php endwhile; ?>

</div>
<?php else: ?>
<div class="alert alert-info text-center">No connected profiles.</div>
<?php endif; ?>
</div>

<style>
.connected-card {
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
.profile-pic {
    width: 90px;
    height: 90px;
    object-fit: cover;
}
</style>

<?php include 'includes/footer.php'; ?>
