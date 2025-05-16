DROP TABLE IF EXISTS shopping_cart;
CREATE TABLE shopping_cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consumer_id INT NOT NULL,
    market_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (consumer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (market_id, product_id) REFERENCES products(market_id, id) ON DELETE CASCADE
);
