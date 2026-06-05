<?php
require_once __DIR__ . '/db.php';
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('cart.php');

$user_id = (int)$_SESSION['user_id'];

// Fetch cart items
$res = mysqli_query($conn,
    "SELECT c.quantity, c.size, p.id AS pid, p.price, p.stock
     FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
$rows = [];
$subtotal = 0.0;
while ($row = mysqli_fetch_assoc($res)) {
    $rows[] = $row;
    $subtotal += (float)$row['price'] * (int)$row['quantity'];
}

if (empty($rows)) redirect('cart.php');

$shipping = $subtotal >= 50 ? 0.0 : 5.99;
$total    = $subtotal + $shipping;

$name    = sanitize($_POST['shipping_name']    ?? '');
$email   = sanitize($_POST['shipping_email']   ?? '');
$phone   = sanitize($_POST['shipping_phone']   ?? '');
$city    = sanitize($_POST['shipping_city']    ?? '');
$address = sanitize($_POST['shipping_address'] ?? '');
$zip     = sanitize($_POST['shipping_zip']     ?? '');
$payment = sanitize($_POST['payment_method']   ?? 'cod');
$notes   = sanitize($_POST['notes']            ?? '');

if (!$name || !$email || !$address || !$city) redirect('checkout.php');

// Insert order
$ins = mysqli_prepare($conn,
    "INSERT INTO orders (user_id,subtotal,shipping,total,shipping_name,shipping_email,shipping_address,shipping_city,shipping_zip,shipping_phone,payment_method,notes)
     VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
mysqli_stmt_bind_param($ins, "idddssssssss",
    $user_id, $subtotal, $shipping, $total,
    $name, $email, $address, $city, $zip, $phone, $payment, $notes);

if (!mysqli_stmt_execute($ins)) redirect('checkout.php');

$order_id = (int)mysqli_insert_id($conn);

// Insert order items
foreach ($rows as $item) {
    $pid   = (int)$item['pid'];
    $qty   = (int)$item['quantity'];
    $sz    = sanitize($item['size']);
    $price = (float)$item['price'];
    mysqli_query($conn,
        "INSERT INTO order_items (order_id,product_id,quantity,size,price) VALUES ($order_id,$pid,$qty,'$sz',$price)");
}

// Clear cart
mysqli_query($conn, "DELETE FROM cart WHERE user_id=$user_id");

redirect("order_success.php?order_id=$order_id");
