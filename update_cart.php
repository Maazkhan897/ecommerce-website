<?php
require_once __DIR__ . '/db.php';
require_once 'config.php';

if (!isLoggedIn()) redirect('login.php');

$cart_id  = (int)($_POST['cart_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));
$user_id  = (int)$_SESSION['user_id'];

if ($cart_id > 0) {
    $stmt = mysqli_prepare($conn, "UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
    mysqli_stmt_bind_param($stmt, "iii", $quantity, $cart_id, $user_id);
    mysqli_stmt_execute($stmt);
}

redirect('cart.php');
