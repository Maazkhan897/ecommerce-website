<?php
require_once __DIR__ . '/db.php';
require_once 'config.php';
$pageTitle = 'Winter Fashion – 2025 Collection';
$activeNav = 'home';
require_once 'header.php';

$allProducts = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC LIMIT 9");
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-content">
    <p class="hero-sub">✨ Winter Fashion 2025</p>
    <h1 class="hero-title">Fashion<br>Collection<br><span class="hero-title-accent">2025</span></h1>
    <p class="hero-desc">Discover curated styles for every occasion — premium quality, unbeatable prices.</p>
    <div class="hero-btns">
      <a href="male.php" class="btn-primary">Shop Now</a>
      <a href="female.php" class="btn-outline">Women's</a>
    </div>
    <div class="hero-stats">
      <div class="stat"><strong>2K+</strong><span>Products</span></div>
      <div class="stat"><strong>50K+</strong><span>Happy Customers</span></div>
      <div class="stat"><strong>4.9★</strong><span>Rating</span></div>
    </div>
  </div>
  <div class="hero-image">
    <div class="hero-img-wrap">
      <img src="hero.png" alt="Fashion Collection" style="width:100%;height:auto">
      <div class="hero-badge-1">🔥 New Arrivals</div>
      <div class="hero-badge-2">FREE Shipping $50+</div>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<section class="categories">
  <div class="cat-card">
    <div class="cat-img cat-img-1">
      <img src="feature_1.png" alt="Men's Fashion">
    </div>
    <div class="cat-label"><a href="male.php">Shop For Male</a></div>
  </div>
  <div class="cat-card">
    <div class="cat-img cat-img-2">
      <img src="feature_2.png" alt="Women's Fashion">
    </div>
    <div class="cat-label"><a href="female.php">Shop For Female</a></div>
  </div>
  <div class="cat-card">
    <div class="cat-img cat-img-3">
      <img src="feature_3.png" alt="Shoes">
    </div>
    <div class="cat-label"><a href="shoes.php">Shop For Shoes</a></div>
  </div>
</section>

<!-- NEW ARRIVALS -->
<section class="new-arrivals">
  <div class="section-header">
    <div>
      <p class="section-eyebrow">Fresh From The Store</p>
      <h2>New Arrivals</h2>
    </div>
    <div class="filter-tabs">
      <button class="tab active" onclick="filterProducts('all',this)">All</button>
      <button class="tab" onclick="filterProducts('men',this)">Men</button>
      <button class="tab" onclick="filterProducts('women',this)">Women</button>
      <button class="tab" onclick="filterProducts('shoes',this)">Shoes</button>
    </div>
  </div>

  <div class="products-grid" id="products-grid">
    <?php
    $products = mysqli_query($conn, "SELECT * FROM products ORDER BY RAND() LIMIT 9");
    while ($p = mysqli_fetch_assoc($products)):
      $discounted = $p['original_price'] > $p['price'];
    ?>
    <div class="product-card" data-cat="<?= htmlspecialchars($p['gender']) ?>">
      <div class="product-img" style="position:relative;overflow:hidden">
        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"
             onerror="this.src='hero.png'"
             style="width:100%;height:240px;object-fit:contain;display:block;transition:transform .4s">
        <?php if ($discounted): ?>
          <span class="sale-badge">SALE</span>
        <?php endif; ?>
        <div class="product-card-overlay">
          <button onclick="quickAddHome(<?= $p['id'] ?>)" class="overlay-btn">🛒 Add to Cart</button>
          <a href="product.php?id=<?= $p['id'] ?>" class="overlay-btn overlay-view">👁 Quick View</a>
        </div>
      </div>
      <div class="product-info">
        <span class="product-cat"><?= htmlspecialchars(ucfirst($p['gender'])) ?></span>
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <div class="product-footer">
          <div>
            <span class="price"><?= formatPrice((float)$p['price']) ?></span>
            <?php if ($discounted): ?>
              <span class="price-strike"><?= formatPrice((float)$p['original_price']) ?></span>
            <?php endif; ?>
          </div>
          <div class="product-actions">
            <button class="icon-btn" onclick="quickAddHome(<?= $p['id'] ?>)" title="Add to cart">🛒</button>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <div style="text-align:center;margin-top:40px">
    <a href="male.php" class="btn-outline" style="margin-right:12px">View Men's</a>
    <a href="female.php" class="btn-outline" style="margin-right:12px">View Women's</a>
    <a href="shoes.php" class="btn-outline">View Shoes</a>
  </div>
