<?php
require_once 'auth.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: products.php'); exit(); }

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}
header('Location: products.php?deleted=1');
exit();
