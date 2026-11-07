CREATE DATABASE IF NOT EXISTS signage_business CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE signage_business;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    email VARCHAR(120),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    unit VARCHAR(30) NOT NULL DEFAULT 'sq.ft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS quotations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quotation_no VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT NULL,
    project VARCHAR(255),
    subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
    installation DECIMAL(14,2) NOT NULL DEFAULT 0,
    delivery DECIMAL(14,2) NOT NULL DEFAULT 0,
    discount DECIMAL(14,2) NOT NULL DEFAULT 0,
    tax_rate DECIMAL(7,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
    total DECIMAL(14,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS quotation_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT NOT NULL,
    material_id INT NULL,
    material_name VARCHAR(120) NOT NULL,
    width DECIMAL(10,2) NOT NULL DEFAULT 0,
    height DECIMAL(10,2) NOT NULL DEFAULT 0,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    area DECIMAL(14,2) NOT NULL DEFAULT 0,
    amount DECIMAL(14,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE SET NULL
);

INSERT IGNORE INTO materials (name, price, unit) VALUES
('Lightbox',25000,'sq.ft'),
('All-in-One Board',18000,'sq.ft'),
('PV Board',12000,'sq.ft'),
('Sticker',8000,'sq.ft'),
('Mesh',9000,'sq.ft');

INSERT IGNORE INTO users (username,password,full_name)
VALUES ('admin', '$2y$12$hG1YFZwnYk1SrN7xb/cWlOvjHSZfbO/uPdviaLwIINortM8dXPwFu', 'Administrator');
