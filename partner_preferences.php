<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* ================= SAVE PREFERENCES ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $min_age = $_POST['min_age'] ?: null;
    $max_age = $_POST['max_age'] ?: null;
    $study_medium = $_POST['study_medium'] ?: null;
    $graduation_subject = $_POST['graduation_subject'] ?: null;
    $division = $_POST['division'] ?: null;
    $marital_status = $_POST['marital_status'] ?: null;
    $min_family = $_POST['min_family'] ?: null;
    $max_family = $_POST['max_family'] ?: null;

    /* ================= VALIDATION ================= */
    $errors = [];

    /* Age validation */
    if ($min_age !== null && (!is_numeric($min_age) || $min_age < 18 || $min_age > 80)) {
        $errors[] = "Min age must be between 18 and 80";
    }

    if ($max_age !== null && (!is_numeric($max_age) || $max_age < 18 || $max_age > 80)) {
        $errors[] = "Max age must be between 18 and 80";
    }

    if ($min_age !== null && $max_age !== null && $min_age > $max_age) {
        $errors[] = "Min age cannot be greater than max age";
    }

    /* Text length validation */
    if ($study_medium !== null && strlen($study_medium) > 50) {
        $errors[] = "Study medium too long";
    }

    if ($graduation_subject !== null && strlen($graduation_subject) > 100) {
        $errors[] = "Graduation subject too long";
    }

    if ($division !== null && strlen($division) > 50) {
        $errors[] = "Division name too long";
    }

    /* Marital status validation */
    $allowed_status = ['Single','Married','Separated','Divorced','Widowed'];
    if ($marital_status !== null && !in_array($marital_status, $allowed_status)) {
        $errors[] = "Invalid marital status";
    }

    /* Family members validation */
    if ($min_family !== null && (!is_numeric($min_family) || $min_family < 1 || $min_family > 50)) {
        $errors[] = "Invalid minimum family members";
    }

    if ($max_family !== null && (!is_numeric($max_family) || $max_family < 1 || $max_family > 50)) {
        $errors[] = "Invalid maximum family members";
    }

    if ($min_family !== null && $max_family !== null && $min_family > $max_family) {
        $errors[] = "Min family members cannot exceed max family members";
    }

    /* Stop execution if errors */
    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo "<p style='color:red'>$e</p>";
        }
        exit;
    }

    /* INSERT / UPDATE */
    $stmt = $conn->prepare("
        INSERT INTO partner_preferences
        (user_id, min_age, max_age, study_medium, graduation_subject, division, marital_status, min_family_members, max_family_members)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            min_age = VALUES(min_age),
            max_age = VALUES(max_age),
            study_medium = VALUES(study_medium),
            graduation_subject = VALUES(graduation_subject),
            division = VALUES(division),
            marital_status = VALUES(marital_status),
            min_family_members = VALUES(min_family_members),
            max_family_members = VALUES(max_family_members)
    ");

    $stmt->bind_param(
        "iiissssii",
        $user_id,
        $min_age,
        $max_age,
        $study_medium,
        $graduation_subject,
        $division,
        $marital_status,
        $min_family,
        $max_family
    );
    $stmt->execute();
}

/* ================= LOAD SAVED PREFERENCES ================= */
$prefs = $conn->prepare("SELECT * FROM partner_preferences WHERE user_id = ?");
$prefs->bind_param("i", $user_id);
$prefs->execute();
$pref = $prefs->get_result()->fetch_assoc();

/* ================= MATCHING PROFILES ================= */
$sql = "
SELECT 
    u.id,
    u.full_name,
    u.profile_id,
    u.marital_status,
    p.age,
    e.study_medium,
    a.division,
    f.total_members
FROM users u
LEFT JOIN user_personal_info p ON p.user_id = u.id
LEFT JOIN user_education e ON e.user_id = u.id
LEFT JOIN user_addresses a ON a.user_id = u.id
LEFT JOIN user_family f ON f.user_id = u.id
WHERE u.id != ?
AND u.status = 'Approved'
AND u.account_status = 'active'
";

$params = [$user_id];
$types = "i";

