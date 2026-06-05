<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Shopping Cart – Winter Fashion';
$activeNav = 'pages';
require_once 'header.php';

if (!isLoggedIn()) {
    redirect('login.php?redirect=cart.php');
}

$user_id = (int)$_SESSION['user_id'];
$items = mysqli_query($conn,
    "SELECT c.id AS cart_id, c.quantity, c.size, p.id AS pid, p.name, p.price, p.image, p.stock
     FROM cart c
     JOIN products p ON c.product_id = p.id
     WHERE c.user_id = $user_id
     ORDER BY c.created_at DESC"
);

$subtotal = 0;
$rows = [];
while ($row = mysqli_fetch_assoc($items)) {
    $subtotal += $row['price'] * $row['quantity'];
    $rows[] = $row;
}
$shipping = $subtotal >= 50 ? 0 : 5.99;
$total = $subtotal + $shipping;
?>

<div class="page-hero-sm">
  <p class="breadcrumb"><a href="index.php">Home</a> / Shopping Cart</p>
  <h1>Shopping Cart</h1>
</div>

<div class="cart-layout">

  <?php if (empty($rows)): ?>
  <div class="cart-empty">
    <div class="cart-empty-icon">🛒</div>
    <h2>Your cart is empty</h2>
    <p>Looks like you haven't added anything yet.</p>
    <a href="index.php" class="btn-primary" style="display:inline-block;margin-top:20px">Continue Shopping</a>
  </div>

  <?php else: ?>
  <!-- Cart Items -->
  <div class="cart-items">
    <div class="cart-header-row">
      <span>Product</span><span>Price</span><span>Quantity</span><span>Total</span><span></span>
    </div>

    <?php foreach ($rows as $item): ?>
    <div class="cart-row" id="cart-row-<?= $item['cart_id'] ?>">
      <div class="cart-product-info">
        <div class="cart-img">
          <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"
               onerror="this.src='hero.png'" style="width:100%;height:100%;object-fit:contain;padding:6px">
        </div>
        <div>
          <a href="product.php?id=<?= $item['pid'] ?>" class="cart-name"><?= htmlspecialchars($item['name']) ?></a>
          <p class="cart-size">Size: <strong><?= htmlspecialchars($item['size']) ?></strong></p>
        </div>
      </div>
      <div class="cart-price"><?= formatPrice((float)$item['price']) ?></div>
      <div class="cart-qty-wrap">
        <form method="POST" action="update_cart.php" style="display:flex;align-items:center;gap:0">
          <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
          <button type="button" class="qty-btn" onclick="stepQty(this,-1)">−</button>
          <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>"
                 class="qty-input" onchange="this.form.submit()">
          <button type="button" class="qty-btn" onclick="stepQty(this,1)">+</button>
        </form>
      </div>
      <div class="cart-line-total"><?= formatPrice((float)$item['price'] * $item['quantity']) ?></div>
      <div>
        <form method="POST" action="remove_from_cart.php">
          <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
          <button type="submit" class="cart-remove" title="Remove">✕</button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Order Summary -->
  <div class="cart-summary">
    <h3>Order Summary</h3>
    <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
    <div class="summary-row">
      <span>Shipping</span>
      <span><?= $shipping == 0 ? '<span style="color:#16a34a">FREE</span>' : formatPrice($shipping) ?></span>
    </div>
    <?php if ($shipping > 0): ?>
      <p class="free-ship-note">Add <?= formatPrice(50 - $subtotal) ?> more for free shipping!</p>
    <?php endif; ?>
    <div class="summary-row summary-total"><span>Total</span><span><?= formatPrice($total) ?></span></div>
    <a href="checkout.php" class="btn-primary" style="display:block;text-align:center;width:100%;margin-top:20px;border-radius:10px;padding:16px">
      Proceed to Checkout →
    </a>
    <a href="<?= isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : 'index.php' ?>"
       style="display:block;text-align:center;color:#1a6fe8;font-size:.9rem;margin-top:16px;font-weight:600">
      ← Continue Shopping
    </a>
    <div class="cart-trust">
      <span>🔒 Secure checkout</span>
      <span>🚚 Free shipping $50+</span>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>

