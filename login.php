<?php
require_once __DIR__ . '/db.php';
require_once 'config.php';
if (isLoggedIn()) redirect('index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your email and password.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id']   = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $goto = $_GET['redirect'] ?? 'index.php';
                redirect($goto);
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – Winter Fashion</title>
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
  background:#fff;border-radius:20px;padding:52px 48px;width:100%;max-width:440px;
  box-shadow:0 24px 64px rgba(26,111,232,0.1);
}
.auth-box h2{font-family:'Playfair Display',serif;font-size:2.2rem;color:#111;margin-bottom:6px}
.auth-sub{color:#888;font-size:0.9rem;margin-bottom:36px}
.form-group{margin-bottom:20px}
.form-group label{display:block;font-size:0.82rem;font-weight:700;color:#333;
  text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px}
.form-group input{
  width:100%;padding:13px 16px;border:1.5px solid #e5e7eb;border-radius:10px;
  font-size:0.9rem;font-family:inherit;outline:none;
  transition:border-color .2s,box-shadow .2s;background:#fafbff;
}
.form-group input:focus{border-color:#1a6fe8;box-shadow:0 0 0 4px rgba(26,111,232,.1);background:#fff}
.forgot{float:right;font-size:.8rem;color:#1a6fe8;font-weight:600;margin-top:-28px;display:block;text-align:right}
.btn-full{width:100%;padding:15px;font-size:1rem;border-radius:10px;margin-top:4px;letter-spacing:.03em;clear:both}
.auth-switch{text-align:center;margin-top:24px;font-size:0.9rem;color:#666}
.auth-switch a{color:#1a6fe8;font-weight:700}
.alert{border-radius:10px;padding:14px 18px;margin-bottom:20px;font-size:.875rem}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626}
@media(max-width:820px){.auth-brand{display:none}.auth-right{padding:24px}}
</style>
</head>
<body>
<div class="auth-page">

  <!-- Brand Side -->
  <div class="auth-brand">
    <div class="brand-logo">Winter</div>
    <div class="brand-tagline">Welcome Back, Fashionista!</div>
    <p class="brand-desc">Sign in to access your wishlist, order history, and exclusive member-only deals.</p>
    <div class="brand-perks">
      <div class="perk"><span class="perk-icon">👗</span> Access your saved wishlist</div>
      <div class="perk"><span class="perk-icon">📦</span> Track all your orders</div>
      <div class="perk"><span class="perk-icon">🎉</span> Unlock member discounts</div>
      <div class="perk"><span class="perk-icon">⚡</span> Faster checkout experience</div>
    </div>
  </div>

  <!-- Form Side -->
  <div class="auth-right">
    <div class="auth-box">
      <h2>Sign In</h2>
      <p class="auth-sub">Enter your credentials to access your account</p>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" placeholder="john@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Your password" required>
        </div>
        <button type="submit" class="btn-primary btn-full">Sign In</button>
      </form>
      <p class="auth-switch">Don't have an account? <a href="register.php">Create one free →</a></p>
    </div>
  </div>

</div>
</body>
</html>
