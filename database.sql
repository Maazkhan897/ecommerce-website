-- Winter Fashion Ecommerce Database
-- Run this in phpMyAdmin or MySQL CLI: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    gender ENUM('men','women','shoes','all') DEFAULT 'all'
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    category_id INT,
    image VARCHAR(255),
    stock INT DEFAULT 100,
    rating DECIMAL(2,1) DEFAULT 4.5,
    reviews_count INT DEFAULT 0,
    gender ENUM('men','women','shoes') NOT NULL,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    size VARCHAR(10) DEFAULT 'M',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_cart_item (user_id, product_id, size),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    shipping DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    shipping_name VARCHAR(100),
    shipping_email VARCHAR(150),
    shipping_address TEXT,
    shipping_city VARCHAR(100),
    shipping_zip VARCHAR(20),
    shipping_phone VARCHAR(20),
    payment_method VARCHAR(50) DEFAULT 'cod',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    size VARCHAR(10),
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Categories
INSERT INTO categories (name, slug, gender) VALUES
('Shirts',          'shirts',    'men'),
('Jackets',         'jackets',   'men'),
('Suits',           'suits',     'men'),
('T-Shirts',        't-shirts',  'men'),
('Trousers',        'trousers',  'men'),
('Accessories',     'accessories','all'),
('Dresses',         'dresses',   'women'),
('Tops',            'tops',      'women'),
('Sweaters',        'sweaters',  'women'),
('Skirts',          'skirts',    'women'),
('Sneakers',        'sneakers',  'shoes'),
('Boots',           'boots',     'shoes'),
('Heels',           'heels',     'shoes'),
('Flats',           'flats',     'shoes'),
('Sandals',         'sandals',   'shoes');

-- Products
INSERT INTO products (name, description, price, original_price, category_id, image, stock, rating, reviews_count, gender, featured) VALUES
-- Men
('Plaid Flannel Shirt',   'Classic plaid flannel shirt crafted from 100% premium cotton. Perfect for casual everyday wear with a timeless rugged look.',         55.00,  75.00,  1,  'shirt2.png',        50, 4.5, 24, 'men',   1),
('Grey Zip Hoodie',       'Premium grey zip-up hoodie with cozy fleece lining and kangaroo pocket. Ideal for cold weather layering.',                            72.00,  95.00,  2,  'greyziphoodie.png', 40, 4.7, 18, 'men',   1),
('Navy Blue Blazer',      'Sophisticated double-breasted navy blazer with tailored slim fit. Perfect for formal occasions and business settings.',               189.00, 250.00, 3,  'blazer.png',        25, 4.8, 32, 'men',   1),
('Classic White Tee',     'Essential crew-neck t-shirt in crisp white, made from soft cotton blend for all-day comfort and breathability.',                      29.00,  40.00,  4,  'whitetee.png',      100,4.3, 45, 'men',   0),
('Slim Fit Chinos',       'Modern slim fit chinos with stretch fabric providing all-day comfort and a sharp, contemporary look for any occasion.',               69.00,  90.00,  5,  'chinos.png',        45, 4.6, 29, 'men',   0),
('Wool Scarf',            'Luxurious merino wool scarf in sage green. Incredibly soft, warm, and effortlessly stylish for winter layering.',                     35.00,  50.00,  6,  'scarf.png',         60, 4.4, 12, 'men',   0),
-- Women
('Evening Gown',          'Stunning sequin-bodice evening gown with flowing chiffon skirt in dusty rose. Perfect for galas and special celebrations.',          149.00, 200.00, 7,  'evening.png',       20, 4.9, 56, 'women', 1),
('White Button Shirt',    'Crisp white button-down shirt for women with a tailored fit. A timeless wardrobe essential that pairs beautifully with everything.',  45.00,  65.00,  8,  'whitebutton.png',   55, 4.5, 38, 'women', 1),
('Christmas Sweater',     'Festive Christmas knit sweater featuring reindeer and snowflake pattern in red and white. Cozy, warm, and holiday-ready.',            65.00,  85.00,  9,  'christmas.png',     35, 4.6, 41, 'women', 0),
('Pink A-Line Skirt',     'Flowy pink tulle A-line midi skirt with comfortable elastic waistband. Elegant and versatile for any season.',                        52.00,  70.00,  10, 'pinkskirt.png',     40, 4.7, 27, 'women', 0),
('Silk Scarf',            'Luxurious ivory silk scarf, beautifully fluid and lightweight. Elevate any outfit with this timeless accessory.',                     38.00,  55.00,  6,  'silkscarf.png',     50, 4.5, 19, 'women', 0),
('Classic Handbag',       'Elegant cream leather shoulder bag with gold-tone hardware and detachable strap. Spacious, chic, and timeless.',                     120.00, 160.00, 6,  'handbag.png',       30, 4.8, 33, 'women', 1),
-- Shoes
('Fashloon Navy Sneaker', 'Stylish navy suede low-top sneakers with light blue accents and gum sole. Versatile and comfortable for everyday wear.',              89.00, 120.00, 11, 'fashloon.png',      60, 4.6, 47, 'shoes', 1),
('Canvas Low-Top',        'Classic white canvas platform low-top sneakers with retro red and blue stripe detail. A timeless casual essential.',                  65.00,  85.00, 11, 'canvas.png',        45, 4.5, 36, 'shoes', 1),
('Leather Chelsea Boot',  'Premium black leather Chelsea boots with elastic gore panels. Sleek, sophisticated, and built to last a lifetime.',                  175.00, 230.00, 12, 'leatherchelsea.png',30, 4.8, 52, 'shoes', 1),
('Stiletto Heels',        'Sleek black patent stiletto heels with delicate ankle strap detail and square toe. Perfect for formal dinners and events.',           95.00, 130.00, 13, 'stilleto.png',      25, 4.4, 21, 'shoes', 0),
('Ballet Flats',          'Classic white ballet flats with delicate bow detail and cushioned insole. Comfortable elegance for everyday wear.',                   58.00,  80.00, 14, 'ballet.png',        50, 4.7, 43, 'shoes', 0),
('Strappy Sandals',       'Tan leather strappy kitten-heel sandals with ankle strap closure. Chic, refined, and perfect for summer occasions.',                  68.00,  95.00, 15, 'strappy.png',       35, 4.5, 28, 'shoes', 0);
