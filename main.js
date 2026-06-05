// ===== Product Filter Tabs =====
function filterProducts(cat, btn) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.product-card').forEach(card => {
    const match = cat === 'all' || card.dataset.cat === cat;
    card.classList.toggle('hidden', !match);
  });
}

// ===== Mobile Nav Toggle =====
function toggleMenu() {
  const nav = document.getElementById('navLinks');
  if (nav) nav.classList.toggle('open');
}

// Close nav when clicking outside
document.addEventListener('click', e => {
  const nav  = document.getElementById('navLinks');
  const btn  = document.querySelector('.hamburger');
  if (nav && nav.classList.contains('open') && !nav.contains(e.target) && e.target !== btn) {
    nav.classList.remove('open');
  }
});

// ===== Product Detail Tabs =====
function showTab(tabId, btn) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  const el = document.getElementById(tabId);
  if (el) el.classList.add('active');
  if (btn) btn.classList.add('active');
}

// ===== Quantity Selector =====
function changeQty(delta) {
  const display = document.getElementById('qty');
  if (!display) return;
  let val = parseInt(display.textContent) + delta;
  if (val < 1) val = 1;
  display.textContent = val;
}

// ===== Global Toast Notification =====
function showToast(msg, isError = false) {
  let t = document.getElementById('global-toast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'global-toast';
    Object.assign(t.style, {
      position: 'fixed', bottom: '28px', right: '28px',
      padding: '14px 22px', borderRadius: '12px',
      fontWeight: '600', fontSize: '.875rem',
      zIndex: '9999', transition: 'all .35s cubic-bezier(.16,1,.3,1)',
      boxShadow: '0 8px 32px rgba(0,0,0,.15)',
      transform: 'translateY(12px)', opacity: '0',
      fontFamily: 'DM Sans, sans-serif',
      minWidth: '220px',
    });
    document.body.appendChild(t);
  }
  t.style.background = isError ? '#fef2f2' : '#f0fdf4';
  t.style.color      = isError ? '#dc2626' : '#16a34a';
  t.style.border     = `1px solid ${isError ? '#fecaca' : '#bbf7d0'}`;
  t.textContent = (isError ? '✗ ' : '✓ ') + msg;
  t.style.opacity = '1';
  t.style.transform = 'translateY(0)';
  clearTimeout(t._timer);
  t._timer = setTimeout(() => {
    t.style.opacity = '0';
    t.style.transform = 'translateY(12px)';
  }, 2800);
}

// ===== Cart Badge Animation =====
function updateCartBadge(count) {
  const badge = document.getElementById('cart-badge');
  if (!badge) return;
  badge.textContent = count;
  badge.style.animation = 'none';
  badge.offsetHeight; // reflow
  badge.style.animation = 'badgePop .4s ease';
}

// ===== Scroll-reveal animation =====
function revealOnScroll() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.style.opacity = '1';
        e.target.style.transform = 'translateY(0)';
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.product-card, .shop-product-card, .feature, .cat-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity .5s ease, transform .5s ease';
    observer.observe(el);
  });
}

// ===== Sticky navbar shadow on scroll =====
window.addEventListener('scroll', () => {
  const nav = document.querySelector('.navbar');
  if (nav) {
    nav.style.boxShadow = window.scrollY > 10
      ? '0 4px 24px rgba(0,0,0,0.1)'
      : '0 1px 8px rgba(0,0,0,0.06)';
  }
});

// ===== Init =====
document.addEventListener('DOMContentLoaded', () => {
  revealOnScroll();

  // Animate badge on load if count > 0
  const badge = document.getElementById('cart-badge');
  if (badge && parseInt(badge.textContent) > 0) {
    badge.style.animation = 'badgePop .5s ease .3s both';
  }
});
