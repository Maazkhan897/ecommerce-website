<?php
require_once 'auth.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: orders.php'); exit(); }

$order_id = (int)($_POST['order_id'] ?? 0);
$status   = sanitize($_POST['status'] ?? '');
$allowed  = ['pending','processing','shipped','delivered','cancelled'];

if ($order_id > 0 && in_array($status, $allowed)) {
    $stmt = mysqli_prepare($conn, "UPDATE orders SET status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    mysqli_stmt_execute($stmt);
}

header("Location: order_detail.php?id=$order_id&updated=1");
exit();
