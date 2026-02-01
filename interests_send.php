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
        u.profile_id, 
        c.status
    FROM connections c
    JOIN users u ON u.id = c.receiver_id
    WHERE c.sender_id = ?
");
$q->bind_param("i", $user_id);
$q->execute();
$res = $q->get_result();
?>

<div class="container my-4">
    <h4>Connection Sent</h4>

    <?php while ($row = $res->fetch_assoc()): ?>
        <div class="card p-3 mb-2">

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= htmlspecialchars($row['full_name']) ?></strong>
                    <span>(<?= htmlspecialchars($row['profile_id']) ?>)</span>
                    <br>
                    <span class="badge bg-info"><?= htmlspecialchars($row['status']) ?></span>
                </div>

                <!-- VIEW PROFILE (FIXED) -->
                <a href="profile_details.php?id=<?= (int)$row['user_id'] ?>"
                   class="btn btn-outline-primary btn-sm">
                    View Profile
                </a>
            </div>

            <?php if ($row['status'] === 'pending'): ?>
                <form method="post" action="cancel_connection.php" class="mt-2">
                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                    <button class="btn btn-warning btn-sm">Cancel Request</button>
                </form>
            <?php endif; ?>

        </div>
    <?php endwhile; ?>
</div>

<?php include 'includes/footer.php'; ?>
