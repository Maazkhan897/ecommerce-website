<?php
require_once 'auth.php';
$pageTitle  = 'Manage Orders';
$activePage = 'orders';

$statusFilter = sanitize($_GET['status'] ?? '');
$search       = sanitize($_GET['q']      ?? '');

$where = "WHERE 1";
if ($statusFilter) $where .= " AND o.status='" . mysqli_real_escape_string($conn, $statusFilter) . "'";
if ($search)       $where .= " AND (u.name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' OR o.id LIKE '%" . mysqli_real_escape_string($conn, $search) . "%')";

$orders = mysqli_query($conn,
    "SELECT o.*, u.name AS customer, u.email AS cust_email
     FROM orders o JOIN users u ON o.user_id=u.id
     $where ORDER BY o.created_at DESC");

// Count by status for tabs
function countStatus($conn, $s) {
    $r = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) AS c FROM orders" . ($s ? " WHERE status='$s'" : "")));
    return $r['c'];
}
$counts = [
    ''           => countStatus($conn, ''),
    'pending'    => countStatus($conn, 'pending'),
    'processing' => countStatus($conn, 'processing'),
    'shipped'    => countStatus($conn, 'shipped'),
    'delivered'  => countStatus($conn, 'delivered'),
    'cancelled'  => countStatus($conn, 'cancelled'),
];

include '_header.php';
?>

<div class="page-hdr">
  <div>
    <h1>📦 Manage Orders</h1>
    <p>Track, review and update customer orders.</p>
  </div>
</div>

<!-- Status Tabs -->
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px">
  <?php
  $tabs = [
    ''           => ['All', '🗂'],
    'pending'    => ['Pending', '⏳'],
    'processing' => ['Processing', '🔄'],
    'shipped'    => ['Shipped', '🚚'],
    'delivered'  => ['Delivered', '✅'],
    'cancelled'  => ['Cancelled', '❌'],
  ];
  foreach ($tabs as $val => [$label, $icon]):
    $active = $statusFilter === $val;
  ?>
  <a href="orders.php?status=<?= urlencode($val) ?><?= $search ? '&q='.urlencode($search) : '' ?>"
     style="padding:8px 16px;border-radius:8px;font-size:.82rem;font-weight:600;text-decoration:none;transition:all .18s;
            <?= $active ? 'background:#1a6fe8;color:#fff;' : 'background:#fff;color:#374151;border:1px solid #e5e7eb;' ?>">
    <?= $icon ?> <?= $label ?>
    <span style="margin-left:4px;<?= $active ? 'background:rgba(255,255,255,.2)' : 'background:#f3f4f6' ?>;padding:1px 7px;border-radius:10px;font-size:.7rem">
      <?= $counts[$val] ?>
    </span>
  </a>
  <?php endforeach; ?>
</div>

<!-- Search -->
<div class="fcard" style="padding:14px 20px;margin-bottom:18px">
  <form method="GET" style="display:flex;gap:12px;align-items:flex-end">
    <input type="hidden" name="status" value="<?= htmlspecialchars($statusFilter) ?>">
    <div class="fg" style="flex:1;margin-bottom:0">
      <input type="text" name="q" placeholder="Search by customer name or order ID…"
             value="<?= htmlspecialchars($search) ?>" style="width:100%">
    </div>
    <button type="submit" class="btn btn-blue">🔍 Search</button>
    <?php if ($search): ?>
      <a href="orders.php?status=<?= urlencode($statusFilter) ?>" class="btn btn-gray">Clear</a>
    <?php endif; ?>
  </form>
</div>

<div class="tcard">
  <div class="tcard-header">
    <div>
      <h3>Orders</h3>
      <p><?= mysqli_num_rows($orders) ?> order(s) found</p>
    </div>
  </div>
  <div class="twrap">
    <table>
      <thead>
        <tr>
          <th>Order #</th>
          <th>Customer</th>
          <th>Date</th>
          <th>Items</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($o = mysqli_fetch_assoc($orders)):
          $itemCount = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT COUNT(*) AS c FROM order_items WHERE order_id={$o['id']}"))['c'];
        ?>
        <tr>
          <td class="fw" style="color:#1a6fe8">#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></td>
          <td>
            <div style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($o['customer']) ?></div>
            <div class="text-muted"><?= htmlspecialchars($o['cust_email']) ?></div>
          </td>
          <td>
            <div style="font-size:.85rem"><?= date('M d, Y', strtotime($o['created_at'])) ?></div>
            <div class="text-muted"><?= date('H:i', strtotime($o['created_at'])) ?></div>
          </td>
          <td class="text-muted"><?= $itemCount ?> item<?= $itemCount!=1?'s':'' ?></td>
          <td class="fw">$<?= number_format($o['total'], 2) ?></td>
          <td>
            <span class="badge" style="background:#f3f4f6;color:#374151">
              <?= $o['payment_method'] === 'cod' ? '💵 COD' : '💳 Card' ?>
            </span>
          </td>
          <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
          <td><a href="order_detail.php?id=<?= $o['id'] ?>" class="btn btn-blue">📄 Details</a></td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($orders) === 0): ?>
        <tr>
          <td colspan="8">
            <div class="empty">
              <div class="empty-icon">📭</div>
              No orders found.
            </div>
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '_footer.php'; ?>
