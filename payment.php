<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}
include 'includes/db_connect.php';
include 'includes/header.php';

/* fetch packages */
$packages = $conn->query("
    SELECT * FROM membership_packages ORDER BY price ASC
");
?>

<style>
.pricing-card {
    border-radius: 15px;
    transition: all 0.3s ease;
}
.pricing-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}
.price {
    font-size: 32px;
    font-weight: bold;
}
.package-badge {
    position: absolute;
    top: -10px;
    right: 15px;
}
</style>

<div class="container my-5">

<div class="text-center mb-5">
    <h2 class="fw-bold">Membership Packages</h2>
    <p class="text-muted">
        Upgrade your membership to connect, chat and view full biodata
    </p>
</div>

<div class="row g-4 justify-content-center">

<?php while ($pkg = $packages->fetch_assoc()): ?>

<div class="col-md-4">
    <div class="card pricing-card position-relative text-center p-4 h-100">

        <?php
        // badge logic (optional)
        if ($pkg['duration_days'] <= 7) {
            echo '<span class="badge bg-secondary package-badge">Basic</span>';
        } elseif ($pkg['duration_days'] <= 30) {
            echo '<span class="badge bg-danger package-badge">Popular</span>';
        } else {
            echo '<span class="badge bg-success package-badge">Premium</span>';
        }
        ?>

        <h4 class="fw-bold mb-3"><?= htmlspecialchars($pkg['name']) ?></h4>

        <p class="price text-danger">৳ <?= number_format($pkg['price']) ?></p>

        <p class="text-muted">
            Valid for <?= $pkg['duration_days'] ?> days
        </p>

        <ul class="list-unstyled text-start my-4">
            <li>✔ Send connection requests</li>
            <li>✔ View full biodata</li>
            <li>✔ Contact details</li>
            <?php if ($pkg['duration_days'] > 30): ?>
                <li>✔ Priority support</li>
            <?php else: ?>
                <li>✖ Priority support</li>
            <?php endif; ?>
        </ul>

        <!-- IMPORTANT PART -->
        <form method="get" action="payment_form.php">
            <input type="hidden" name="package_id" value="<?= $pkg['id'] ?>">
            <button class="btn btn-danger w-100 fw-bold">
                Buy Now
            </button>
        </form>

    </div>
</div>

<?php endwhile; ?>

</div>

<div class="text-center mt-5 text-muted">
    <p>🔒 Secure payment • 💍 Verified profiles • 📞 24/7 Support</p>
</div>

</div>

<?php include 'includes/footer.php'; ?>
