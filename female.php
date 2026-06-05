<?php
require_once __DIR__ . '/db.php';
$pageTitle = "Women's Fashion – Winter";
$activeNav = 'shop';
require_once 'header.php';

$gender = 'women';
$products = mysqli_query($conn, "SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.gender='$gender' ORDER BY p.featured DESC, p.created_at DESC");
?>

<section class="shop-hero">
  <p class="breadcrumb"><a href="index.php">Home</a> / Women's Fashion</p>
  <h1>Women's Fashion</h1>
  <p>Explore our curated collection of elegant women's clothing</p>
</section>

<?php include 'shop_layout.php'; ?>

<?php require_once 'footer.php'; ?>
