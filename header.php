<?php
require_once __DIR__ . '/config.php';
$cartCount = getCartCount();
$pageTitle = $pageTitle ?? 'Winter Fashion';
$activeNav = $activeNav ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- PROMO BAR -->
<div class="promo-bar">
  <div class="promo-track">
    🚚 Free shipping on all orders above $50 &nbsp;&nbsp;|&nbsp;&nbsp;
    🎉 New arrivals every week &nbsp;&nbsp;|&nbsp;&nbsp;
    🔒 Secure &amp; encrypted payments &nbsp;&nbsp;|&nbsp;&nbsp;
    🔄 30-day hassle-free returns &nbsp;&nbsp;|&nbsp;&nbsp;
    🚚 Free shipping on all orders above $50 &nbsp;&nbsp;|&nbsp;&nbsp;
    🎉 New arrivals every week
  </div>
</div>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="nav-container">
    <a href="index.php" class="logo">Winter</a>
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php" <?= $activeNav==='home'?'class="active"':'' ?>>Home</a></li>
      <li class="dropdown">
        <a href="#" <?= $activeNav==='shop'?'class="active"':'' ?>>Shop <span class="arrow">▾</span></a>
        <div class="dropdown-menu">
          <a href="male.php">Shop For Male</a>
          <a href="female.php">Shop For Female</a>
          <a href="shoes.php">Shoes</a>
        </div>
      </li>
      <li class="dropdown">
        <a href="#" <?= $activeNav==='pages'?'class="active"':'' ?>>Pages <span class="arrow">▾</span></a>
        <div class="dropdown-menu">
          <a href="product.php">Product Details</a>
          <?php if (isLoggedIn()): ?>
            <a href="cart.php">Shopping Cart</a>
            <a href="checkout.php">Checkout</a>
          <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
          <?php endif; ?>
        </div>
      </li>
      <li><a href="#">Contact</a></li>
    </ul>
    <div class="nav-icons">
      <a href="cart.php" class="cart-icon">
        🛒<span class="badge" id="cart-badge"><?= $cartCount ?></span>
      </a>
      <?php if (isLoggedIn()): ?>
        <span class="nav-user">Hi, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?></span>
        <a href="logout.php" class="nav-auth-link">Logout</a>
      <?php else: ?>
        <a href="login.php" class="nav-auth-link">Login</a>
      <?php endif; ?>
    </div>
    <button class="hamburger" onclick="toggleMenu()">☰</button>
  </div>
</nav>
