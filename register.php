<?php
require_once __DIR__ . '/db.php';
require_once 'config.php';
if (isLoggedIn()) redirect('index.php');

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize($_POST['name'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($name))                               $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
    if (strlen($password) < 6)                      $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm)                      $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = 'That email address is already registered.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($ins, "sss", $name, $email, $hashed);
            if (mysqli_stmt_execute($ins)) {
                $success = true;
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register – Winter Fashion</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
.auth-page{min-height:100vh;display:flex;background:#f0f4ff}
.auth-brand{
  width:44%;background:linear-gradient(145deg,#0a3580 0%,#1a6fe8 55%,#3b8ff5 100%);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:64px 48px;color:#fff;position:relative;overflow:hidden;
}
.auth-brand::before{content:'';position:absolute;width:500px;height:500px;border-radius:50%;
  background:rgba(255,255,255,0.04);top:-180px;right:-180px}
.auth-brand::after{content:'';position:absolute;width:350px;height:350px;border-radius:50%;
  background:rgba(255,255,255,0.06);bottom:-120px;left:-120px}
.brand-logo{font-family:'Playfair Display',serif;font-size:3rem;font-weight:900;letter-spacing:-1px;margin-bottom:24px}
.brand-tagline{font-size:1.8rem;font-weight:700;line-height:1.3;margin-bottom:16px}
.brand-desc{font-size:0.95rem;opacity:.85;line-height:1.8;max-width:300px;margin-bottom:36px}
.brand-perks{display:flex;flex-direction:column;gap:14px}
.perk{display:flex;align-items:center;gap:12px;font-size:0.9rem;opacity:.9}
.perk-icon{font-size:1.3rem;width:32px;text-align:center}

.auth-right{flex:1;display:flex;align-items:center;justify-content:center;padding:40px}
.auth-box{
  background:#fff;border-radius:20px;padding:52px 48px;width:100%;max-width:480px;
  box-shadow:0 24px 64px rgba(26,111,232,0.1);
}
.auth-box h2{font-family:'Playfair Display',serif;font-size:2.2rem;color:#111;margin-bottom:6px}
.auth-sub{color:#888;font-size:0.9rem;margin-bottom:36px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-group{margin-bottom:20px}
.form-group label{display:block;font-size:0.82rem;font-weight:700;color:#333;
  text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px}
.form-group input{
  width:100%;padding:13px 16px;border:1.5px solid #e5e7eb;border-radius:10px;
  font-size:0.9rem;font-family:inherit;outline:none;
  transition:border-color .2s,box-shadow .2s;background:#fafbff;
}
.form-group input:focus{border-color:#1a6fe8;box-shadow:0 0 0 4px rgba(26,111,232,.1);background:#fff}
.btn-full{width:100%;padding:15px;font-size:1rem;border-radius:10px;margin-top:4px;letter-spacing:.03em}
.auth-divider{text-align:center;color:#bbb;font-size:0.8rem;margin:20px 0;position:relative}
.auth-divider::before,.auth-divider::after{content:'';position:absolute;top:50%;width:42%;height:1px;background:#e5e7eb}
.auth-divider::before{left:0}.auth-divider::after{right:0}
.auth-switch{text-align:center;margin-top:24px;font-size:0.9rem;color:#666}
.auth-switch a{color:#1a6fe8;font-weight:700}
.alert{border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:.875rem}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626}
.alert-error li{margin-left:16px;margin-bottom:3px}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;font-weight:600}
.alert-success a{color:#15803d;text-decoration:underline}
@media(max-width:820px){.auth-brand{display:none}.auth-right{padding:24px}}
</style>
</head>
<body>
<div class="auth-page">

  <!-- Brand Side -->
  <div class="auth-brand">
    <div class="brand-logo">Winter</div>
    <div class="brand-tagline">Discover Your Perfect Style</div>
    <p class="brand-desc">Join thousands of fashion lovers who trust Winter for their curated wardrobe collections.</p>
    <div class="brand-perks">
      <div class="perk"><span class="perk-icon">🚚</span> Free shipping on orders above $50</div>
      <div class="perk"><span class="perk-icon">🔄</span> 30-day hassle-free returns</div>
      <div class="perk"><span class="perk-icon">🔒</span> Secure &amp; encrypted payments</div>
      <div class="perk"><span class="perk-icon">⭐</span> Exclusive member discounts</div>
      <div class="perk"><span class="perk-icon">🎁</span> Early access to new collections</div>
    </div>
  </div>

  <!-- Form Side -->
  <div class="auth-right">
    <div class="auth-box">
      <h2>Create Account</h2>
      <p class="auth-sub">Sign up and start shopping today — it's free!</p>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success">🎉 Account created! <a href="login.php">Click here to login →</a></div>
      <?php else: ?>

      <form method="POST">
        <div class="form-row">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="John Doe"
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="john@example.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Min 6 characters" required>
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
          </div>
        </div>
        <button type="submit" class="btn-primary btn-full">Create My Account</button>
      </form>
      <p class="auth-switch">Already have an account? <a href="login.php">Sign in here</a></p>

      <?php endif; ?>
    </div>
  </div>

</div>
</body>
</html>
