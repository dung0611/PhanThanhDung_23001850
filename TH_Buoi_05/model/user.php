<?php

require_once __DIR__ . '/../common/dbConnect.php';

function getUserByUsername(string $username)
{
    $stmt = getDbConnection()->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $stmt->execute([$username]);
    return $stmt->fetch();
}

function registerUser(string $username, string $password): bool
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = getDbConnection()->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        return $stmt->execute([$username, $hash]);
    } catch (PDOException $e) {
        // UNIQUE vẫn ngăn trùng tên khi có hai yêu cầu đăng ký đồng thời.
        if ($e->getCode() === '23000') {
            return false;
        }
        throw $e;
    }
}
