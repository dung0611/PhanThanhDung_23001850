<?php
require_once __DIR__ . '/../common/app.php';
$currentScript = basename($_SERVER['SCRIPT_NAME']);
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? 'Trang chủ') ?> - Shopping Cart TH5</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body>
<header class="site-header">
    <div class="container header-content">
        <a href="index.php" class="brand-title">🛒 Shopping Cart <span class="brand-badge">TH5</span></a>
        <nav class="main-nav" aria-label="Điều hướng chính">
            <a href="index.php" class="nav-link <?= $currentScript === 'index.php' ? 'active' : '' ?>">Trang chủ</a>
            <?php if ($user): ?>
                <a href="product_list.php" class="nav-link <?= $currentScript === 'product_list.php' ? 'active' : '' ?>">Sản phẩm</a>
                <a href="product_add.php" class="nav-link <?= $currentScript === 'product_add.php' ? 'active' : '' ?>">+ Thêm sản phẩm</a>
                <span class="account-name">Xin chào, <strong><?= h($user['username']) ?></strong></span>
                <a href="logout.php" class="nav-link">Đăng xuất</a>
            <?php else: ?>
                <a href="login.php" class="nav-link <?= $currentScript === 'login.php' ? 'active' : '' ?>">Đăng nhập</a>
                <a href="register.php" class="nav-link <?= $currentScript === 'register.php' ? 'active' : '' ?>">Đăng ký</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="content-area">
    <div class="container">
        <?php foreach (['success_msg' => 'success', 'error_msg' => 'danger'] as $key => $type): ?>
            <?php if (isset($_SESSION[$key])): ?>
                <div class="alert alert-<?= $type ?>" role="status"><?= h($_SESSION[$key]) ?></div>
                <?php unset($_SESSION[$key]); ?>
            <?php endif; ?>
        <?php endforeach; ?>
