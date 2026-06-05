<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Shoes Collection – Winter';
$activeNav = 'shop';
require_once 'header.php';

$gender = 'shoes';
$products = mysqli_query($conn, "SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.gender='$gender' ORDER BY p.featured DESC, p.created_at DESC");
?>

<section class="shop-hero">
  <p class="breadcrumb"><a href="index.php">Home</a> / Shoes</p>
  <h1>Shoes Collection</h1>
  <p>Step up your style with our premium footwear collection</p>
</section>

<?php include 'shop_layout.php'; ?>

<?php require_once 'footer.php'; ?>
