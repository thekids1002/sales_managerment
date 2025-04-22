-- Create database if not exists
CREATE DATABASE IF NOT EXISTS sale_management;
USE sale_management;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(191) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Create users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(191) UNIQUE,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Laptops', 'Portable computers for work and play'),
('Smartphones', 'Mobile phones with advanced features'),
('Audio', 'Headphones, speakers and audio equipment'),
('Accessories', 'Computer and phone accessories');

-- Insert sample data for testings
INSERT INTO products (category_id, name, description, price, stock_quantity) VALUES
(1, 'Laptop Dell XPS 13', 'High-performance laptop with Intel Core i7', 1200.00, 10),
(2, 'iPhone 13 Pro', 'Apple smartphone with A15 Bionic chip', 999.00, 15),
(2, 'Samsung Galaxy S21', 'Android smartphone with Exynos processor', 799.00, 20),
(4, 'Logitech MX Master 3', 'Wireless mouse with customizable buttons', 99.99, 30),
(3, 'Sony WH-1000XM4', 'Noise cancelling wireless headphones', 349.99, 25);

INSERT INTO customers (name, email, phone, address) VALUES
('John Doe', 'john.doe@example.com', '123-456-7890', '123 Main St, City'),
('Jane Smith', 'jane.smith@example.com', '098-765-4321', '456 Oak Ave, Town'),
('Robert Johnson', 'robert.j@example.com', '555-123-4567', '789 Pine Rd, Village');

-- Create admin user (password: admin123)
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$0yAkg.b534T46Xqvznhjo.CS1DQScvuWE3zG1cjt3ix.xs/NMDAEe', 'admin@example.com', 'admin'); 