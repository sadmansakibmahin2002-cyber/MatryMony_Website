<?php
session_start();

include '../includes/db_connect.php';
include '../includes/header.php';
require_once '../lib/SslCommerzNotification.php';

use SslCommerz\SslCommerzNotification;

$ssl = new SslCommerzNotification();

/* -----------------------------
   REQUIRED POST DATA FROM SSL
----------------------------- */
$tran_id  = $_POST['tran_id']  ?? '';
$amount   = $_POST['amount']   ?? 0;
$currency = $_POST['currency'] ?? 'BDT';

$paymentSuccess = false;
$paymentData = null;

/* -----------------------------
   VALIDATE PAYMENT
----------------------------- */
if ($tran_id && $ssl->orderValidate($_POST, $tran_id, $amount, $currency)) {

    $payment = $conn->query("
        SELECT * FROM payments
        WHERE tran_id = '$tran_id'
          AND status = 'PENDING'
    ")->fetch_assoc();

    if ($payment) {

        /* FETCH PACKAGE */
        $pkg = $conn->query("
            SELECT duration_days, name
            FROM membership_packages
            WHERE id = {$payment['package_id']}
        ")->fetch_assoc();

        $expiryDate = date('Y-m-d', strtotime("+{$pkg['duration_days']} days"));

        /* UPDATE PAYMENT */
        $conn->query("
            UPDATE payments
            SET status='SUCCESS', updated_at=NOW()
            WHERE tran_id='$tran_id'
        ");

        /* UPDATE USER MEMBERSHIP */
        $conn->query("
            UPDATE users SET
                membership_package_id = {$payment['package_id']},
                membership_expiry     = '$expiryDate',
                membership_status     = 'ACTIVE'
            WHERE id = {$payment['user_id']}
        ");

        $paymentSuccess = true;
        $paymentData = [
            'tran_id' => $tran_id,
            'amount' => $amount,
            'currency' => $currency,
            'package_name' => $pkg['name'],
            'expiry' => $expiryDate
        ];
    }
}
?>

<div class="container my-5">

<?php if ($paymentSuccess): ?>

    <div class="card shadow-sm p-4 text-center">

        <h2 class="text-success fw-bold mb-3">
            ✅ Payment Successful
        </h2>

        <p class="text-muted mb-4">
            Your membership has been activated successfully.
        </p>

        <hr>

        <!-- PAYMENT DETAILS -->
        <div class="text-start mx-auto" style="max-width: 500px;">
            <p><strong>Transaction ID:</strong> <?= htmlspecialchars($paymentData['tran_id']) ?></p>
            <p><strong>Package:</strong> <?= htmlspecialchars($paymentData['package_name']) ?></p>
            <p><strong>Amount Paid:</strong> ৳ <?= number_format($paymentData['amount'], 2) ?></p>
            <p><strong>Membership Valid Until:</strong> <?= date('d M Y', strtotime($paymentData['expiry'])) ?></p>
            <p><strong>Status:</strong> <span class="badge bg-success">ACTIVE</span></p>
        </div>

        <hr>

        <!-- ACTION BUTTONS -->
        <div class="d-flex justify-content-center gap-3 flex-wrap">

            <a href="../download_receipt.php?tran_id=<?= urlencode($paymentData['tran_id']) ?>"
               class="btn btn-outline-primary">
                ⬇ Download Payment Slip
            </a>

            <a href="../my_profile.php" class="btn btn-success">
                Go to My Profile
            </a>

            <a href="../index.php" class="btn btn-secondary">
                Home
            </a>

        </div>

    </div>

<?php else: ?>

    <div class="card shadow-sm p-4 text-center">

        <h2 class="text-danger fw-bold mb-3">
            ❌ Payment Validation Failed
        </h2>

        <p class="text-muted mb-4">
            We could not verify your payment. If money was deducted,
            please contact support with your transaction ID.
        </p>

        <a href="../membership.php" class="btn btn-danger">
            Try Again
        </a>

    </div>

<?php endif; ?>

</div>

<?php include '../includes/footer.php'; ?>
