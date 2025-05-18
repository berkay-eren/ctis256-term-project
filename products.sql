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

INSERT INTO `products` (`market_id`, `id`, `title`, `normal_price`, `discounted_price`, `stock`, `image_url`, `expiration_date`) VALUES
(7, 1, 'ProductSearch', 10.00, 10.00, 5, 'uploads/7_1_1747597882.jpg', '2025-05-20'),
(6, 4, 'Product4', 10.00, 10.00, 3, 'uploads/6_4_1747597191.jpg', '2025-05-17'),
(6, 3, 'Product3', 10.00, 10.00, 0, 'uploads/6_3_1747597176.jpg', '2025-05-15'),
(6, 2, 'Product2', 13.00, 8.00, 10, 'uploads/6_2_1747597159.jpeg', '2025-05-24'),
(6, 1, 'Product1', 15.00, 12.00, 5, 'uploads/6_1_1747597141.jpg', '2025-05-19'),
(7, 2, 'ProductSearch', 5.00, 5.00, 5, 'uploads/7_2_1747597892.jpg', '2025-05-25'),
(7, 3, 'ProductSearch', 5.00, 5.00, 5, 'uploads/7_3_1747597902.jpg', '2025-05-25'),
(7, 4, 'ProductSearch', 5.00, 5.00, 5, 'uploads/7_4_1747597912.jpg', '2025-05-25'),
(7, 5, 'ProductSearch', 5.00, 5.00, 5, 'uploads/7_5_1747597923.jpg', '2025-05-25'),
(7, 6, 'ProductSearch', 5.00, 5.00, 5, 'uploads/7_6_1747597933.jpg', '2025-05-25'),
(7, 7, 'ProductSearch', 5.00, 5.00, 5, 'uploads/7_7_1747598029.jpg', '2025-05-25');
COMMIT;