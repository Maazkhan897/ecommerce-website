<?php
require_once 'auth.php';
$pageTitle  = 'Order Details';
$activePage = 'orders';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: orders.php'); exit(); }

$stmt = mysqli_prepare($conn, "SELECT o.*, u.name AS customer, u.email AS cust_email FROM orders o JOIN users u ON o.user_id=u.id WHERE o.id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$order) { header('Location: orders.php'); exit(); }

$items = mysqli_query($conn,
    "SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$id");

$updated = isset($_GET['updated']);
include '_header.php';
?>

<div class="page-hdr">
  <div>
    <h1>📄 Order #<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></h1>
    <p>Placed on <?= date('F d, Y \a\t H:i', strtotime($order['created_at'])) ?></p>
  </div>
  <a href="orders.php" class="btn btn-gray">← Back to Orders</a>
</div>

<?php if ($updated): ?>
  <div class="alert alert-ok">✓ Order status updated successfully.</div>
<?php endif; ?>

<div class="two-col">
  <!-- Left: Items + Shipping -->
  <div>
    <!-- Order Items -->
    <div class="tcard" style="margin-bottom:20px">
      <div class="tcard-header"><h3>🛍 Items Ordered</h3></div>
      <div class="twrap">
        <table>
          <thead>
            <tr><th>Product</th><th>Size</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr>
          </thead>
          <tbody>
            <?php while ($item = mysqli_fetch_assoc($items)): ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <img src="../<?= htmlspecialchars($item['image']) ?>" class="timg" onerror="this.src='../hero.png'">
                  <span style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($item['name']) ?></span>
                </div>
              </td>
              <td><span class="badge" style="background:#f3f4f6;color:#374151"><?= htmlspecialchars($item['size']) ?></span></td>
              <td class="fw"><?= $item['quantity'] ?></td>
              <td>$<?= number_format($item['price'], 2) ?></td>
              <td class="fw" style="color:#1a6fe8">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Shipping Details -->
    <div class="fcard">
      <div class="fcard-title">🚚 Shipping Information</div>
      <div class="fgrid2">
        <div>
          <div class="detail-row"><span class="label">Full Name</span><span class="val"><?= htmlspecialchars($order['shipping_name']) ?></span></div>
          <div class="detail-row"><span class="label">Email</span><span class="val"><?= htmlspecialchars($order['shipping_email']) ?></span></div>
          <div class="detail-row"><span class="label">Phone</span><span class="val"><?= htmlspecialchars($order['shipping_phone'] ?: '—') ?></span></div>
        </div>
        <div>
          <div class="detail-row"><span class="label">Address</span><span class="val"><?= htmlspecialchars($order['shipping_address']) ?></span></div>
          <div class="detail-row"><span class="label">City</span><span class="val"><?= htmlspecialchars($order['shipping_city']) ?></span></div>
          <div class="detail-row"><span class="label">ZIP</span><span class="val"><?= htmlspecialchars($order['shipping_zip'] ?: '—') ?></span></div>
        </div>
      </div>
      <?php if ($order['notes']): ?>
        <div style="margin-top:12px;padding:12px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;font-size:.875rem;color:#92400e">
          <strong>📝 Note:</strong> <?= htmlspecialchars($order['notes']) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Right: Summary + Status -->
  <div>
    <!-- Order Summary -->
    <div class="fcard" style="margin-bottom:20px">
      <div class="fcard-title">💰 Order Summary</div>
      <div class="detail-row"><span class="label">Customer</span><span class="val"><?= htmlspecialchars($order['customer']) ?></span></div>
      <div class="detail-row"><span class="label">Subtotal</span><span class="val">$<?= number_format($order['subtotal'], 2) ?></span></div>
      <div class="detail-row">
        <span class="label">Shipping</span>
        <span class="val"><?= $order['shipping'] == 0 ? '<span style="color:#16a34a">FREE</span>' : '$'.number_format($order['shipping'], 2) ?></span>
      </div>
      <div class="detail-row" style="border-top:2px solid #e5e7eb;margin-top:4px;padding-top:14px">
        <span class="label fw">Total</span>
        <span class="val" style="font-size:1.1rem;color:#1a6fe8">$<?= number_format($order['total'], 2) ?></span>
      </div>
      <div class="detail-row">
        <span class="label">Payment</span>
        <span class="val"><?= $order['payment_method'] === 'cod' ? '💵 Cash on Delivery' : '💳 Credit Card' ?></span>
      </div>
      <div class="detail-row">
        <span class="label">Current Status</span>
        <span class="val"><span class="badge badge-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></span>
      </div>
    </div>

    <!-- Update Status -->
    <div class="fcard">
      <div class="fcard-title">🔄 Update Order Status</div>
      <form method="POST" action="update_order_status.php">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <div class="fg">
          <label>New Status</label>
          <select name="status" required>
            <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-blue btn-lg" style="width:100%;justify-content:center">
          💾 Update Status
        </button>
      </form>

      <div style="margin-top:16px;padding:12px;background:#f9fafb;border-radius:8px">
        <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;margin-bottom:8px">Status Flow</div>
        <div style="display:flex;flex-direction:column;gap:6px">
          <?php
          $flow = ['pending'=>'⏳','processing'=>'🔄','shipped'=>'🚚','delivered'=>'✅','cancelled'=>'❌'];
          foreach ($flow as $s => $icon):
            $isCurrent = $order['status'] === $s;
          ?>
          <div style="display:flex;align-items:center;gap:8px;font-size:.82rem;
                      <?= $isCurrent ? 'font-weight:700;color:#1a6fe8' : 'color:#9ca3af' ?>">
            <?= $icon ?> <?= ucfirst($s) ?>
            <?= $isCurrent ? ' ← <em>current</em>' : '' ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '_footer.php'; ?>
