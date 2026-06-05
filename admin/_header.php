<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> – Winter Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:#f0f4ff;display:flex;min-height:100vh;color:#111}

/* ── Sidebar ── */
.sidebar{width:260px;background:linear-gradient(180deg,#071e5a 0%,#0a3580 50%,#0d47a1 100%);min-height:100vh;position:fixed;top:0;left:0;z-index:100;display:flex;flex-direction:column}
.sidebar-logo{padding:26px 24px 22px;border-bottom:1px solid rgba(255,255,255,.1)}
.sidebar-logo h1{font-size:1.55rem;font-weight:800;color:#fff;letter-spacing:-.5px}
.sidebar-logo span{font-size:.7rem;color:rgba(255,255,255,.5);display:block;margin-top:2px;text-transform:uppercase;letter-spacing:.08em}
.sidebar-nav{padding:16px 12px;flex:1;overflow-y:auto}
.nav-section{margin-bottom:6px}
.nav-section-label{font-size:.63rem;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.35);padding:10px 12px 6px;font-weight:700}
.nav-link{display:flex;align-items:center;gap:11px;padding:10px 12px;color:rgba(255,255,255,.7);text-decoration:none;border-radius:8px;font-size:.855rem;font-weight:500;transition:all .18s;margin-bottom:1px}
.nav-link:hover{background:rgba(255,255,255,.1);color:#fff}
.nav-link.active{background:linear-gradient(135deg,rgba(255,255,255,.18),rgba(255,255,255,.08));color:#fff;font-weight:600;box-shadow:inset 0 0 0 1px rgba(255,255,255,.12)}
.nav-icon{font-size:1rem;width:22px;text-align:center;flex-shrink:0}
.nav-badge{margin-left:auto;background:rgba(255,255,255,.2);color:#fff;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:10px}
.sidebar-footer{padding:18px 20px;border-top:1px solid rgba(255,255,255,.1)}
.admin-info{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.admin-avatar{width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#1a6fe8,#3b8ff5);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:1rem;flex-shrink:0}
.admin-name{font-size:.855rem;color:#fff;font-weight:600;line-height:1.3}
.admin-role{font-size:.7rem;color:rgba(255,255,255,.5)}
.btn-logout{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:10px 16px;background:rgba(255,255,255,.08);color:rgba(255,255,255,.75);border:1px solid rgba(255,255,255,.12);border-radius:8px;font-size:.82rem;font-weight:600;cursor:pointer;text-align:center;text-decoration:none;transition:all .2s}
.btn-logout:hover{background:rgba(220,38,38,.25);border-color:rgba(220,38,38,.4);color:#fff}

/* ── Main ── */
.admin-main{margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh}
.topbar{background:#fff;padding:15px 32px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;gap:16px}
.topbar-left h2{font-size:1.1rem;font-weight:700;color:#111}
.topbar-breadcrumb{font-size:.75rem;color:#9ca3af;margin-top:2px}
.topbar-breadcrumb a{color:#1a6fe8;text-decoration:none}
.topbar-actions{display:flex;align-items:center;gap:10px}
.topbar-link{font-size:.8rem;color:#1a6fe8;text-decoration:none;padding:7px 14px;border:1px solid #bfdbfe;border-radius:6px;font-weight:600;background:#eff6ff;transition:all .2s}
.topbar-link:hover{background:#1a6fe8;color:#fff;border-color:#1a6fe8}
.content{padding:28px 32px;flex:1}

/* ── Cards ── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px}
.stat-card{background:#fff;border-radius:14px;padding:24px 20px;border:1px solid #e5e7eb;position:relative;overflow:hidden;transition:box-shadow .2s}
.stat-card:hover{box-shadow:0 8px 30px rgba(0,0,0,.08)}
.stat-card::after{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:14px 14px 0 0}
.stat-card.c-blue::after{background:linear-gradient(90deg,#1a6fe8,#3b8ff5)}
.stat-card.c-green::after{background:linear-gradient(90deg,#16a34a,#4ade80)}
.stat-card.c-purple::after{background:linear-gradient(90deg,#7c3aed,#a78bfa)}
.stat-card.c-orange::after{background:linear-gradient(90deg,#d97706,#fbbf24)}
.stat-icon{font-size:2.1rem;margin-bottom:14px;display:block}
.stat-value{font-size:1.9rem;font-weight:800;color:#111;line-height:1;letter-spacing:-.5px}
.stat-label{font-size:.78rem;color:#6b7280;margin-top:5px;font-weight:500}
.stat-sub{font-size:.75rem;color:#9ca3af;margin-top:8px}
.stat-sub.up{color:#16a34a}

/* ── Table card ── */
.tcard{background:#fff;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;margin-bottom:24px}
.tcard-header{padding:18px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;gap:12px}
.tcard-header h3{font-size:.95rem;font-weight:700;color:#111}
.tcard-header p{font-size:.78rem;color:#9ca3af;margin-top:2px}
.twrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead th{padding:11px 16px;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#6b7280;background:#f9fafb;border-bottom:1px solid #e5e7eb;white-space:nowrap}
tbody td{padding:14px 16px;font-size:.855rem;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover td{background:#fafbff}
.timg{width:46px;height:46px;border-radius:8px;object-fit:contain;background:#f8faff;border:1px solid #e5e7eb;padding:3px}

/* ── Status badges ── */
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap}
.badge-pending{background:#fef3c7;color:#d97706}
.badge-processing{background:#dbeafe;color:#1d4ed8}
.badge-shipped{background:#ede9fe;color:#7c3aed}
.badge-delivered{background:#dcfce7;color:#16a34a}
.badge-cancelled{background:#fee2e2;color:#dc2626}

/* ── Buttons ── */
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:7px;font-size:.78rem;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:all .18s;font-family:inherit;white-space:nowrap}
.btn-blue{background:#1a6fe8;color:#fff}.btn-blue:hover{background:#1558c0}
.btn-green{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}.btn-green:hover{background:#16a34a;color:#fff;border-color:#16a34a}
.btn-red{background:#fef2f2;color:#dc2626;border:1px solid #fecaca}.btn-red:hover{background:#dc2626;color:#fff;border-color:#dc2626}
.btn-gray{background:#f9fafb;color:#374151;border:1px solid #e5e7eb}.btn-gray:hover{background:#e5e7eb}
.btn-lg{padding:12px 24px;font-size:.9rem;border-radius:9px}

/* ── Form card ── */
.fcard{background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:28px 32px;margin-bottom:24px}
.fcard-title{font-size:.95rem;font-weight:700;color:#111;margin-bottom:22px;padding-bottom:16px;border-bottom:1px solid #f3f4f6}
.fgrid2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.fgrid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}
.fg{margin-bottom:18px}
.fg label{display:block;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#555;margin-bottom:7px}
.fg input,.fg select,.fg textarea{width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.875rem;font-family:inherit;outline:none;transition:border-color .2s,box-shadow .2s;background:#fafbff}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:#1a6fe8;box-shadow:0 0 0 3px rgba(26,111,232,.08);background:#fff}
.fg textarea{resize:vertical;min-height:96px}
.fg select{cursor:pointer}

/* ── Alert ── */
.alert{padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:.875rem;font-weight:500}
.alert-ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a}
.alert-err{background:#fef2f2;border:1px solid #fecaca;color:#dc2626}

/* ── Page header ── */
.page-hdr{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px}
.page-hdr h1{font-size:1.4rem;font-weight:800;color:#111}
.page-hdr p{font-size:.82rem;color:#6b7280;margin-top:3px}

/* ── Misc ── */
.empty{text-align:center;padding:60px 24px;color:#9ca3af}
.empty-icon{font-size:3rem;margin-bottom:12px}
.stock-low{color:#dc2626;font-weight:700}
.stock-ok{color:#16a34a}
.text-muted{color:#9ca3af;font-size:.82rem}
.fw{font-weight:700}
.two-col{display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start}
.detail-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6;font-size:.875rem}
.detail-row:last-child{border-bottom:none}
.detail-row .label{color:#6b7280;font-weight:500}
.detail-row .val{font-weight:600;color:#111;text-align:right}

@media(max-width:1200px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){.two-col{grid-template-columns:1fr}}
@media(max-width:768px){.sidebar{transform:translateX(-100%)}.admin-main{margin-left:0}.content{padding:20px 16px}.topbar{padding:12px 16px}}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <h1>⚙ Winter</h1>
    <span>Administration Panel</span>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">
      <div class="nav-section-label">Overview</div>
      <a href="index.php" class="nav-link <?= ($activePage??'')==='dashboard'?'active':'' ?>">
        <span class="nav-icon">📊</span> Dashboard
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-section-label">Catalog</div>
      <a href="products.php" class="nav-link <?= ($activePage??'')==='products'?'active':'' ?>">
        <span class="nav-icon">👕</span> Manage Products
      </a>
      <a href="add_product.php" class="nav-link <?= ($activePage??'')==='add_product'?'active':'' ?>">
        <span class="nav-icon">➕</span> Add Product
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-section-label">Sales</div>
      <a href="orders.php" class="nav-link <?= ($activePage??'')==='orders'?'active':'' ?>">
        <span class="nav-icon">📦</span> Manage Orders
      </a>
    </div>
    <div class="nav-section">
      <div class="nav-section-label">Store</div>
      <a href="../index.php" class="nav-link" target="_blank">
        <span class="nav-icon">🌐</span> View Storefront
      </a>
    </div>
  </nav>

  <div class="sidebar-footer">
    <div class="admin-info">
      <div class="admin-avatar"><?= strtoupper(substr($adminName, 0, 1)) ?></div>
      <div>
        <div class="admin-name"><?= htmlspecialchars($adminName) ?></div>
        <div class="admin-role">Administrator</div>
      </div>
    </div>
    <a href="logout.php" class="btn-logout">🚪 Sign Out</a>
  </div>
</aside>

<!-- MAIN WRAPPER -->
<main class="admin-main">
  <div class="topbar">
    <div class="topbar-left">
      <h2><?= htmlspecialchars($pageTitle ?? 'Admin') ?></h2>
      <div class="topbar-breadcrumb">
        <a href="index.php">Admin</a> / <?= htmlspecialchars($pageTitle ?? '') ?>
      </div>
    </div>
    <div class="topbar-actions">
      <a href="../index.php" class="topbar-link" target="_blank">🛍 View Store</a>
    </div>
  </div>
  <div class="content">
