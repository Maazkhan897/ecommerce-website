<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../config.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your email and password.';
    } else {
        $stmt = mysqli_prepare($conn,
            "SELECT id, name, password, is_admin FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($row && $row['is_admin'] == 1 && password_verify($password, $row['password'])) {
            $_SESSION['admin_id']   = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid credentials or insufficient admin permissions.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login – Winter Fashion</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#0a3580 0%,#1a6fe8 60%,#3b8ff5 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
.wrap{display:flex;width:100%;max-width:900px;border-radius:20px;overflow:hidden;box-shadow:0 40px 100px rgba(0,0,0,.3)}
.brand{flex:1;background:rgba(255,255,255,.08);backdrop-filter:blur(10px);padding:60px 48px;color:#fff;display:flex;flex-direction:column;justify-content:center}
.brand h1{font-size:2.5rem;font-weight:800;margin-bottom:8px;letter-spacing:-1px}
.brand p.tagline{font-size:1.1rem;opacity:.85;margin-bottom:40px}
.feature{display:flex;align-items:center;gap:14px;margin-bottom:18px;font-size:.9rem;opacity:.85}
.feature-icon{width:36px;height:36px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0}
.form-side{background:#fff;width:420px;padding:52px 44px;display:flex;flex-direction:column;justify-content:center}
.admin-badge{display:inline-flex;align-items:center;gap:6px;background:#eff6ff;color:#1a6fe8;font-size:.72rem;font-weight:700;padding:5px 12px;border-radius:20px;text-transform:uppercase;letter-spacing:.06em;margin-bottom:28px;border:1px solid #bfdbfe}
.form-side h2{font-size:1.9rem;font-weight:800;color:#111;margin-bottom:6px}
.form-side p.sub{color:#6b7280;font-size:.875rem;margin-bottom:32px}
.fg{margin-bottom:18px}
.fg label{display:block;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#444;margin-bottom:7px}
.fg input{width:100%;padding:13px 16px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.9rem;font-family:inherit;outline:none;transition:all .2s;background:#fafbff}
.fg input:focus{border-color:#1a6fe8;box-shadow:0 0 0 3px rgba(26,111,232,.1);background:#fff}
.btn-login{width:100%;padding:15px;background:linear-gradient(135deg,#0a3580,#1a6fe8);color:#fff;border:none;border-radius:10px;font-size:1rem;font-weight:700;cursor:pointer;font-family:inherit;transition:opacity .2s;letter-spacing:.02em}
.btn-login:hover{opacity:.9}
.alert{border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:.875rem;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;font-weight:500}
.back{text-align:center;margin-top:22px;font-size:.85rem;color:#9ca3af}
.back a{color:#1a6fe8;font-weight:600;text-decoration:none}
.back a:hover{text-decoration:underline}
@media(max-width:680px){.brand{display:none}.form-side{width:100%;border-radius:20px}}
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <h1>⚙ Winter<br>Admin</h1>
    <p class="tagline">Manage your store with ease.</p>
    <div class="feature"><div class="feature-icon">📊</div><div>Real-time dashboard & analytics</div></div>
    <div class="feature"><div class="feature-icon">👕</div><div>Full product catalog management</div></div>
    <div class="feature"><div class="feature-icon">📦</div><div>Order tracking & status updates</div></div>
    <div class="feature"><div class="feature-icon">🔒</div><div>Secure, role-based access control</div></div>
  </div>
  <div class="form-side">
    <span class="admin-badge">🔒 Admin Access Only</span>
    <h2>Sign In</h2>
    <p class="sub">Enter your administrator credentials</p>

    <?php if ($error): ?>
      <div class="alert">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="fg">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="admin@winter.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
      </div>
      <div class="fg">
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn-login">🔑 Sign In to Dashboard</button>
    </form>
    <p class="back"><a href="../index.php">← Back to Store</a></p>
  </div>
</div>
</body>
</html>
