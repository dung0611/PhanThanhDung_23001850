<?php
require_once __DIR__ . '/common/app.php';
$pageTitle = 'Trang chủ';
if (currentUser()) {
    require_once __DIR__ . '/model/product.php';
    $products = getAllProducts();
    $totalProducts = count($products);
    $totalQuantity = array_sum(array_column($products, 'quantity'));
}
include __DIR__ . '/view/header.php';
?>
<div class="card">
    <div class="page-header">
        <h1 class="page-title">Hệ thống quản lý sản phẩm</h1>
        <span class="badge badge-in-stock">Bài thực hành 5</span>
    </div>
    <p class="intro">Quản lý sản phẩm, tồn kho và tài khoản với PHP &amp; MySQL.</p>
    <?php if (currentUser()): ?>
        <div class="stats-grid">
            <div class="stat-card"><span>Tổng số mặt hàng</span><strong><?= $totalProducts ?></strong></div>
            <div class="stat-card"><span>Tổng tồn kho</span><strong><?= (int)$totalQuantity ?></strong></div>
        </div>
        <div class="action-row">
            <a href="product_list.php" class="btn btn-primary">Xem danh sách sản phẩm</a>
            <a href="product_add.php" class="btn btn-success">+ Thêm sản phẩm mới</a>
        </div>
    <?php else: ?>
        <p class="intro">Đăng nhập để xem, thêm, sửa và xóa sản phẩm. Bạn có thể đăng ký nếu chưa có tài khoản.</p>
        <div class="action-row">
            <a href="login.php" class="btn btn-primary">Đăng nhập</a>
            <a href="register.php" class="btn btn-success">Tạo tài khoản</a>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/view/footer.php'; ?>
