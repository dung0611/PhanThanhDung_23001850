<?php

// Mặc định dùng XAMPP. Có thể đổi trực tiếp hoặc thiết lập biến môi trường DB_*.
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'shopping_cart';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') === false ? '' : getenv('DB_PASSWORD');
$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

try {
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log('Lỗi kết nối CSDL TH5: ' . $e->getMessage());
    http_response_code(500);
    exit('Không thể kết nối cơ sở dữ liệu. Vui lòng khởi động MySQL, import database.sql và kiểm tra common/dbConnect.php.');
}

function getDbConnection(): PDO
{
    global $conn;
    return $conn;
}
