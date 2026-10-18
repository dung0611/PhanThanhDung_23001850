<?php

// Khởi tạo session trước khi xử lý form hoặc gửi bất kỳ HTML nào.
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    session_start();
}

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function postString(string $key): string
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : '';
}

function redirect(string $page): void
{
    header('Location: ' . $page);
    exit;
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireLogin(): void
{
    if (!currentUser()) {
        $_SESSION['error_msg'] = 'Vui lòng đăng nhập để quản lý sản phẩm.';
        redirect('login.php');
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrfToken()) . '">';
}

function verifyCsrf(): void
{
    $token = postString('csrf_token');
    if ($token === '' || !hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        exit('Phiên xác nhận không hợp lệ. Vui lòng quay lại, tải lại trang và thử lại.');
    }
}

function validateProduct(string $name, string $price, string $quantity): array
{
    $errors = [];
    if ($name === '') {
        $errors[] = 'Tên sản phẩm không được để trống.';
    } elseif (preg_match_all('/./us', $name) > 100) {
        $errors[] = 'Tên sản phẩm không được vượt quá 100 ký tự.';
    }
    if (!preg_match('/^\d+(?:\.\d{1,2})?$/D', $price)
        || (float)$price <= 0 || (float)$price > 99999999.99) {
        $errors[] = 'Giá phải lớn hơn 0, không vượt quá 99.999.999,99 và có tối đa 2 chữ số thập phân.';
    }
    if (filter_var($quantity, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 2147483647]]) === false) {
        $errors[] = 'Số lượng phải là số nguyên từ 0 đến 2.147.483.647.';
    }
    return $errors;
}
