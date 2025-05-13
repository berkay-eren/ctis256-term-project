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