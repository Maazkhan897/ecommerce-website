<?php
require_once __DIR__ . '/db.php';
// Shared shop grid layout — included by male.php, female.php, shoes.php
/** @var string $gender */
/** @var \mysqli_result|false $products */
$genderLabel = $gender === 'men' ? "Men's" : ($gender === 'women' ? "Women's" : 'Shoes');
$browseLinks = [
    'male.php'   => "Men's Fashion",
    'female.php' => "Women's Fashion",
    'shoes.php'  => 'Shoes',
];
$currentPage = basename($_SERVER['PHP_SELF']);

// Sidebar categories
$sideCats = mysqli_query($conn, "SELECT * FROM categories WHERE gender='$gender' OR gender='all'");
?>

<div class="shop-layout">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-section">
      <h4>Categories</h4>
      <ul>
        <li><a href="<?= $currentPage ?>" class="active">All <?= $genderLabel ?></a></li>
        <?php while ($cat = mysqli_fetch_assoc($sideCats)): ?>
          <li><a href="#"><?= htmlspecialchars($cat['name']) ?></a></li>
        <?php endwhile; ?>
      </ul>
    </div>
    <div class="sidebar-section">
      <h4>Price Range</h4>
      <div class="price-range">
        <input type="range" min="0" max="500" value="300" id="priceRange" oninput="document.getElementById('priceVal').textContent='$'+this.value">
        <div class="price-range-vals"><span>$0</span><span id="priceVal">$300</span></div>
      </div>
    </div>
    <div class="sidebar-section">
      <h4>Size</h4>
      <div style="display:flex;flex-wrap:wrap;gap:8px">
        <?php foreach (['XS','S','M','L','XL','XXL'] as $sz): ?>
          <button class="sidebar-size-btn" onclick="this.classList.toggle('active-size')"><?= $sz ?></button>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="sidebar-section">
      <h4>Browse</h4>
      <ul>
        <?php foreach ($browseLinks as $href => $label): ?>
          <li><a href="<?= $href ?>" <?= $href===$currentPage?'class="active"':'' ?>><?= $label ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </aside>

  <!-- PRODUCTS GRID -->
  <main>
    <?php
    /** @var \mysqli_result|false $products */
    $allRows = [];
    while ($products && $p = mysqli_fetch_assoc($products)) $allRows[] = $p;
    $count = count($allRows);
    ?>
    <div class="shop-grid-header">
      <p style="font-size:.85rem;color:#6b7280">Showing <strong><?= $count ?></strong> products</p>
      <select onchange="" style="padding:8px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none">
        <option>Default Sorting</option>
        <option>Price: Low to High</option>
        <option>Price: High to Low</option>
        <option>Newest First</option>
      </select>
    </div>

    <div class="shop-grid">
      <?php if (empty($allRows)): ?>
        <p style="grid-column:1/-1;text-align:center;padding:60px;color:#9ca3af">No products found.</p>
      <?php endif; ?>
      <?php foreach ($allRows as $p):
        $disc = $p['original_price'] > $p['price'];
      ?>
      <div class="shop-product-card" onclick="location.href='product.php?id=<?= $p['id'] ?>'">
        <div class="product-img" style="position:relative;overflow:hidden">
          <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"
               onerror="this.src='hero.png'"
               style="width:100%;height:240px;object-fit:contain;display:block;transition:transform .4s">
          <?php if ($disc): ?>
            <span class="sale-badge">SALE</span>
          <?php endif; ?>
          <?php if ($p['featured']): ?>
            <span class="featured-badge">⭐ Featured</span>
          <?php endif; ?>
          <div class="sp-card-overlay">
            <button onclick="event.stopPropagation();shopQuickAdd(<?= $p['id'] ?>)" class="overlay-btn-sm">🛒 Add to Cart</button>
          </div>
        </div>
        <div class="product-info">
          <p class="product-cat"><?= htmlspecialchars($p['cat_name'] ?? ucfirst($gender)) ?></p>
          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <div class="rating"><?= str_repeat('★', (int)$p['rating']) ?><?= str_repeat('☆', 5-(int)$p['rating']) ?>
            <span style="color:#9ca3af;font-size:.75rem">(<?= $p['reviews_count'] ?>)</span>
          </div>
          <div class="product-footer">
            <div>
              <span class="price"><?= formatPrice((float)$p['price']) ?></span>
              <?php if ($disc): ?>
                <span style="text-decoration:line-through;color:#9ca3af;font-size:.8rem;margin-left:6px"><?= formatPrice((float)$p['original_price']) ?></span>
              <?php endif; ?>
            </div>
            <button class="add-btn" onclick="event.stopPropagation();shopQuickAdd(<?= $p['id'] ?>)">Add to Cart</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </main>
</div>

<style>
.shop-grid-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.sidebar-size-btn{padding:6px 12px;border:1.5px solid #e5e7eb;background:#fff;border-radius:6px;
  font-size:.8rem;font-weight:600;cursor:pointer;transition:all .2s}
.sidebar-size-btn.active-size,.sidebar-size-btn:hover{background:#1a6fe8;border-color:#1a6fe8;color:#fff}
.featured-badge{position:absolute;top:12px;right:12px;background:linear-gradient(135deg,#f59e0b,#d97706);
  color:#fff;font-size:.68rem;font-weight:700;padding:4px 8px;border-radius:4px;z-index:2}
.sale-badge{position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#ef4444,#dc2626);
  color:#fff;font-size:.7rem;font-weight:700;padding:4px 10px;border-radius:4px;z-index:2}
.sp-card-overlay{position:absolute;bottom:0;left:0;right:0;padding:12px;
  background:linear-gradient(transparent,rgba(26,111,232,.9));
  opacity:0;transition:opacity .3s;display:flex;justify-content:center}
.shop-product-card:hover .sp-card-overlay{opacity:1}
.shop-product-card:hover .product-img img{transform:scale(1.05)}
.overlay-btn-sm{background:#fff;color:#1a6fe8;border:none;padding:9px 20px;border-radius:20px;
  font-size:.8rem;font-weight:700;cursor:pointer;transition:all .2s}
.overlay-btn-sm:hover{background:#1a6fe8;color:#fff}
</style>

<script>
function shopQuickAdd(pid) {
  fetch('add_to_cart.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `product_id=${pid}&quantity=1&size=M`
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      document.getElementById('cart-badge').textContent = d.cart_count;
      showToast(d.message);
    } else if (d.redirect) {
      window.location.href = d.redirect;
    } else {
      showToast(d.message, true);
    }
  });
}

function showToast(msg, isError = false) {
  let t = document.getElementById('global-toast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'global-toast';
    t.style.cssText = `position:fixed;bottom:24px;right:24px;padding:14px 22px;border-radius:12px;
      font-weight:600;font-size:.875rem;z-index:9999;transition:all .3s;box-shadow:0 8px 24px rgba(0,0,0,.15)`;
    document.body.appendChild(t);
  }
  t.style.background = isError ? '#fef2f2' : '#f0fdf4';
  t.style.color      = isError ? '#dc2626'  : '#16a34a';
  t.style.border     = `1px solid ${isError ? '#fecaca' : '#bbf7d0'}`;
  t.textContent = (isError ? '✗ ' : '✓ ') + msg;
  t.style.opacity = '1'; t.style.transform = 'translateY(0)';
  setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateY(8px)'; }, 2500);
}
</script>
