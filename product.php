<?php
require_once __DIR__ . '/db.php';
$pageTitle = 'Product Details – Winter Fashion';
$activeNav = 'pages';
require_once 'header.php';

$id = (int)($_GET['id'] ?? 0);

// Fetch product
$stmt = mysqli_prepare($conn, "SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$product) {
    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.featured DESC, p.id LIMIT 1"));
}

$pid    = (int)$product['id'];
$gender = sanitize($product['gender']);
$related = mysqli_query($conn, "SELECT * FROM products WHERE gender='$gender' AND id != $pid ORDER BY RAND() LIMIT 4");
?>

<div class="breadcrumb-bar">
  <p class="breadcrumb">
    <a href="index.php">Home</a> /
    <a href="<?= $product['gender']==='men'?'male.php':($product['gender']==='women'?'female.php':'shoes.php') ?>">
      <?= ucfirst($product['gender']) ?>'s Fashion</a> /
    <?= htmlspecialchars($product['name']) ?>
  </p>
</div>

<div class="product-detail">
  <div class="product-images">
    <div class="main-image" id="main-img" style="display:flex;align-items:center;justify-content:center;background:#f8faff">
      <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
           onerror="this.src='hero.png'" style="max-width:92%;max-height:92%;object-fit:contain">
    </div>
    <div class="thumbnails">
      <div class="thumb active">
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="" onerror="this.style.display='none'"
             style="width:100%;height:100%;object-fit:contain;padding:4px">
      </div>
    </div>
  </div>

  <div class="product-detail-info">
    <?php if ($product['original_price'] > $product['price']): ?>
      <span class="sale-badge-lg">SALE</span>
    <?php endif; ?>
    <p class="pd-cat"><?= htmlspecialchars($product['cat_name'] ?? '') ?></p>
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <div class="pd-rating">
      <?= renderStars((float)$product['rating']) ?>
      <span class="pd-review-count">(<?= (int)$product['reviews_count'] ?> Reviews)</span>
    </div>
    <div class="pd-price-row">
      <span class="detail-price"><?= formatPrice((float)$product['price']) ?></span>
      <?php if ($product['original_price'] > $product['price']): ?>
        <span class="price-original"><?= formatPrice((float)$product['original_price']) ?></span>
        <span class="price-save">Save <?= formatPrice($product['original_price'] - $product['price']) ?></span>
      <?php endif; ?>
    </div>
    <div class="detail-meta">
      <p>Category <span>: <?= htmlspecialchars($product['cat_name'] ?? 'N/A') ?></span></p>
      <p>Availability <span>: <?= $product['stock']>0?'<span style="color:#16a34a">✓ In Stock</span>':'<span style="color:#dc2626">Out of Stock</span>' ?></span></p>
    </div>
    <p class="detail-desc"><?= htmlspecialchars($product['description']) ?></p>

    <div class="size-section">
      <p class="size-label">Select Size</p>
      <div class="size-btns">
        <?php foreach (['XS','S','M','L','XL','XXL'] as $s): ?>
          <button class="size-btn <?= $s==='M'?'active-size':'' ?>" onclick="selectSize(this,'<?= $s ?>')"><?= $s ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="qty-selector">
      <button class="qty-btn" onclick="changeQty(-1)">−</button>
      <span class="qty-display" id="qty">1</span>
      <button class="qty-btn" onclick="changeQty(1)">+</button>
    </div>

    <div class="detail-actions">
      <button class="btn-add-cart" onclick="addToCartDetail(<?= $pid ?>)">🛒 ADD TO CART</button>
      <button class="btn-wishlist">♡</button>
    </div>
    <div class="pd-toast" id="pd-toast"></div>
    <div class="trust-badges">
      <span>🚚 Free shipping over $50</span>
      <span>🔄 30-day returns</span>
      <span>🔒 Secure checkout</span>
    </div>
  </div>
</div>

