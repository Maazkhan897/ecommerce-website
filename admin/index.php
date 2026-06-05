<?php
require_once 'auth.php';
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';

// ── Stats ──
$totalOrders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders"))['c'];
$revenue       = (float)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total),0) AS s FROM orders WHERE status != 'cancelled'"))['s'];
$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM products"))['c'];
$totalUsers    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE is_admin=0 OR is_admin IS NULL"))['c'];
$pendingOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders WHERE status='pending'"))['c'];
$lowStock      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM products WHERE stock <= 10"))['c'];

// ── Recent orders ──
$recentOrders = mysqli_query($conn,
    "SELECT o.*, u.name AS customer FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 8");

// ── Low stock products ──
$lowStockProducts = mysqli_query($conn,
    "SELECT id, name, stock, image FROM products WHERE stock <= 10 ORDER BY stock ASC LIMIT 5");

include '_header.php';
?>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card c-blue">
    <span class="stat-icon">📦</span>
    <div class="stat-value"><?= number_format($totalOrders) ?></div>
    <div class="stat-label">Total Orders</div>
    <div class="stat-sub"><?= $pendingOrders ?> pending</div>
  </div>
  <div class="stat-card c-green">
    <span class="stat-icon">💰</span>
    <div class="stat-value"><?= '$' . number_format($revenue, 2) ?></div>
    <div class="stat-label">Total Revenue</div>
    <div class="stat-sub up">Completed orders</div>
  </div>
  <div class="stat-card c-purple">
    <span class="stat-icon">👕</span>
    <div class="stat-value"><?= number_format($totalProducts) ?></div>
    <div class="stat-label">Products</div>
    <div class="stat-sub"><?= $lowStock ?> low stock</div>
  </div>
  <div class="stat-card c-orange">
    <span class="stat-icon">👥</span>
    <div class="stat-value"><?= number_format($totalUsers) ?></div>
    <div class="stat-label">Customers</div>
    <div class="stat-sub">Registered accounts</div>
  </div>
</div>

<div class="two-col" style="align-items:start">
  <!-- Recent Orders -->
  <div class="tcard">
    <div class="tcard-header">
      <div>
        <h3>📋 Recent Orders</h3>
        <p>Latest 8 orders placed</p>
      </div>
      <a href="orders.php" class="btn btn-gray">View All</a>
    </div>
    <div class="twrap">
      <table>
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php while ($o = mysqli_fetch_assoc($recentOrders)): ?>
          <tr>
            <td class="fw">#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></td>
            <td><?= htmlspecialchars($o['customer']) ?></td>
            <td class="fw">$<?= number_format($o['total'], 2) ?></td>
            <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
            <td class="text-muted"><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
            <td><a href="order_detail.php?id=<?= $o['id'] ?>" class="btn btn-green">View</a></td>
          </tr>
          <?php endwhile; ?>
          <?php if ($totalOrders === 0): ?>
          <tr><td colspan="6" class="empty"><div class="empty-icon">📭</div>No orders yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Low Stock -->
  <div>
    <div class="tcard">
      <div class="tcard-header">
        <div>
          <h3>⚠ Low Stock Alert</h3>
          <p>Products with ≤ 10 units left</p>
        </div>
      </div>
      <div class="twrap">
        <table>
          <thead>
            <tr><th>Product</th><th>Stock</th><th></th></tr>
          </thead>
          <tbody>
            <?php
            $hasLow = false;
            while ($p = mysqli_fetch_assoc($lowStockProducts)):
              $hasLow = true;
            ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <img src="../<?= htmlspecialchars($p['image']) ?>" class="timg" onerror="this.src='../hero.png'">
                  <span style="font-size:.83rem;font-weight:600"><?= htmlspecialchars($p['name']) ?></span>
                </div>
              </td>
              <td><span class="stock-low"><?= $p['stock'] ?> left</span></td>
              <td><a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-blue">Edit</a></td>
            </tr>
            <?php endwhile; ?>
            <?php if (!$hasLow): ?>
            <tr><td colspan="3" style="text-align:center;padding:28px;color:#16a34a;font-size:.875rem">✓ All products have sufficient stock</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="fcard">
      <div class="fcard-title">⚡ Quick Actions</div>
      <div style="display:flex;flex-direction:column;gap:10px">
        <a href="add_product.php" class="btn btn-blue btn-lg" style="justify-content:center">➕ Add New Product</a>
        <a href="orders.php?status=pending" class="btn btn-gray btn-lg" style="justify-content:center">📋 View Pending Orders</a>
        <a href="products.php" class="btn btn-gray btn-lg" style="justify-content:center">👕 Manage Products</a>
      </div>
    </div>
  </div>
</div>

<?php include '_footer.php'; ?>
