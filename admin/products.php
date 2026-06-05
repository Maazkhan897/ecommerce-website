<?php
require_once 'auth.php';
$pageTitle  = 'Manage Products';
$activePage = 'products';

$msg = '';
if (isset($_GET['deleted']))  $msg = 'ok:Product deleted successfully.';
if (isset($_GET['added']))    $msg = 'ok:Product added successfully.';
if (isset($_GET['updated']))  $msg = 'ok:Product updated successfully.';

// Search / filter
$search = sanitize($_GET['q'] ?? '');
$gender = sanitize($_GET['gender'] ?? '');

$where = "WHERE 1";
if ($search) $where .= " AND (p.name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%')";
if ($gender) $where .= " AND p.gender='" . mysqli_real_escape_string($conn, $gender) . "'";

$products = mysqli_query($conn,
    "SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id
     $where ORDER BY p.created_at DESC");

include '_header.php';
?>

<div class="page-hdr">
  <div>
    <h1>👕 Manage Products</h1>
    <p>View, edit and remove products from your catalog.</p>
  </div>
  <a href="add_product.php" class="btn btn-blue btn-lg">➕ Add New Product</a>
</div>

<?php if ($msg): ?>
  <?php [$type, $text] = explode(':', $msg, 2); ?>
  <div class="alert alert-ok"><?= htmlspecialchars($text) ?></div>
<?php endif; ?>

<!-- Filters -->
<div class="fcard" style="padding:18px 24px;margin-bottom:20px">
  <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
    <div class="fg" style="flex:1;min-width:200px;margin-bottom:0">
      <label>Search</label>
      <input type="text" name="q" placeholder="Product name…" value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="fg" style="min-width:160px;margin-bottom:0">
      <label>Category</label>
      <select name="gender">
        <option value="">All Categories</option>
        <option value="men"   <?= $gender==='men'  ?'selected':'' ?>>Men</option>
        <option value="women" <?= $gender==='women'?'selected':'' ?>>Women</option>
        <option value="shoes" <?= $gender==='shoes'?'selected':'' ?>>Shoes</option>
      </select>
    </div>
    <div style="margin-bottom:0;display:flex;gap:8px">
      <button type="submit" class="btn btn-blue">🔍 Search</button>
      <a href="products.php" class="btn btn-gray">Reset</a>
    </div>
  </form>
</div>

<div class="tcard">
  <div class="tcard-header">
    <div>
      <h3>Product Catalog</h3>
      <p><?= mysqli_num_rows($products) ?> product(s) found</p>
    </div>
  </div>
  <div class="twrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Product</th>
          <th>Category</th>
          <th>Price</th>
          <th>Original</th>
          <th>Stock</th>
          <th>Gender</th>
          <th>Featured</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($p = mysqli_fetch_assoc($products)): ?>
        <tr>
          <td class="text-muted"><?= $p['id'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:12px">
              <img src="../<?= htmlspecialchars($p['image']) ?>" class="timg" onerror="this.src='../hero.png'">
              <div>
                <div style="font-weight:600;font-size:.875rem"><?= htmlspecialchars($p['name']) ?></div>
                <div class="text-muted" style="margin-top:2px"><?= htmlspecialchars($p['cat_name'] ?? '—') ?></div>
              </div>
            </div>
          </td>
          <td class="text-muted"><?= htmlspecialchars($p['cat_name'] ?? '—') ?></td>
          <td class="fw" style="color:#1a6fe8">$<?= number_format($p['price'],2) ?></td>
          <td class="text-muted">
            <?= $p['original_price'] ? '$'.number_format($p['original_price'],2) : '—' ?>
          </td>
          <td>
            <?php if ($p['stock'] <= 5): ?>
              <span class="stock-low">⚠ <?= $p['stock'] ?></span>
            <?php elseif ($p['stock'] <= 10): ?>
              <span style="color:#d97706;font-weight:700"><?= $p['stock'] ?></span>
            <?php else: ?>
              <span class="stock-ok"><?= $p['stock'] ?></span>
            <?php endif; ?>
          </td>
          <td>
            <span class="badge" style="background:#f3f4f6;color:#374151"><?= ucfirst($p['gender']) ?></span>
          </td>
          <td>
            <?= $p['featured'] ? '<span class="badge badge-delivered">⭐ Yes</span>' : '<span class="text-muted">No</span>' ?>
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-green">✏ Edit</a>
              <form method="POST" action="delete_product.php"
                    onsubmit="return confirm('Delete \'<?= htmlspecialchars(addslashes($p['name'])) ?>\'? This cannot be undone.')">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn btn-red">🗑 Del</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($products) === 0): ?>
        <tr>
          <td colspan="9">
            <div class="empty">
              <div class="empty-icon">🔍</div>
              No products found matching your criteria.
            </div>
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '_footer.php'; ?>
