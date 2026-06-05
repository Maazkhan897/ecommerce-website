<?php
/**
 * One-time admin setup script.
 * Visit this page ONCE to create the admin user, then DELETE this file.
 */
require_once __DIR__ . '/db.php';

$messages = [];
$errors   = [];

// 1. Add is_admin column to users table (compatible with MySQL 5.7+ and MariaDB)
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'is_admin'");
if ($colCheck && mysqli_num_rows($colCheck) === 0) {
    $alter = mysqli_query($conn,
        "ALTER TABLE users ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0");
    if ($alter) {
        $messages[] = 'Column <code>is_admin</code> added to users table successfully.';
    } else {
        $errors[] = 'Could not alter users table: ' . mysqli_error($conn);
    }
} else {
    $messages[] = 'Column <code>is_admin</code> already exists in users table.';
}

// 2. Create / promote admin user
$adminEmail    = 'admin@winter.com';
$adminPassword = 'admin123';
$adminName     = 'Administrator';
$hash          = password_hash($adminPassword, PASSWORD_DEFAULT);

$check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($check, "s", $adminEmail);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) === 0) {
    $ins = mysqli_prepare($conn,
        "INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)");
    mysqli_stmt_bind_param($ins, "sss", $adminName, $adminEmail, $hash);
    if (mysqli_stmt_execute($ins)) {
        $messages[] = 'Admin user created successfully.';
    } else {
        $errors[] = 'Failed to create admin user: ' . mysqli_error($conn);
    }
} else {
    $upd = mysqli_prepare($conn, "UPDATE users SET is_admin = 1 WHERE email = ?");
    mysqli_stmt_bind_param($upd, "s", $adminEmail);
    mysqli_stmt_execute($upd);
    $messages[] = 'Existing admin user updated with admin privileges.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Setup – Winter Fashion</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  body{font-family:'DM Sans',sans-serif;background:#f0f4ff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
  .card{background:#fff;border-radius:16px;padding:48px;max-width:520px;width:100%;box-shadow:0 20px 60px rgba(26,111,232,.12)}
  h1{font-size:1.8rem;color:#0a3580;margin-bottom:8px}
  p.sub{color:#6b7280;margin-bottom:28px;font-size:.9rem}
  .msg{padding:12px 16px;border-radius:8px;margin-bottom:10px;font-size:.875rem}
  .msg.ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a}
  .msg.err{background:#fef2f2;border:1px solid #fecaca;color:#dc2626}
  .creds{background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:20px;margin:24px 0}
  .creds p{font-size:.9rem;margin-bottom:8px;color:#1e40af}
  .creds strong{color:#0a3580}
  .warn{background:#fefce8;border:1px solid #fde68a;border-radius:8px;padding:14px 16px;color:#92400e;font-size:.85rem;margin-top:20px}
  .btn{display:inline-block;padding:12px 28px;background:#1a6fe8;color:#fff;border-radius:8px;font-weight:700;text-decoration:none;font-size:.9rem}
</style>
</head>
<body>
<div class="card">
  <h1>⚙ Admin Setup</h1>
  <p class="sub">Winter Fashion – One-time admin configuration</p>

  <?php foreach ($messages as $m): ?>
    <div class="msg ok">✓ <?= $m ?></div>
  <?php endforeach; ?>
  <?php foreach ($errors as $e): ?>
    <div class="msg err">✗ <?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>

  <?php if (empty($errors)): ?>
  <div class="creds">
    <p><strong>Admin Login URL:</strong><br><code>http://localhost/ecommerce/admin/login.php</code></p>
    <p><strong>Email:</strong> admin@winter.com</p>
    <p><strong>Password:</strong> admin123</p>
  </div>
  <a href="admin/login.php" class="btn">Go to Admin Login →</a>
  <div class="warn">
    ⚠ <strong>Security:</strong> Delete this file (<code>admin_setup.php</code>) after setup to prevent unauthorized access.
  </div>
  <?php endif; ?>
</div>
</body>
</html>
