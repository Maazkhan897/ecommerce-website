<?php
require_once __DIR__ . '/db.php';
require_once 'config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart.', 'redirect' => 'login.php']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

$product_id = (int)($_POST['product_id'] ?? 0);
$quantity   = max(1, (int)($_POST['quantity'] ?? 1));
$size       = sanitize($_POST['size'] ?? 'M');
$user_id    = (int)$_SESSION['user_id'];

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit();
}

// Verify product exists and has stock
$stmt = mysqli_prepare($conn, "SELECT id, name, stock FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$row = mysqli_fetch_assoc($res)) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit();
}

if ($row['stock'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock available.']);
    exit();
}

// Upsert into cart
$check = mysqli_prepare($conn, "SELECT id, quantity FROM cart WHERE user_id=? AND product_id=? AND size=?");
mysqli_stmt_bind_param($check, "iis", $user_id, $product_id, $size);
mysqli_stmt_execute($check);
$checkRes = mysqli_stmt_get_result($check);

if ($existing = mysqli_fetch_assoc($checkRes)) {
    $newQty = $existing['quantity'] + $quantity;
    $upd = mysqli_prepare($conn, "UPDATE cart SET quantity=? WHERE id=?");
    mysqli_stmt_bind_param($upd, "ii", $newQty, $existing['id']);
    mysqli_stmt_execute($upd);
} else {
    $ins = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, quantity, size) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($ins, "iiis", $user_id, $product_id, $quantity, $size);
    mysqli_stmt_execute($ins);
}

echo json_encode([
    'success'    => true,
    'message'    => htmlspecialchars($row['name']) . ' added to cart!',
    'cart_count' => getCartCount(),
]);
