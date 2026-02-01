<?php
session_start();

/* ---------- LOCAL DEBUG (TURN OFF IN PRODUCTION) ---------- */
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

include 'includes/db_connect.php';
require_once 'lib/SslCommerzNotification.php';

use SslCommerz\SslCommerzNotification;

/* ---------- BASIC CHECK ---------- */
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'fail', 'message' => 'User not logged in']);
    exit;
}

if (!isset($_POST['package_id'])) {
    echo json_encode(['status' => 'fail', 'message' => 'Package not selected']);
    exit;
}

$user_id    = (int) $_SESSION['user_id'];
$package_id = (int) $_POST['package_id'];

/* =========================================================
   FETCH USER (MATCHES YOUR TABLE)
   ========================================================= */
$stmt = $conn->prepare("
    SELECT full_name, email, contact
    FROM users
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(['status' => 'fail', 'message' => 'User not found']);
    exit;
}

/* =========================================================
   FETCH PACKAGE
   ========================================================= */
$stmt = $conn->prepare("
    SELECT id, name, price, duration_days
    FROM membership_packages
    WHERE id = ?
");
$stmt->bind_param("i", $package_id);
$stmt->execute();
$pkg = $stmt->get_result()->fetch_assoc();

if (!$pkg) {
    echo json_encode(['status' => 'fail', 'message' => 'Invalid package']);
    exit;
}

/* =========================================================
   CREATE TRANSACTION
   ========================================================= */
$tran_id = 'MTRX' . time() . rand(100, 999);

/* =========================================================
   INSERT PAYMENT AS PENDING
   ========================================================= */
$stmt = $conn->prepare("
    INSERT INTO payments (user_id, package_id, tran_id, amount, status)
    VALUES (?, ?, ?, ?, 'PENDING')
");
$stmt->bind_param("iisd", $user_id, $package_id, $tran_id, $pkg['price']);
$stmt->execute();

/* =========================================================
   SSL COMMERZ INIT
   ========================================================= */
$ssl = new SslCommerzNotification();

$postData = [
    'total_amount'     => $pkg['price'],
    'currency'         => 'BDT',
    'tran_id'          => $tran_id,
    'product_category' => 'Membership',

    /* ---------- CUSTOMER INFO ---------- */
    'cus_name'     => $user['full_name'],
    'cus_email'    => $user['email'],
    'cus_add1'     => 'Dhaka',
    'cus_add2'     => '',
    'cus_city'     => 'Dhaka',
    'cus_postcode' => '1207',
    'cus_country'  => 'Bangladesh',
    'cus_phone'    => $user['contact'] ?: '01700000000',

    /* ---------- SHIPPING (NOT USED) ---------- */
    'shipping_method' => 'NO',
    'num_of_item'     => 1,
    'ship_name'       => 'N/A',
    'ship_add1'       => 'N/A',
    'ship_city'       => 'N/A',

    /* ---------- PRODUCT INFO ---------- */
    'product_name'    => $pkg['name'],
    'product_profile' => 'non-physical-goods',

    /* ---------- EXTRA META ---------- */
    'value_a' => $user_id,
    'value_b' => $package_id
];

/* =========================================================
   RETURN SSL GATEWAY URL
   ========================================================= */
echo $ssl->makePayment($postData);
exit;
?>