</section>

<!-- FEATURED BANNER -->
<section class="promo-section">
  <div class="promo-inner">
    <div class="promo-text">
      <p class="promo-eyebrow">Limited Time Offer</p>
      <h2>Get 20% OFF<br>Your First Order</h2>
      <p>Use code <span class="code-tag">WINTER20</span> at checkout</p>
      <a href="register.php" class="btn-primary" style="margin-top:24px;display:inline-block">Claim Offer</a>
    </div>
    <div class="promo-img">
      <img src="feature_2.png" alt="Promo" style="max-height:320px;object-fit:contain">
    </div>
  </div>
</section>

<?php require_once 'footer.php'; ?>

<style>
/* Hero enhancements */
.hero-desc{font-size:.95rem;color:#555;margin-bottom:28px;line-height:1.7;max-width:380px}
.hero-btns{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:36px}
.btn-outline{
  display:inline-block;padding:14px 28px;border:2px solid var(--blue);color:var(--blue);
  border-radius:4px;font-weight:600;font-size:.9rem;transition:all .2s;
}
.btn-outline:hover{background:var(--blue);color:#fff}
.hero-stats{display:flex;gap:32px}
.stat strong{display:block;font-size:1.3rem;font-weight:900;color:#111;font-family:'Playfair Display',serif}
.stat span{font-size:.8rem;color:#888}
.hero-title-accent{background:linear-gradient(135deg,#1a6fe8,#0a3580);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero-img-wrap{position:relative;padding:20px}
.hero-badge-1,.hero-badge-2{
  position:absolute;background:#fff;border-radius:12px;
  padding:10px 16px;font-size:.8rem;font-weight:700;
  box-shadow:0 8px 24px rgba(0,0,0,0.12);
}
.hero-badge-1{top:20px;right:20px;color:#ef4444}
.hero-badge-2{bottom:40px;left:-10px;color:#16a34a}
/* Section eyebrow */
.section-eyebrow{font-size:.75rem;text-transform:uppercase;letter-spacing:.1em;color:#1a6fe8;font-weight:700;margin-bottom:8px}
/* Product card overlay */
.product-card-overlay{
  position:absolute;inset:0;background:rgba(10,53,128,.75);
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;
  opacity:0;transition:opacity .3s;
}
.product-card:hover .product-card-overlay{opacity:1}
.product-card:hover .product-img img{transform:scale(1.06)}
.overlay-btn{
  background:#fff;color:#1a6fe8;border:none;padding:10px 22px;border-radius:24px;
  font-size:.83rem;font-weight:700;cursor:pointer;transition:all .2s;width:160px;text-align:center;
}
.overlay-btn:hover{background:#1a6fe8;color:#fff}
.overlay-view{display:block;text-decoration:none;background:transparent;border:1.5px solid #fff;color:#fff}
.overlay-view:hover{background:#fff;color:#1a6fe8}
.sale-badge{position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#ef4444,#dc2626);
  color:#fff;font-size:.7rem;font-weight:700;padding:4px 10px;border-radius:4px;z-index:2}
.price-strike{text-decoration:line-through;color:#9ca3af;font-size:.85rem;margin-left:6px}
/* Promo banner */
.promo-section{background:linear-gradient(135deg,#0a3580 0%,#1a6fe8 100%);margin:40px 0}
.promo-inner{max-width:1280px;margin:0 auto;padding:60px 24px;display:flex;align-items:center;justify-content:space-between;gap:40px}
.promo-text{color:#fff}
.promo-eyebrow{font-size:.8rem;text-transform:uppercase;letter-spacing:.1em;opacity:.8;margin-bottom:12px}
.promo-text h2{font-family:'Playfair Display',serif;font-size:2.8rem;font-weight:900;line-height:1.2;margin-bottom:16px}
.promo-text p{font-size:1rem;opacity:.9}
.code-tag{background:rgba(255,255,255,.2);border:1px dashed rgba(255,255,255,.5);padding:4px 12px;border-radius:4px;font-weight:700;letter-spacing:.05em}
.promo-text .btn-primary{background:#fff;color:#1a6fe8}
.promo-text .btn-primary:hover{background:#f0f9ff}
@media(max-width:768px){.promo-img{display:none}.promo-inner{padding:40px 24px}.promo-text h2{font-size:2rem}}
</style>

<script>
function quickAddHome(pid) {
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
