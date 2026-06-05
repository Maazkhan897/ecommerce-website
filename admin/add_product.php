<?php
require_once 'auth.php';
$pageTitle  = 'Add Product';
$activePage = 'add_product';

$errors = [];
$ok     = false;

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

    if (empty($name))    $errors[] = 'Product name is required.';
    if ($price <= 0)     $errors[] = 'Price must be greater than 0.';
    if (empty($gender))  $errors[] = 'Gender/category is required.';
    if (empty($image))   $errors[] = 'Image filename is required.';

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO products (name,description,price,original_price,category_id,image,stock,rating,reviews_count,gender,featured)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssddisidisi",
            $name, $description, $price, $original_price,
            $category_id, $image, $stock, $rating, $reviews_count, $gender, $featured);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: products.php?added=1');
            exit();
        } else {
            $errors[] = 'Database error: ' . mysqli_error($conn);
        }
    }
}

$categories = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
include '_header.php';
?>

<div class="page-hdr">
  <div>
    <h1>➕ Add New Product</h1>
    <p>Fill in the details below to add a product to the catalog.</p>
  </div>
  <a href="products.php" class="btn btn-gray">← Back to Products</a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-err">
    <?php foreach ($errors as $e): ?><div>✗ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
  </div>
<?php endif; ?>

<form method="POST">
  <div class="fcard">
    <div class="fcard-title">📋 Basic Information</div>
    <div class="fg">
      <label>Product Name *</label>
      <input type="text" name="name" placeholder="e.g. Classic White Tee" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
    </div>
    <div class="fg">
      <label>Description</label>
      <textarea name="description" placeholder="Describe this product…"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>
    <div class="fgrid3">
      <div class="fg">
        <label>Price ($) *</label>
        <input type="number" name="price" step="0.01" min="0.01" placeholder="29.99" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required>
      </div>
      <div class="fg">
        <label>Original Price ($)</label>
        <input type="number" name="original_price" step="0.01" min="0" placeholder="49.99 (for SALE badge)" value="<?= htmlspecialchars($_POST['original_price'] ?? '') ?>">
      </div>
      <div class="fg">
        <label>Stock *</label>
        <input type="number" name="stock" min="0" placeholder="100" value="<?= htmlspecialchars($_POST['stock'] ?? '100') ?>" required>
      </div>
    </div>
    <div class="fgrid3">
      <div class="fg">
        <label>Rating (1–5)</label>
        <input type="number" name="rating" step="0.1" min="1" max="5" placeholder="4.5" value="<?= htmlspecialchars($_POST['rating'] ?? '4.5') ?>">
      </div>
      <div class="fg">
        <label>Reviews Count</label>
        <input type="number" name="reviews_count" min="0" placeholder="0" value="<?= htmlspecialchars($_POST['reviews_count'] ?? '0') ?>">
      </div>
      <div class="fg">
        <label>Gender / Section *</label>
        <select name="gender" required>
          <option value="">Select…</option>
          <option value="men"   <?= ($_POST['gender']??'')==='men'  ?'selected':'' ?>>Men</option>
          <option value="women" <?= ($_POST['gender']??'')==='women'?'selected':'' ?>>Women</option>
          <option value="shoes" <?= ($_POST['gender']??'')==='shoes'?'selected':'' ?>>Shoes</option>
        </select>
      </div>
    </div>
    <div class="fgrid2">
      <div class="fg">
        <label>Category</label>
        <select name="category_id">
          <option value="0">— No category —</option>
          <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
            <option value="<?= $cat['id'] ?>" <?= ($_POST['category_id']??0)==$cat['id']?'selected':'' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="fg">
        <label>Image Filename *</label>
        <input type="text" name="image" placeholder="e.g. shirt2.png" value="<?= htmlspecialchars($_POST['image'] ?? '') ?>" required>
        <div class="text-muted" style="margin-top:5px;font-size:.75rem">Image must be uploaded to the project root folder.</div>
      </div>
    </div>
    <div class="fg">
      <label style="display:flex;align-items:center;gap:10px;text-transform:none;letter-spacing:0;font-size:.875rem;cursor:pointer">
        <input type="checkbox" name="featured" <?= isset($_POST['featured'])?'checked':'' ?> style="width:18px;height:18px;accent-color:#1a6fe8">
        Mark as Featured (appears in homepage featured section)
      </label>
    </div>
  </div>

  <div style="display:flex;gap:12px">
    <button type="submit" class="btn btn-blue btn-lg">💾 Save Product</button>
    <a href="products.php" class="btn btn-gray btn-lg">Cancel</a>
  </div>
</form>

<?php include '_footer.php'; ?>
