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
        b.created_at
    FROM blocked_users b
    JOIN users u ON u.id = b.blocked_id
    WHERE b.blocker_id = ?
    ORDER BY b.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
<h4 class="text-center fw-bold mb-4">Blocked Users</h4>

<?php if ($result->num_rows > 0): ?>
<div class="row justify-content-center">

<?php while ($row = $result->fetch_assoc()): ?>

<div class="col-md-4 col-lg-3 mb-4">
<div class="card text-center p-4">

<!-- ✅ ALWAYS SHOW DEFAULT ICON (NO REAL PHOTO) -->
<img src="assets/profile.png"
     class="profile-pic rounded-circle mb-3 mx-auto d-block"
     width="90"
     height="90"
     alt="Blocked user">

<h6 class="fw-bold"><?= htmlspecialchars($row['full_name']) ?></h6>
<small><?= htmlspecialchars($row['profile_id']) ?></small>

<p class="text-muted mt-2">
Blocked on <?= date("d M Y", strtotime($row['created_at'])) ?>
</p>

<form method="post" action="unblock_user.php">
    <input type="hidden" name="user_id" value="<?= (int)$row['id'] ?>">
    <button class="btn btn-outline-success btn-sm">
        Unblock
    </button>
</form>

<a href="profile_details.php?id=<?= (int)$row['id'] ?>"
   class="btn btn-outline-primary btn-sm">
   View Profile
</a>


</div>
</div>

<?php endwhile; ?>

</div>
<?php else: ?>
<div class="alert alert-info text-center">
    You have not blocked anyone.
</div>
<?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
