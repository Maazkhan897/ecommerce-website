<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Order Placed – Winter Fashion';
$activeNav = '';
require_once 'header.php';

if (!isLoggedIn()) redirect('login.php');

$order_id = (int)($_GET['order_id'] ?? 0);
$user_id  = (int)$_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id=? AND user_id=?");
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$order) redirect('index.php');

$orderItems = mysqli_query($conn,
    "SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$order_id");
?>

<div class="success-page">
  <div class="success-card">
    <div class="success-icon">✅</div>
    <h1>Order Placed Successfully!</h1>
    <p class="success-sub">Thank you, <strong><?= htmlspecialchars(explode(' ',$_SESSION['user_name'])[0]) ?></strong>! Your order has been received and is being processed.</p>

    <div class="order-ref">
      <span>Order #</span>
      <strong><?= str_pad($order_id, 6, '0', STR_PAD_LEFT) ?></strong>
    </div>

    <div class="order-meta-grid">
      <div class="order-meta-item">
        <p class="meta-label">Order Date</p>
        <p class="meta-val"><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
      </div>
      <div class="order-meta-item">
        <p class="meta-label">Payment</p>
        <p class="meta-val"><?= $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Credit Card' ?></p>
      </div>
      <div class="order-meta-item">
        <p class="meta-label">Status</p>
        <p class="meta-val status-badge">⏳ <?= ucfirst($order['status']) ?></p>
      </div>
      <div class="order-meta-item">
        <p class="meta-label">Total</p>
        <p class="meta-val" style="color:#1a6fe8;font-size:1.1rem"><?= formatPrice((float)$order['total']) ?></p>
      </div>
    </div>

    <div class="order-items-list">
      <h3>Items Ordered</h3>
      <?php while ($item = mysqli_fetch_assoc($orderItems)): ?>
      <div class="oi-row">
        <div class="oi-img">
          <img src="<?= htmlspecialchars($item['image']) ?>" alt="" onerror="this.src='hero.png'"
               style="width:100%;height:100%;object-fit:contain;padding:4px">
        </div>
        <div class="oi-info">
          <p class="oi-name"><?= htmlspecialchars($item['name']) ?></p>
          <p class="oi-meta">Size: <?= htmlspecialchars($item['size']) ?> &nbsp;·&nbsp; Qty: <?= $item['quantity'] ?></p>
        </div>
        <p class="oi-price"><?= formatPrice($item['price'] * $item['quantity']) ?></p>
      </div>
      <?php endwhile; ?>
    </div>

    <div class="success-addr">
      <h4>Shipping To</h4>
      <p><?= htmlspecialchars($order['shipping_name']) ?></p>
      <p><?= htmlspecialchars($order['shipping_address']) ?>, <?= htmlspecialchars($order['shipping_city']) ?></p>
      <p><?= htmlspecialchars($order['shipping_phone']) ?></p>
    </div>

    <div class="success-actions">
      <a href="index.php" class="btn-primary">Continue Shopping</a>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>

<style>
.success-page{min-height:70vh;display:flex;align-items:center;justify-content:center;padding:60px 24px;background:linear-gradient(135deg,#eff6ff 0%,#f0fdf4 100%)}
.success-card{background:#fff;border-radius:20px;padding:52px 48px;max-width:680px;width:100%;
  box-shadow:0 24px 64px rgba(26,111,232,0.1);text-align:center}
.success-icon{font-size:4rem;margin-bottom:16px;animation:bounceIn .6s ease}
@keyframes bounceIn{0%{transform:scale(0)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
.success-card h1{font-family:'Playfair Display',serif;font-size:2rem;color:#111;margin-bottom:12px}
.success-sub{color:#555;font-size:.95rem;margin-bottom:28px}
.order-ref{background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 24px;
  display:inline-flex;gap:12px;align-items:center;margin-bottom:28px;font-size:.9rem}
.order-ref strong{font-size:1.2rem;color:#1a6fe8;font-family:'Playfair Display',serif}
.order-meta-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:32px;text-align:left}
.order-meta-item{background:#f9fafb;border-radius:10px;padding:14px 16px}
.meta-label{font-size:.75rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px}
.meta-val{font-size:.9rem;font-weight:700;color:#111}
.status-badge{color:#d97706}
.order-items-list{text-align:left;margin-bottom:24px}
.order-items-list h3{font-family:'Playfair Display',serif;font-size:1.1rem;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #e5e7eb}
.oi-row{display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid #f3f4f6}
.oi-img{width:56px;height:56px;background:#f8faff;border-radius:8px;flex-shrink:0;border:1px solid #e5e7eb}
.oi-name{font-size:.9rem;font-weight:600;color:#111;margin-bottom:3px}
.oi-meta{font-size:.78rem;color:#9ca3af}
.oi-price{margin-left:auto;font-weight:700;color:#1a6fe8;white-space:nowrap}
.success-addr{background:#f9fafb;border-radius:10px;padding:18px 20px;text-align:left;margin-bottom:28px}
.success-addr h4{font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;margin-bottom:8px}
.success-addr p{font-size:.9rem;color:#333;margin-bottom:4px}
.success-actions{display:flex;gap:16px;justify-content:center;flex-wrap:wrap}
@media(max-width:600px){.order-meta-grid{grid-template-columns:repeat(2,1fr)}.success-card{padding:32px 24px}}
</style>