/* Apply filters only if set */
if (!empty($pref['min_age'])) {
    $sql .= " AND p.age >= ?";
    $types .= "i";
    $params[] = $pref['min_age'];
}
if (!empty($pref['max_age'])) {
    $sql .= " AND p.age <= ?";
    $types .= "i";
    $params[] = $pref['max_age'];
}
if (!empty($pref['study_medium'])) {
    $sql .= " AND e.study_medium = ?";
    $types .= "s";
    $params[] = $pref['study_medium'];
}
if (!empty($pref['graduation_subject'])) {
    $sql .= " AND e.graduation_subject = ?";
    $types .= "s";
    $params[] = $pref['graduation_subject'];
}
if (!empty($pref['division'])) {
    $sql .= " AND a.division = ?";
    $types .= "s";
    $params[] = $pref['division'];
}
if (!empty($pref['marital_status'])) {
    $sql .= " AND u.marital_status = ?";
    $types .= "s";
    $params[] = $pref['marital_status'];
}
if (!empty($pref['min_family_members'])) {
    $sql .= " AND f.total_members >= ?";
    $types .= "i";
    $params[] = $pref['min_family_members'];
}
if (!empty($pref['max_family_members'])) {
    $sql .= " AND f.total_members <= ?";
    $types .= "i";
    $params[] = $pref['max_family_members'];
}

/* Prevent duplicates + random */
$sql .= " GROUP BY u.id ORDER BY RAND() LIMIT 6";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$matches = $stmt->get_result();
?>

<div class="container my-5">

<h4>Partner Preferences</h4>

<form method="post" class="card p-4 mb-4">
<div class="row">

<div class="col-md-3">
<label>Min Age</label>
<input type="number" name="min_age" class="form-control" value="<?= $pref['min_age'] ?? '' ?>">
</div>

<div class="col-md-3">
<label>Max Age</label>
<input type="number" name="max_age" class="form-control" value="<?= $pref['max_age'] ?? '' ?>">
</div>

<div class="col-md-3">
<label>Study Medium</label>
<input type="text" name="study_medium" class="form-control" value="<?= $pref['study_medium'] ?? '' ?>">
</div>

<div class="col-md-3">
<label>Graduation Subject</label>
<input type="text" name="graduation_subject" class="form-control" value="<?= $pref['graduation_subject'] ?? '' ?>">
</div>

<div class="col-md-4 mt-3">
<label>Division</label>
<input type="text" name="division" class="form-control" value="<?= $pref['division'] ?? '' ?>">
</div>

<div class="col-md-4 mt-3">
<label>Marital Status</label>
<select name="marital_status" class="form-control">
    <option value="">Any</option>
    <?php
    $statuses = ['Single','Married','Separated','Divorced','Widowed'];
    foreach ($statuses as $s):
    ?>
    <option value="<?= $s ?>" <?= ($pref['marital_status'] ?? '') === $s ? 'selected' : '' ?>>
        <?= $s ?>
    </option>
    <?php endforeach; ?>
</select>
</div>

<div class="col-md-4 mt-3">
<label>Min Family Members</label>
<input type="number" name="min_family" class="form-control" value="<?= $pref['min_family_members'] ?? '' ?>">
</div>

<div class="col-md-4 mt-3">
<label>Max Family Members</label>
<input type="number" name="max_family" class="form-control" value="<?= $pref['max_family_members'] ?? '' ?>">
</div>

</div>

<button class="btn btn-danger mt-4">Save Preferences</button>
</form>

<h5>Matching Profiles</h5>

<div class="row">
<?php while ($m = $matches->fetch_assoc()): ?>
<div class="col-md-4 mb-3">
<div class="card p-3">
<strong><?= htmlspecialchars($m['full_name']) ?></strong>
<p><?= htmlspecialchars($m['profile_id']) ?></p>
<p>Age: <?= (int)$m['age'] ?></p>
<p><?= htmlspecialchars($m['study_medium']) ?> | <?= htmlspecialchars($m['division']) ?></p>

<a href="profile_details.php?id=<?= (int)$m['id'] ?>" class="btn btn-outline-primary btn-sm">
View Profile
</a>
</div>
</div>
<?php endwhile; ?>
</div>

</div>

<?php include 'includes/footer.php'; ?>
