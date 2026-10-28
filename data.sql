
-- ============================================
-- KOPE SHOP DATABASE
-- One-to-Many Relationship
-- Parent: categories
-- Child: menu_items
-- ============================================

CREATE DATABASE IF NOT EXISTS kope_shop_db;

USE kope_shop_db;

-- Parent table: Categories
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL
);

-- Child table: Menu Items
CREATE TABLE IF NOT EXISTS menu_items (
    menu_item_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    is_available TINYINT(1) DEFAULT 1,
    INDEX fk_menu_items_categories (category_id),
    CONSTRAINT fk_menu_items_categories_fk
        FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);
