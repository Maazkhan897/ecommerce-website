<?php
require_once 'auth.php';
$pageTitle  = 'Edit Product';
$activePage = 'products';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: products.php'); exit(); }

$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$product) { header('Location: products.php'); exit(); }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = sanitize($_POST['name']          ?? '');
    $description   = sanitize($_POST['description']   ?? '');
    $price         = (float)($_POST['price']          ?? 0);
    $original_price= (float)($_POST['original_price'] ?? 0);
    $category_id   = (int)($_POST['category_id']      ?? 0);
    $image         = sanitize($_POST['image']         ?? '');
    $stock         = (int)($_POST['stock']            ?? 0);
    $rating        = (float)($_POST['rating']         ?? 4.5);
    $reviews_count = (int)($_POST['reviews_count']    ?? 0);
    $gender        = sanitize($_POST['gender']        ?? '');
    $featured      = isset($_POST['featured']) ? 1 : 0;

    if (empty($name))   $errors[] = 'Product name is required.';
    if ($price <= 0)    $errors[] = 'Price must be greater than 0.';
    if (empty($gender)) $errors[] = 'Gender/category is required.';
    if (empty($image))  $errors[] = 'Image filename is required.';

    if (empty($errors)) {
        $upd = mysqli_prepare($conn,
            "UPDATE products SET name=?,description=?,price=?,original_price=?,category_id=?,image=?,stock=?,rating=?,reviews_count=?,gender=?,featured=? WHERE id=?");
        mysqli_stmt_bind_param($upd, "ssddisidisii",
            $name, $description, $price, $original_price,
            $category_id, $image, $stock, $rating, $reviews_count, $gender, $featured, $id);
        if (mysqli_stmt_execute($upd)) {
            header('Location: products.php?updated=1');
            exit();
        } else {
            $errors[] = 'Database error: ' . mysqli_error($conn);
        }
    }
    // Re-populate $product with POST values for re-display
    $product = array_merge($product, $_POST);
}

$categories = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
include '_header.php';
?>

<div class="page-hdr">
  <div>
    <h1>✏ Edit Product</h1>
    <p>Update the details for: <strong><?= htmlspecialchars($product['name']) ?></strong></p>
  </div>
  <a href="products.php" class="btn btn-gray">← Back to Products</a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-err">
    <?php foreach ($errors as $e): ?><div>✗ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Current image preview -->
<div class="fcard" style="padding:16px 24px;margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:16px">
    <img src="../<?= htmlspecialchars($product['image']) ?>" onerror="this.src='../hero.png'"
         style="width:72px;height:72px;object-fit:contain;background:#f8faff;border:1px solid #e5e7eb;border-radius:10px;padding:4px">
    <div>
      <div style="font-weight:700;font-size:.95rem"><?= htmlspecialchars($product['name']) ?></div>
      <div class="text-muted">ID: <?= $product['id'] ?> &nbsp;·&nbsp; Stock: <?= $product['stock'] ?></div>
    </div>
  </div>
</div>

<form method="POST">
  <div class="fcard">
    <div class="fcard-title">📋 Product Details</div>
    <div class="fg">
      <label>Product Name *</label>
      <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>
    <div class="fg">
      <label>Description</label>
      <textarea name="description"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
    </div>
    <div class="fgrid3">
      <div class="fg">
        <label>Price ($) *</label>
        <input type="number" name="price" step="0.01" min="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>
      </div>
      <div class="fg">
        <label>Original Price ($)</label>
        <input type="number" name="original_price" step="0.01" min="0" value="<?= htmlspecialchars($product['original_price'] ?? '') ?>">
      </div>
      <div class="fg">
        <label>Stock *</label>
        <input type="number" name="stock" min="0" value="<?= htmlspecialchars($product['stock']) ?>" required>
      </div>
    </div>
    <div class="fgrid3">
      <div class="fg">
        <label>Rating (1–5)</label>
        <input type="number" name="rating" step="0.1" min="1" max="5" value="<?= htmlspecialchars($product['rating']) ?>">
      </div>
      <div class="fg">
        <label>Reviews Count</label>
        <input type="number" name="reviews_count" min="0" value="<?= htmlspecialchars($product['reviews_count']) ?>">
      </div>
      <div class="fg">
        <label>Gender / Section *</label>
        <select name="gender" required>
          <option value="men"   <?= $product['gender']==='men'  ?'selected':'' ?>>Men</option>
          <option value="women" <?= $product['gender']==='women'?'selected':'' ?>>Women</option>
          <option value="shoes" <?= $product['gender']==='shoes'?'selected':'' ?>>Shoes</option>
        </select>
      </div>
    </div>
    <div class="fgrid2">
      <div class="fg">
        <label>Category</label>
        <select name="category_id">
          <option value="0">— No category —</option>
          <?php
          mysqli_data_seek($categories, 0);
          while ($cat = mysqli_fetch_assoc($categories)): ?>
            <option value="<?= $cat['id'] ?>" <?= $product['category_id']==$cat['id']?'selected':'' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="fg">
        <label>Image Filename *</label>
        <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>" required>
        <div class="text-muted" style="margin-top:5px;font-size:.75rem">File must be in the project root folder.</div>
      </div>
    </div>
    <div class="fg">
      <label style="display:flex;align-items:center;gap:10px;text-transform:none;letter-spacing:0;font-size:.875rem;cursor:pointer">
        <input type="checkbox" name="featured" <?= $product['featured']?'checked':'' ?> style="width:18px;height:18px;accent-color:#1a6fe8">
        Mark as Featured
      </label>
    </div>
  </div>

  <div style="display:flex;gap:12px">
    <button type="submit" class="btn btn-blue btn-lg">💾 Update Product</button>
    <a href="products.php" class="btn btn-gray btn-lg">Cancel</a>
  </div>
</form>

<?php include '_footer.php'; ?>
