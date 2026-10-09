

CREATE DATABASE IF NOT EXISTS `shopping_cart` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `shopping_cart`;

-- Tạo bảng products
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu (9 sản phẩm)
INSERT INTO `products` (`id`, `name`, `price`, `quantity`) VALUES
(1, 'Laptop ASUS Vivobook', 15000000.00, 10),
(2, 'Chuột không dây Rapoo', 500000.00, 25),
(3, 'Bàn phím cơ Akko', 1200000.00, 15),
(4, 'Tai nghe Bluetooth JBL', 850000.00, 20),
(5, 'Màn hình LG 24 inch', 4500000.00, 8),
(6, 'Ổ cứng SSD Kingston 500GB', 950000.00, 12),
(7, 'Loa Bluetooth Anker', 1500000.00, 18),
(8, 'Webcam Logitech C270', 650000.00, 14),
(9, 'USB SanDisk 64GB', 220000.00, 30)
ON DUPLICATE KEY UPDATE 
    `name` = VALUES(`name`), 
    `price` = VALUES(`price`), 
    `quantity` = VALUES(`quantity`);
