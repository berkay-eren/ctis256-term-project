DROP TABLE IF EXISTS `products`;
CREATE TABLE products (
    market_id INT NOT NULL,
    id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    normal_price DECIMAL(10,2) NOT NULL,
    discounted_price DECIMAL(10,2),
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255) NOT NULL,
    expiration_date DATE NOT NULL,
    PRIMARY KEY (market_id, id),
    FOREIGN KEY (market_id) REFERENCES users(id) ON DELETE CASCADE
);