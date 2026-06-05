<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Checkout – Winter Fashion';
$activeNav = 'pages';
require_once 'header.php';

if (!isLoggedIn()) redirect('login.php?redirect=checkout.php');

$user_id = (int)$_SESSION['user_id'];

// Fetch user
$uRes = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user = mysqli_fetch_assoc($uRes);

// Fetch cart
$items = mysqli_query($conn,
    "SELECT c.id AS cart_id, c.quantity, c.size, p.id AS pid, p.name, p.price, p.image, p.stock
     FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
$rows = [];
$subtotal = 0;
while ($row = mysqli_fetch_assoc($items)) { $rows[] = $row; $subtotal += $row['price']*$row['quantity']; }

if (empty($rows)) redirect('cart.php');

$shipping = $subtotal >= 50 ? 0 : 5.99;
$total = $subtotal + $shipping;
?>

<div class="page-hero-sm">
  <p class="breadcrumb"><a href="index.php">Home</a> / <a href="cart.php">Cart</a> / Checkout</p>
  <h1>Checkout</h1>
</div>

<div class="checkout-layout">

  <!-- Shipping Form -->
  <div class="checkout-form-wrap">
    <form method="POST" action="place_order.php" id="checkoutForm">
      <div class="checkout-section">
        <h3>Shipping Information</h3>
        <div class="form-row-2">
          <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="shipping_name" value="<?= htmlspecialchars($user['name']) ?>" required>
          </div>
          <div class="form-group">
            <label>Email Address *</label>
            <input type="email" name="shipping_email" value="<?= htmlspecialchars($user['email']) ?>" required>
          </div>
        </div>
        <div class="form-row-2">
          <div class="form-group">
            <label>Phone Number *</label>
            <input type="tel" name="shipping_phone" placeholder="+1 234 567 8900" required>
          </div>
          <div class="form-group">
            <label>City *</label>
            <input type="text" name="shipping_city" placeholder="New York" required>
          </div>
        </div>
        <div class="form-group">
          <label>Street Address *</label>
          <input type="text" name="shipping_address" placeholder="123 Main St, Apt 4B" required>
        </div>
        <div class="form-row-2">
          <div class="form-group">
            <label>ZIP / Postal Code</label>
            <input type="text" name="shipping_zip" placeholder="10001">
          </div>
          <div class="form-group">
            <label>Order Notes (optional)</label>
            <input type="text" name="notes" placeholder="Special instructions...">
          </div>
        </div>
      </div>

      <div class="checkout-section">
        <h3>Payment Method</h3>
        <div class="payment-options">
          <label class="payment-opt active-payment" id="pay-cod">
            <input type="radio" name="payment_method" value="cod" checked onchange="setPayment(this)">
            <span class="pay-icon">💵</span>
            <div><strong>Cash on Delivery</strong><p>Pay when you receive your order</p></div>
          </label>
          <label class="payment-opt" id="pay-card">
            <input type="radio" name="payment_method" value="card" onchange="setPayment(this)">
            <span class="pay-icon">💳</span>
            <div><strong>Credit / Debit Card</strong><p>Visa, Mastercard, Amex</p></div>
          </label>
        </div>
        <div id="card-fields" style="display:none;margin-top:20px">
          <div class="form-group">
            <label>Card Number</label>
            <input type="text" placeholder="1234 5678 9012 3456" maxlength="19">
          </div>
          <div class="form-row-2">
            <div class="form-group">
              <label>Expiry Date</label>
              <input type="text" placeholder="MM / YY" maxlength="7">
            </div>
            <div class="form-group">
              <label>CVV</label>
              <input type="text" placeholder="123" maxlength="4">
            </div>
          </div>
        </div>
      </div>

      <button type="submit" class="btn-primary" style="width:100%;padding:18px;font-size:1.05rem;border-radius:12px;margin-top:8px">
        🔒 Place Order — <?= formatPrice($total) ?>
      </button>
    </form>
  </div>

  <!-- Order Summary -->
  <div class="checkout-summary">
    <h3>Your Order</h3>
    <?php foreach ($rows as $item): ?>
    <div class="co-item">
      <div class="co-img">
        <img src="<?= htmlspecialchars($item['image']) ?>" alt="" onerror="this.src='hero.png'"
             style="width:100%;height:100%;object-fit:contain;padding:4px">
        <span class="co-qty"><?= $item['quantity'] ?></span>
      </div>
      <div class="co-info">
        <p class="co-name"><?= htmlspecialchars($item['name']) ?></p>
        <p class="co-meta">Size: <?= htmlspecialchars($item['size']) ?></p>
      </div>
      <p class="co-price"><?= formatPrice($item['price'] * $item['quantity']) ?></p>
    </div>
    <?php endforeach; ?>

    <div class="co-divider"></div>
    <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
    <div class="summary-row">
      <span>Shipping</span>
      <span><?= $shipping==0 ? '<span style="color:#16a34a">FREE</span>' : formatPrice($shipping) ?></span>
    </div>
    <div class="summary-row summary-total"><span>Total</span><span><?= formatPrice($total) ?></span></div>

    <div class="co-trust">
      <div class="co-trust-item"><span>🔒</span><p>SSL Encrypted & Secure</p></div>
      <div class="co-trust-item"><span>🔄</span><p>30-Day Easy Returns</p></div>
    </div>
  </div>

</div>

<?php require_once 'footer.php'; ?>

<style>
.page-hero-sm{background:var(--light);padding:48px 24px;text-align:center}
.page-hero-sm h1{font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:900;margin-bottom:8px}
.page-hero-sm .breadcrumb{font-size:.85rem;color:#6b7280;margin-bottom:12px}
.page-hero-sm a{color:#1a6fe8}
.checkout-layout{max-width:1280px;margin:48px auto 80px;padding:0 24px;display:grid;grid-template-columns:1fr 400px;gap:40px;align-items:start}
.checkout-section{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:28px;margin-bottom:20px}
.checkout-section h3{font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;margin-bottom:24px;color:#111}
.form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-group{margin-bottom:18px}
.form-group label{display:block;font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#555;margin-bottom:7px}
.form-group input{width:100%;padding:12px 16px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.9rem;font-family:inherit;outline:none;transition:border-color .2s}
.form-group input:focus{border-color:#1a6fe8;box-shadow:0 0 0 3px rgba(26,111,232,.08)}
.payment-options{display:flex;flex-direction:column;gap:12px}
.payment-opt{display:flex;align-items:center;gap:16px;padding:16px 20px;border:1.5px solid #e5e7eb;border-radius:12px;cursor:pointer;transition:all .2s}
.payment-opt:hover{border-color:#1a6fe8}
.payment-opt.active-payment{border-color:#1a6fe8;background:#eff6ff}
.payment-opt input{width:18px;height:18px;accent-color:#1a6fe8}
.pay-icon{font-size:1.6rem}
.payment-opt strong{display:block;font-size:.95rem;color:#111}
.payment-opt p{font-size:.8rem;color:#6b7280;margin-top:2px}
.checkout-summary{background:#fff;border-radius:16px;border:1px solid #e5e7eb;padding:28px;position:sticky;top:80px}
.checkout-summary h3{font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid #e5e7eb}
.co-item{display:flex;align-items:center;gap:14px;margin-bottom:16px}
.co-img{width:64px;height:64px;background:#f8faff;border-radius:8px;flex-shrink:0;position:relative;border:1px solid #e5e7eb}
.co-qty{position:absolute;top:-8px;right:-8px;background:#1a6fe8;color:#fff;width:20px;height:20px;border-radius:50%;font-size:.7rem;font-weight:700;display:flex;align-items:center;justify-content:center}
.co-name{font-size:.9rem;font-weight:600;color:#111;margin-bottom:3px}
.co-meta{font-size:.78rem;color:#6b7280}
.co-price{margin-left:auto;font-weight:700;font-size:.95rem;color:#111;white-space:nowrap}
.co-divider{height:1px;background:#e5e7eb;margin:16px 0}
.summary-row{display:flex;justify-content:space-between;margin-bottom:12px;font-size:.9rem;color:#555}
.summary-total{font-size:1.05rem;font-weight:700;color:#111;padding-top:12px;border-top:1px solid #e5e7eb;margin-top:8px}
.co-trust{display:flex;gap:16px;margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6}
.co-trust-item{display:flex;align-items:center;gap:8px;font-size:.78rem;color:#6b7280}
.co-trust-item span{font-size:1.2rem}
@media(max-width:900px){.checkout-layout{grid-template-columns:1fr}.form-row-2{grid-template-columns:1fr}}
</style>

<script>
function setPayment(radio) {
  document.querySelectorAll('.payment-opt').forEach(el => el.classList.remove('active-payment'));
  radio.closest('.payment-opt').classList.add('active-payment');
  document.getElementById('card-fields').style.display = radio.value === 'card' ? 'block' : 'none';
}
</script>
