-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    in_stock TINYINT(1) NOT NULL DEFAULT 1,
    description TEXT,
    brand VARCHAR(255),
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Create product_gallery table for storing product images
CREATE TABLE IF NOT EXISTS product_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(255),
    image_url TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create attribute_sets table (for product attributes like Size, Color, etc.)
CREATE TABLE IF NOT EXISTS attribute_sets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(255),
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,  -- e.g., 'text' or 'swatch'
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create attributes table (each option inside an attribute set)
CREATE TABLE IF NOT EXISTS attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attribute_set_id INT,
    display_value VARCHAR(255),
    value VARCHAR(255),
    FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create prices table
CREATE TABLE IF NOT EXISTS prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    currency_label VARCHAR(10) NOT NULL,
    currency_symbol VARCHAR(10) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create order_items table to hold the items for each order.
-- The chosen_attributes column stores a JSON string (use TEXT for MySQL 5.6 compatibility)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id VARCHAR(255),
    quantity INT NOT NULL,
    chosen_attributes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;
