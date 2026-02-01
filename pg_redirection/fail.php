<?php
include '../includes/db_connect.php';

$tran_id = $_POST['tran_id'] ?? '';

$conn->query("
    UPDATE payments SET status='FAILED', updated_at=NOW()
    WHERE tran_id='$tran_id'
");

header("Location: ../payment_failed.php");
?>