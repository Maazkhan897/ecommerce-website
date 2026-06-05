# 🛍️ Winter — Fashion E-Commerce Web Application

A fully functional, multi-page e-commerce web application built as a semester project for the Web Technologies course at COMSATS University Islamabad. The platform supports end-to-end online shopping — from browsing products to placing orders — along with a complete admin panel for store management.

---

## 📌 Project Overview

**Winter** is a fashion-focused e-commerce platform covering Men's, Women's, and Shoes collections. It supports user registration and login, product browsing with filters, cart management, order placement, and a full admin dashboard.

---

## ✨ Features

| Feature | Status |
|---------|--------|
| User Registration & Login | ✅ Complete |
| Dynamic Product Listing | ✅ Complete |
| Product Search & Category Filter | ✅ Complete |
| Product Detail Page with Reviews | ✅ Complete |
| Shopping Cart (Session-based) | ✅ Complete |
| Checkout & Order Placement | ✅ Complete |
| Order History (per user) | ✅ Complete |
| Admin Dashboard with Stats | ✅ Complete |
| Admin — Manage Products (CRUD) | ✅ Complete |
| Admin — Manage Orders & Status | ✅ Complete |
| Responsive Design | ✅ Complete |
| Secure Password Hashing | ✅ Complete |
| SQL Injection Prevention | ✅ Complete |

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3 (Flexbox/Grid), Vanilla JavaScript |
| Backend | PHP 8.x (Procedural) |
| Database | MySQL (InnoDB, Prepared Statements) |
| Local Server | XAMPP / Apache |
| Dev Tools | VS Code, phpMyAdmin |

---

## 🗂️ Project Structure

```
ecommerce/
├── index.php                  # Homepage — hero banner & featured products
├── login.php                  # User login
├── register.php               # User registration
├── products.php               # All products with filter/search
├── product-detail.php         # Single product view & add-to-cart
├── cart.php                   # Shopping cart
├── checkout.php               # Order placement & payment form
├── orders.php                 # User order history
├── place_order.php            # Order processing logic
├── add_to_cart.php            # Cart AJAX handler
│
├── admin/
│   ├── index.php              # Admin dashboard (stats)
│   ├── products.php           # Manage products list
│   ├── add_product.php        # Add new product
│   ├── edit_product.php       # Edit existing product
│   ├── orders.php             # View & update orders
│   ├── order_detail.php       # Order detail view
│   ├── auth.php               # Admin session guard
│   └── admin_setup.php        # One-time admin user setup (delete after use)
│
├── includes/
│   ├── db.php                 # Database connection
│   └── config.php             # Session helpers, utility functions
│
├── css/
│   └── style.css              # Global stylesheet
│
├── js/
│   └── main.js                # Client-side interactivity
│
├── uploads/                   # Product images
└── database.sql               # Full DB schema + seed data
```

---

## 🗄️ Database Schema

The MySQL database (`ecommerce`) contains 6 tables:

| Table | Description |
|-------|-------------|
| `users` | Registered customer accounts |
| `categories` | Product categories (men/women/shoes) |
| `products` | Product catalog with pricing, stock, ratings |
| `cart` | Per-user cart items |
| `orders` | Customer orders with shipping info |
| `order_items` | Line items linked to each order |

All tables use the InnoDB engine with proper foreign key constraints.

---

## 🚀 Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (PHP 8.x + MySQL + Apache)
- A browser

### Installation

1. **Clone the repository** into your XAMPP `htdocs` folder:
   ```bash
   git clone https://github.com/your-username/winter-ecommerce.git
   cd xampp/htdocs/winter-ecommerce
   ```

2. **Import the database:**
   - Open [phpMyAdmin](http://localhost/phpmyadmin)
   - Create a new database named `ecommerce`
   - Import `database.sql`

   Or via CLI:
   ```bash
   mysql -u root -p ecommerce < database.sql
   ```

3. **Configure the DB connection** in `includes/db.php`:
   ```php
   $conn = mysqli_connect("localhost", "root", "", "ecommerce", 3307);
   ```
   Adjust the host, user, password, and port to match your setup.

4. **Start Apache and MySQL** from the XAMPP Control Panel.

5. **Create the admin account:**
   Visit `http://localhost/winter-ecommerce/admin/admin_setup.php` once in your browser.
   > ⚠️ Delete `admin_setup.php` after running it.

6. **Open the app:**
   ```
   http://localhost/winter-ecommerce/
   ```

### Default Admin Credentials
```
Email:    admin@winter.com
Password: admin123
```

---

## 🔐 Security Practices

- Passwords stored using PHP `password_hash()` (bcrypt) — never plain text
- Login validated with `password_verify()`
- All user inputs sanitized with `mysqli_real_escape_string()`
- Database queries use **MySQLi Prepared Statements** throughout to prevent SQL injection
- Admin panel protected via session-based role checking (`is_admin` flag) on every page load

---

## 📸 Pages Overview

| Page | Description |
|------|-------------|
| **Homepage** | Hero banner, featured products, category links |
| **Product Listing** | Grid view with category tabs, price range slider, size filter |
| **Product Detail** | Images, size selector, reviews & rating breakdown, related products |
| **Cart** | Item list, quantity controls, order summary |
| **Checkout** | Shipping form, payment method (COD / Card), order total |
| **Order Success** | Confirmation with order ID, items, and shipping details |
| **Admin Dashboard** | Total orders, revenue, users, products, low-stock alerts |

---

## ⚠️ Known Limitations

- Payment gateway is simulated (Cash on Delivery and Card are display-only; no real payment processing)
- Image uploads require the `/uploads/` directory to have write permissions
- Designed for local development (XAMPP); deployment to a live server requires additional configuration

---

## 📚 References

- [PHP Official Documentation](https://www.php.net/manual/en/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [W3Schools](https://www.w3schools.com)
- [MDN Web Docs](https://developer.mozilla.org)
- [phpMyAdmin Docs](https://docs.phpmyadmin.net)

---

## 📄 License

This project was developed for academic purposes as part of the Web Technologies course at COMSATS University Islamabad. Not licensed for commercial use.
