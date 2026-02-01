<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

/* ===============================
   LOGIN CHECK
================================ */
if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* ===============================
   HANDLE REMOVE ACTION
================================ */
if (isset($_POST['remove_id'])) {
    $remove_id = (int)$_POST['remove_id'];

    $del = $conn->prepare("
        DELETE FROM shortlists
        WHERE user_id = ?
          AND shortlisted_user_id = ?
    ");
    $del->bind_param("ii", $user_id, $remove_id);
    $del->execute();

    header("Location: dashboard_shortlist.php");
    exit();
}

/* ===============================
   COUNT SHORTLIST
================================ */
$countStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM shortlists
    WHERE user_id = ?
");
$countStmt->bind_param("i", $user_id);
$countStmt->execute();
$total = $countStmt->get_result()->fetch_assoc()['total'];

/* ===============================
   FETCH SHORTLIST DATA
================================ */
$sql = "
SELECT 
    u.id,
    u.full_name,
    p.age,
    a.division
FROM shortlists s
JOIN users u ON u.id = s.shortlisted_user_id
LEFT JOIN user_personal_info p ON p.user_id = u.id
LEFT JOIN user_addresses a ON a.user_id = u.id
WHERE s.user_id = ?
ORDER BY s.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">

<h3 class="mb-4">❤️ My Shortlisted Profiles</h3>

<div class="mb-3 fw-bold text-secondary">
    Total Shortlisted Profiles: <?= $total ?>
</div>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">

<thead class="table-light text-center">
<tr>
    <th style="width:5%">#</th>
    <th>Name</th>
    <th style="width:15%">Age</th>
    <th style="width:20%">Location</th>
    <th style="width:20%">Action</th>
</tr>
</thead>

<tbody class="text-center">

<?php if ($result->num_rows == 0): ?>
<tr>
    <td colspan="5" class="text-muted">No shortlisted profiles found.</td>
</tr>
<?php endif; ?>

<?php 
$sl = 1;
while ($row = $result->fetch_assoc()):
?>
<tr>
    <td><?= $sl++ ?></td>
    <td><?= htmlspecialchars($row['full_name']) ?></td>
    <td><?= $row['age'] ?? 'N/A' ?></td>
    <td><?= htmlspecialchars($row['division'] ?? 'N/A') ?></td>
    <td>
        <a href="profile_details.php?id=<?= $row['id'] ?>" 
           class="btn btn-primary btn-sm">
           View
        </a>

        <!-- REMOVE BUTTON -->
        <form method="post" style="display:inline"
              onsubmit="return confirm('Remove from shortlist?');">
            <input type="hidden" name="remove_id" value="<?= $row['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm">
                Remove
            </button>
        </form>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

</div>

<?php include 'includes/footer.php'; ?>