<style>
.page-hero-sm{background:var(--light);padding:48px 24px;text-align:center}
.page-hero-sm h1{font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:900;margin-bottom:8px}
.page-hero-sm .breadcrumb{font-size:.85rem;color:#6b7280;margin-bottom:12px}
.page-hero-sm a{color:#1a6fe8}
.cart-layout{max-width:1280px;margin:48px auto 80px;padding:0 24px;display:grid;grid-template-columns:1fr 360px;gap:36px;align-items:start}
.cart-empty{grid-column:1/-1;text-align:center;padding:80px 24px}
.cart-empty-icon{font-size:5rem;margin-bottom:16px}
.cart-empty h2{font-family:'Playfair Display',serif;font-size:2rem;margin-bottom:8px}
.cart-empty p{color:#6b7280}
.cart-items{background:#fff;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden}
.cart-header-row{display:grid;grid-template-columns:3fr 1fr 1.5fr 1fr 40px;gap:16px;
  padding:14px 20px;background:#f9fafb;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#6b7280}
.cart-row{display:grid;grid-template-columns:3fr 1fr 1.5fr 1fr 40px;gap:16px;align-items:center;
  padding:20px;border-bottom:1px solid #f3f4f6}
.cart-row:last-child{border-bottom:none}
.cart-product-info{display:flex;align-items:center;gap:16px}
.cart-img{width:80px;height:80px;background:#f8faff;border-radius:10px;flex-shrink:0;border:1px solid #e5e7eb}
.cart-name{font-weight:600;font-size:.95rem;color:#111;display:block;margin-bottom:4px}
.cart-name:hover{color:#1a6fe8}
.cart-size{font-size:.8rem;color:#6b7280}
.cart-price{font-weight:600;color:#1a6fe8}
.cart-qty-wrap{display:flex}
.qty-input{width:48px;border:1.5px solid #e5e7eb;text-align:center;font-size:.9rem;font-weight:600;
  padding:6px 0;outline:none;border-left:none;border-right:none}
.cart-line-total{font-weight:700;color:#111}
.cart-remove{background:none;border:none;cursor:pointer;color:#9ca3af;font-size:1rem;padding:6px;border-radius:6px;transition:all .2s}
.cart-remove:hover{background:#fef2f2;color:#dc2626}
.cart-summary{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:28px;position:sticky;top:80px}
.cart-summary h3{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #e5e7eb}
.summary-row{display:flex;justify-content:space-between;margin-bottom:14px;font-size:.95rem;color:#555}
.summary-total{font-size:1.1rem;font-weight:700;color:#111;padding-top:14px;border-top:1px solid #e5e7eb;margin-top:8px}
.free-ship-note{font-size:.78rem;color:#1a6fe8;background:#eff6ff;padding:8px 12px;border-radius:6px;margin-bottom:12px}
.cart-trust{display:flex;gap:12px;flex-wrap:wrap;margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6}
.cart-trust span{font-size:.78rem;color:#6b7280}
@media(max-width:900px){.cart-layout{grid-template-columns:1fr}.cart-header-row,.cart-row{grid-template-columns:2fr 1fr 1fr 80px}}
@media(max-width:600px){.cart-header-row{display:none}.cart-row{grid-template-columns:1fr;gap:8px;padding:16px}}
</style>

<script>
function stepQty(btn, delta) {
  const form = btn.closest('form');
  const input = form.querySelector('input[name="quantity"]');
  const val = parseInt(input.value) + delta;
  const max = parseInt(input.max) || 99;
  if (val >= 1 && val <= max) { input.value = val; form.submit(); }
}
</script>
