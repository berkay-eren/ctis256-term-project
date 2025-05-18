CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type VARCHAR(10) NOT NULL,  
    name VARCHAR(50) NOT NULL,      
    email VARCHAR(50) NOT NULL UNIQUE,
    pass VARCHAR(255),           
    city VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    verification_code VARCHAR(6) NOT NULL,
    is_verified INT(1) DEFAULT 0, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

// Insert sample data
INSERT INTO `users` (`id`, `user_type`, `name`, `email`, `pass`, `city`, `district`, `verification_code`, `is_verified`, `created_at`) VALUES
(6, 'market', 'Işık Dönger', 'example223038@gmail.com', '$2y$10$KMcKWAUSTZ6Cs8YwScHbXO.F.G5LOFuXaGoTnBUKd0X/hX6al.XSO', 'İzmir', 'Bornova', '583233', 1, '2025-05-18 19:37:43'),
(7, 'market', 'Berkay Eren', '256termproject@gmail.com', '$2y$10$naKEqixo7BSfdXCC1FwHOeZbbl79np4ZmkiGLZLCZJfyLHUcSvcXG', 'Ankara', 'Çankaya', '146567', 1, '2025-05-18 19:50:17');
COMMIT;