<?php
require_once __DIR__ . '/db.php';
require_once 'config.php';

if (!isLoggedIn()) redirect('login.php');

$cart_id = (int)($_POST['cart_id'] ?? $_GET['cart_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

if ($cart_id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE id=? AND user_id=?");
    mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
    mysqli_stmt_execute($stmt);
}

redirect('cart.php');