<div class="product-tabs">
  <div class="tab-buttons">
    <button class="tab-btn" onclick="showTab('desc',this)">Description</button>
    <button class="tab-btn" onclick="showTab('spec',this)">Specification</button>
    <button class="tab-btn active" onclick="showTab('reviews',this)">Reviews</button>
  </div>
  <div class="tab-content" id="desc">
    <p style="color:#555;line-height:1.9;max-width:720px;font-size:.95rem"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
  </div>
  <div class="tab-content" id="spec">
    <table class="spec-table">
      <tr><td>Gender</td><td><?= ucfirst($product['gender']) ?></td></tr>
      <tr><td>Category</td><td><?= htmlspecialchars($product['cat_name'] ?? '—') ?></td></tr>
      <tr><td>Price</td><td><?= formatPrice((float)$product['price']) ?></td></tr>
      <tr><td>Stock</td><td><?= (int)$product['stock'] ?> units</td></tr>
      <tr><td>Rating</td><td><?= $product['rating'] ?>/5 (<?= $product['reviews_count'] ?> reviews)</td></tr>
    </table>
  </div>
  <div class="tab-content active" id="reviews">
    <div class="reviews-section">
      <div class="overall-box">
        <p style="font-size:.8rem;color:#6b7280;margin-bottom:8px">Overall</p>
        <div class="overall-score"><?= number_format((float)$product['rating'],1) ?></div>
        <div style="color:#ffd700;font-size:1.2rem;margin:8px 0"><?= renderStars((float)$product['rating']) ?></div>
        <p style="font-size:.8rem;color:#6b7280">Based on <?= $product['reviews_count'] ?> reviews</p>
      </div>
      <div class="star-breakdown">
        <h4 style="margin-bottom:16px;font-size:.95rem">Rating Breakdown</h4>
        <?php foreach ([5=>70,4=>20,3=>6,2=>3,1=>1] as $star=>$pct): ?>
        <div class="star-row">
          <span><?= $star ?> Star</span>
          <span style="color:#ffd700"><?= str_repeat('★',$star) ?><?= str_repeat('☆',5-$star) ?></span>
          <div class="star-bar"><div class="star-fill" style="width:<?= $pct ?>%"></div></div>
          <span><?= round($product['reviews_count']*$pct/100) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="add-review">
        <h3>Write a Review</h3>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:16px">Share your experience</p>
        <div class="review-form">
          <input type="text" placeholder="Your Name">
          <input type="email" placeholder="Your Email">
          <textarea placeholder="Your review..." rows="3"></textarea>
          <button class="btn-primary" style="align-self:flex-start">Submit Review</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (mysqli_num_rows($related) > 0): ?>
<div class="related-section">
  <h2 class="section-title-gradient">Related Products</h2>
  <div class="related-grid">
    <?php while ($rel = mysqli_fetch_assoc($related)): ?>
    <div class="shop-product-card" onclick="location.href='product.php?id=<?= $rel['id'] ?>'">
      <div class="product-img" style="background:#f8faff;position:relative">
        <img src="<?= htmlspecialchars($rel['image']) ?>" alt="<?= htmlspecialchars($rel['name']) ?>"
             onerror="this.src='hero.png'" style="width:180px;height:200px;object-fit:contain">
        <?php if ($rel['original_price'] > $rel['price']): ?>
          <span class="sale-badge">SALE</span>
        <?php endif; ?>
      </div>
      <div class="product-info">
        <p class="product-cat"><?= ucfirst($rel['gender']) ?></p>
        <h3><?= htmlspecialchars($rel['name']) ?></h3>
        <div class="product-footer">
          <span class="price"><?= formatPrice((float)$rel['price']) ?></span>
          <button class="add-btn" onclick="event.stopPropagation();quickAdd(<?= $rel['id'] ?>)">Add</button>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>

