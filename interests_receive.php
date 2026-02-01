<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

$user_id = $_SESSION['user_id'];

$q = $conn->prepare("
    SELECT 
        c.id,
        u.id AS user_id,
        u.full_name,
        u.profile_id
    FROM connections c
    JOIN users u ON u.id = c.sender_id
    WHERE c.receiver_id = ?
      AND c.status = 'pending'
");
$q->bind_param("i", $user_id);
$q->execute();
$res = $q->get_result();
?>

<div class="container my-4">
<h4>Connection Requests</h4>

<?php while ($row = $res->fetch_assoc()): ?>
<div class="card p-3 mb-2">

    <div class="d-flex justify-content-between align-items-center">
        <div>
            <strong><?= htmlspecialchars($row['full_name']) ?></strong>
            <span>(<?= htmlspecialchars($row['profile_id']) ?>)</span>
        </div>

        <!-- VIEW PROFILE (ADDED) -->
        <a href="profile_details.php?id=<?= (int)$row['user_id'] ?>"
           class="btn btn-outline-primary btn-sm">
            View Profile
        </a>
    </div>

    <div class="mt-2">
        <form method="post" action="connection_action.php" style="display:inline">
            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
            <input type="hidden" name="action" value="accept">
            <button class="btn btn-success btn-sm">Accept</button>
        </form>

        <form method="post" action="connection_action.php" style="display:inline">
            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
            <input type="hidden" name="action" value="reject">
            <button class="btn btn-danger btn-sm">Reject</button>
        </form>
    </div>

</div>
<?php endwhile; ?>
</div>

<?php include 'includes/footer.php'; ?>
