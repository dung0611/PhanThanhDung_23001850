<?php
declare(strict_types=1);

// Mặc định cho XAMPP. Có thể sửa tại đây hoặc đặt biến môi trường DB_*.
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$database = getenv('DB_NAME') ?: 'th_buoi_09';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') === false ? '' : getenv('DB_PASSWORD');

return new PDO(
    "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);
