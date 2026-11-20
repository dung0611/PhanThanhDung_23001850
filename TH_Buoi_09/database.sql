CREATE DATABASE IF NOT EXISTS `th_buoi_09`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `th_buoi_09`;

CREATE TABLE IF NOT EXISTS `products` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `price` INT NOT NULL,
    `stock` INT NOT NULL,
    `image` VARCHAR(255) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `chk_product_name` CHECK (CHAR_LENGTH(TRIM(`name`)) BETWEEN 1 AND 100),
    CONSTRAINT `chk_product_price` CHECK (`price` > 0),
    CONSTRAINT `chk_product_stock` CHECK (`stock` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12 sản phẩm mẫu ban đầu. Nhập lại file sẽ cập nhật các ID 1–12.
INSERT INTO `products` (`id`, `name`, `price`, `stock`) VALUES
    (1, 'Bút bi', 5000, 20),
    (2, 'Vở ô ly', 12000, 15),
    (3, 'Thước kẻ', 8000, 0),
    (4, 'Bút chì', 4000, 30),
    (5, 'Tẩy trắng', 3000, 18),
    (6, 'Bút dạ quang', 11000, 12),
    (7, 'Sổ tay', 25000, 9),
    (8, 'Kẹp giấy', 7000, 0),
    (9, 'Giấy ghi chú', 15000, 22),
    (10, 'Bìa hồ sơ', 6000, 16),
    (11, 'Bút mực', 18000, 8),
    (12, 'Hộp bút', 35000, 5)
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `price` = VALUES(`price`),
    `stock` = VALUES(`stock`);

-- 8 sản phẩm bổ sung. Kiểm tra theo tên để nhập lại file không tạo bản ghi trùng.
INSERT INTO `products` (`name`, `price`, `stock`)
SELECT seed.name, seed.price, seed.stock
FROM (
    SELECT 'Kéo học sinh' AS name, 22000 AS price, 0 AS stock
    UNION ALL SELECT 'Hồ dán', 9000, 25
    UNION ALL SELECT 'Băng keo trong', 14000, 16
    UNION ALL SELECT 'Ghim bấm', 12000, 30
    UNION ALL SELECT 'Bút xóa', 17000, 11
    UNION ALL SELECT 'Phấn viết bảng', 10000, 20
    UNION ALL SELECT 'Giấy A4', 65000, 7
    UNION ALL SELECT 'Compa', 28000, 0
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM `products` AS existing WHERE existing.name = seed.name);
