-- CREATE DATABASE IF NOT EXISTS scandiweb_test CHARACTER SET utf8mb4;
-- USE scandiweb_test;

CREATE TABLE categories (
    name VARCHAR(100) NOT NULL,
    PRIMARY KEY (name)
);

CREATE TABLE products (
    id          VARCHAR(100)  NOT NULL,
    name        VARCHAR(255)  NOT NULL,
    in_stock    TINYINT(1)    NOT NULL DEFAULT 1,
    description TEXT,
    category    VARCHAR(100)  NOT NULL,
    brand       VARCHAR(255),
    PRIMARY KEY (id),
    FOREIGN KEY (category) REFERENCES categories(name)
);

CREATE TABLE product_gallery (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    product_id  VARCHAR(100)  NOT NULL,
    image_url   TEXT          NOT NULL,
    sort_order  INT           NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE attributes (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    product_id  VARCHAR(100)  NOT NULL,
    name        VARCHAR(100)  NOT NULL,
    type        VARCHAR(50)   NOT NULL DEFAULT 'text',
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE attribute_items (
    id                INT           AUTO_INCREMENT PRIMARY KEY,
    attribute_id  INT           NOT NULL,
    display_value     VARCHAR(255)  NOT NULL,
    value             VARCHAR(255)  NOT NULL,
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE
);

CREATE TABLE prices (
    id              INT            AUTO_INCREMENT PRIMARY KEY,
    product_id      VARCHAR(100)   NOT NULL,
    amount          DECIMAL(10,2)  NOT NULL,
    currency_label  VARCHAR(10)    NOT NULL DEFAULT 'USD',
    currency_symbol VARCHAR(5)     NOT NULL DEFAULT '$',
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    selected_attributes JSON,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);