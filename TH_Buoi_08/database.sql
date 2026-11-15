CREATE DATABASE IF NOT EXISTS `th_buoi_08`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `th_buoi_08`;

CREATE TABLE IF NOT EXISTS `products` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `price` INT NOT NULL,
    `stock` INT NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `chk_product_name` CHECK (CHAR_LENGTH(TRIM(`name`)) BETWEEN 1 AND 100),
    CONSTRAINT `chk_product_price` CHECK (`price` > 0),
    CONSTRAINT `chk_product_stock` CHECK (`stock` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chỉ nhập dữ liệu mẫu khi cài bài. Nhập lại file sẽ đặt lại các ID 1, 2, 3.
INSERT INTO `products` (`id`, `name`, `price`, `stock`) VALUES
    (1, 'Bút bi', 5000, 20),
    (2, 'Vở ô ly', 12000, 15),
    (3, 'Thước kẻ', 8000, 0)
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `price` = VALUES(`price`),
    `stock` = VALUES(`stock`);
