<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

/* ---------------- LOGIN STATUS ---------------- */
$isLoggedIn = isset($_SESSION['user_id']);

/* ---------------- BLOCKED USER CHECK ---------------- */
if ($isLoggedIn) {
    $user_id = (int)$_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT account_status FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($account_status);
    $stmt->fetch();
    $stmt->close();

    if ($account_status === 'blocked') {
        echo "
        <div class='container my-5'>
            <div class='alert alert-danger text-center'>
                <h5>🚫 Your account is blocked</h5>
                <p>You are not allowed to search profiles.</p>
            </div>
        </div>";
        include 'includes/footer.php';
        exit();
    }
}

/* ---------------- SEARCH FILTERS ---------------- */
$looking_for = $_GET['looking_for'] ?? '';
$religion    = $_GET['religion'] ?? '';
$location    = $_GET['location'] ?? '';
$marital_status = $_GET['marital_status'] ?? '';

$sql = "
SELECT 
    u.*,
    p.*,
    a.*
FROM users u
JOIN user_personal_info p ON p.user_id = u.id
JOIN user_addresses a ON a.user_id = u.id
WHERE u.status = 'Approved'
";

/* ---------------- APPLY FILTERS ---------------- */
if ($looking_for === 'Bride') $sql .= " AND gender = 'Female'";
if ($looking_for === 'Groom') $sql .= " AND gender = 'Male'";
if ($religion) $sql .= " AND religion = '".$conn->real_escape_string($religion)."'";
if($marital_status) $sql .= " AND marital_status = '".$conn->real_escape_string($marital_status)."'";
if ($location) $sql .= " AND division = '".$conn->real_escape_string($location)."'";

$result = $conn->query($sql);
?>

<div class="container my-5">
<h3>Search Results</h3>

<?php if(isset($_GET['shortlist']) && $_GET['shortlist'] === 'success'): ?>
<script>
    alert("✅ Profile shortlisted successfully!");
</script>
<?php endif; ?>

<div class="row g-4">

<?php while($row = $result->fetch_assoc()): ?>

<?php
$imgPath = ($row['gender'] === 'Female') ? 'assets/bride.png' : 'assets/groom.png';
?>

<div class="col-md-3">
<div class="card text-center shadow-sm">
<img src="<?= $imgPath ?>" style="height:220px;object-fit:cover">

<div class="card-body">
<h6><?= htmlspecialchars($row['full_name']) ?></h6>
<p>Age: <?= (int)$row['age'] ?></p>
<p><?= htmlspecialchars($row['division']) ?></p>

<a href="profile_details.php?id=<?= (int)$row['id'] ?>" class="btn btn-danger btn-sm mb-1">
View Profile
</a>

<?php if($isLoggedIn): ?>
<form method="post" action="shortlist.php">
<input type="hidden" name="shortlisted_user_id" value="<?= (int)$row['id'] ?>">
<button type="submit" class="btn btn-outline-danger btn-sm">
❤️ Shortlist
</button>
</form>
<?php endif; ?>

</div>
</div>
</div>

<?php endwhile; ?>

</div>
</div>

<?php include 'includes/footer.php'; ?>