<style>
.breadcrumb-bar{background:#f9fafb;padding:12px 24px;border-bottom:1px solid #e5e7eb}
.breadcrumb-bar .breadcrumb{max-width:1280px;margin:0 auto;font-size:.85rem;color:#6b7280}
.breadcrumb-bar a{color:#1a6fe8}
.pd-cat{font-size:.75rem;color:#1a6fe8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px;font-weight:700}
.pd-rating{display:flex;align-items:center;gap:8px;margin-bottom:16px}
.star{color:#ffd700;font-size:1.1rem}
.pd-review-count{color:#6b7280;font-size:.85rem}
.pd-price-row{display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.price-original{text-decoration:line-through;color:#9ca3af;font-size:1.1rem}
.price-save{background:#dcfce7;color:#16a34a;font-size:.8rem;font-weight:700;padding:4px 10px;border-radius:20px}
.sale-badge-lg{background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;font-size:.8rem;font-weight:700;
  padding:6px 14px;border-radius:4px;letter-spacing:.05em;display:inline-block;margin-bottom:12px}
.size-section{margin-bottom:20px}
.size-label{font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#333;margin-bottom:10px}
.size-btns{display:flex;gap:8px;flex-wrap:wrap}
.size-btn{width:44px;height:44px;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;border-radius:8px;
  font-size:.85rem;font-weight:600;transition:all .2s}
.size-btn:hover{border-color:#1a6fe8;color:#1a6fe8}
.size-btn.active-size{background:#1a6fe8;border-color:#1a6fe8;color:#fff}
.pd-toast{margin-top:16px;font-size:.875rem;font-weight:600;opacity:0;transition:opacity .3s;color:#16a34a;min-height:20px}
.trust-badges{display:flex;gap:20px;flex-wrap:wrap;margin-top:20px;padding-top:20px;border-top:1px solid #e5e7eb}
.trust-badges span{font-size:.8rem;color:#555}
.spec-table{border-collapse:collapse;width:100%;max-width:500px}
.spec-table tr{border-bottom:1px solid #e5e7eb}
.spec-table td{padding:12px;font-size:.9rem}
.spec-table td:first-child{font-weight:700;color:#333;width:140px}
.spec-table td:last-child{color:#555}
.sale-badge{position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#ef4444,#dc2626);
  color:#fff;font-size:.7rem;font-weight:700;padding:4px 10px;border-radius:4px;z-index:2}
.related-section{max-width:1280px;margin:0 auto 80px;padding:0 24px}
.section-title-gradient{font-family:'Playfair Display',serif;font-size:1.9rem;margin-bottom:32px;
  background:linear-gradient(135deg,#1a6fe8,#0a3580);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.related-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px}
@media(max-width:1024px){.related-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.related-grid{grid-template-columns:1fr 1fr}}
</style>

<script>
let selectedSize='M';
function selectSize(el,s){
  document.querySelectorAll('.size-btn').forEach(b=>b.classList.remove('active-size'));
  el.classList.add('active-size'); selectedSize=s;
}
function addToCartDetail(pid){
  const qty=parseInt(document.getElementById('qty').textContent);
  const toast=document.getElementById('pd-toast');
  const btn=document.querySelector('.btn-add-cart');
  fetch('add_to_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`product_id=${pid}&quantity=${qty}&size=${selectedSize}`})
  .then(r=>r.json()).then(d=>{
    if(d.success){
      toast.style.color='#16a34a'; toast.textContent='✓ '+d.message; toast.style.opacity='1';
      document.getElementById('cart-badge').textContent=d.cart_count;
      btn.innerHTML='✓ Added!'; btn.style.background='#16a34a';
      setTimeout(()=>{toast.style.opacity='0';btn.innerHTML='🛒 ADD TO CART';btn.style.background='';},2200);
    } else if(d.redirect){
      window.location.href=d.redirect+'?redirect='+encodeURIComponent(window.location.href);
    } else {
      toast.style.color='#dc2626'; toast.textContent='✗ '+d.message; toast.style.opacity='1';
      setTimeout(()=>{toast.style.opacity='0';},2000);
    }
  });
}
function quickAdd(pid){
  fetch('add_to_cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`product_id=${pid}&quantity=1&size=M`})
  .then(r=>r.json()).then(d=>{
    if(d.success) document.getElementById('cart-badge').textContent=d.cart_count;
    else if(d.redirect) window.location.href=d.redirect;
  });
}
showTab('reviews',document.querySelector('.tab-btn.active'));
</script>
