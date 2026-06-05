<?php
require_once __DIR__ . '/db.php';
$pageTitle = "Men's Fashion – Winter";
$activeNav = 'shop';
require_once 'header.php';

$gender = 'men';
$products = mysqli_query($conn, "SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.gender='$gender' ORDER BY p.featured DESC, p.created_at DESC");
?>

<section class="shop-hero">
  <p class="breadcrumb"><a href="index.php">Home</a> / Men's Fashion</p>
  <h1>Men's Fashion</h1>
  <p>Discover the latest styles in men's clothing and accessories</p>
</section>

<?php include 'shop_layout.php'; ?>

<?php require_once 'footer.php'; ?>
