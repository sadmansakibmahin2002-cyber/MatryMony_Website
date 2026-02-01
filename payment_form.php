<?php
session_start();

/* ---------- LOGIN CHECK ---------- */
if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

/* ---------- PACKAGE ID CHECK ---------- */
if (!isset($_GET['package_id'])) {
    header("Location: membership.php");
    exit();
}

include 'includes/db_connect.php';
include 'includes/header.php';

/* ---------- FETCH PACKAGE ---------- */
$package_id = (int) $_GET['package_id'];

$stmt = $conn->prepare("SELECT * FROM membership_packages WHERE id=?");
$stmt->bind_param("i", $package_id);
$stmt->execute();
$pkg = $stmt->get_result()->fetch_assoc();

if (!$pkg) {
    die("Invalid membership package");
}
?>

<style>
.payment-card {
    border-radius: 15px;
}
.price {
    font-size: 34px;
    font-weight: bold;
}
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card payment-card shadow-sm p-4">

                <h3 class="fw-bold text-center mb-3">
                    Complete Your Payment
                </h3>

                <p class="text-center text-muted mb-4">
                    Upgrade your membership to unlock premium features
                </p>

                <hr>

                <!-- PACKAGE DETAILS -->
                <div class="mb-4">
                    <h5 class="fw-bold">Selected Package</h5>

                    <p class="mb-1">
                        <strong><?= htmlspecialchars($pkg['name']) ?></strong>
                    </p>

                    <p class="mb-1">
                        Valid for <strong><?= (int)$pkg['duration_days'] ?></strong> days
                    </p>

                    <p class="price text-danger">
                        ৳ <?= number_format($pkg['price']) ?>
                    </p>
                </div>

                <!-- FEATURES -->
                <ul class="list-unstyled mb-4">
                    <li>✔ Unlimited profile views</li>
                    <li>✔ Send connection requests</li>
                    <li>✔ View contact details</li>
                    <li>✔ Priority support</li>
                </ul>

                <!-- PAYMENT BUTTON -->
                <input type="hidden" id="package_id" value="<?= $pkg['id'] ?>">

                <button type="button"
                        id="payNowBtn"
                        class="btn btn-danger w-100 fw-bold py-2">
                    Proceed to Secure Payment
                </button>

                <p class="text-center text-muted mt-3 mb-0">
                    🔒 Secure payment powered by SSLCommerz
                </p>

            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- ================= AJAX CHECKOUT SCRIPT ================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const payBtn = document.getElementById("payNowBtn");

    payBtn.addEventListener("click", function () {

        payBtn.disabled = true;
        payBtn.innerText = "Redirecting to payment...";

        const packageId = document.getElementById("package_id").value;

        fetch("ajax_checkout.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "package_id=" + encodeURIComponent(packageId)
        })
        .then(res => res.text())   // 🔥 IMPORTANT (DEBUG SAFE)
        .then(text => {

            console.log("SERVER RESPONSE:", text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                alert("SERVER ERROR:\n\n" + text);
                payBtn.disabled = false;
                payBtn.innerText = "Proceed to Secure Payment";
                return;
            }

            if (data.status === "success" && data.data) {
                window.location.href = data.data; // SSLCommerz Gateway
            } else {
                alert(data.message || "Payment initialization failed.");
                payBtn.disabled = false;
                payBtn.innerText = "Proceed to Secure Payment";
            }
        })
        .catch(err => {
            console.error(err);
            alert("JS Error. Check console.");
            payBtn.disabled = false;
            payBtn.innerText = "Proceed to Secure Payment";
        });
    });

});
</script>
