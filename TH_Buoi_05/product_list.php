<?php
require_once __DIR__ . '/common/app.php';
requireLogin();
require_once __DIR__ . '/model/product.php';
$pageTitle = 'Danh sách sản phẩm';
$products = getAllProducts();
include __DIR__ . '/view/header.php';
?>
<div class="card">
    <div class="page-header">
        <h1 class="page-title">Danh sách sản phẩm</h1>
        <a href="product_add.php" class="btn btn-success">+ Thêm sản phẩm</a>
    </div>
    <?php if (!$products): ?>
        <p>Chưa có sản phẩm nào. <a href="product_add.php">Thêm sản phẩm đầu tiên</a>.</p>
    <?php else: ?>
        <p class="intro">Chọn <strong>Xóa sản phẩm</strong> tại dòng sản phẩm cần xóa, sau đó xác nhận trên trang tiếp theo.</p>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>ID</th><th>Tên sản phẩm</th><th class="text-right">Giá</th><th class="text-center">Số lượng</th><th class="text-center">Chức năng</th></tr></thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td data-label="ID"><?= 'SP' . str_pad((string)(int)$product['id'], 3, '0', STR_PAD_LEFT) ?></td>
                        <td data-label="Tên sản phẩm"><strong><?= h($product['name']) ?></strong></td>
                        <td data-label="Giá" class="text-right price-text"><?= number_format((float)$product['price'], 2, ',', '.') ?> đ</td>
                        <td data-label="Số lượng" class="text-center"><span class="badge <?= $product['quantity'] > 10 ? 'badge-in-stock' : ($product['quantity'] > 0 ? 'badge-low-stock' : 'badge-out-stock') ?>"><?= (int)$product['quantity'] ?> cái</span></td>
                        <td data-label="Chức năng" class="text-center"><div class="table-actions">
                            <a href="product_edit.php?id=<?= (int)$product['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="product_delete.php?id=<?= (int)$product['id'] ?>" class="btn btn-danger btn-sm" aria-label="Xóa sản phẩm <?= h($product['name']) ?>">Xóa sản phẩm</a>
                        </div></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="form-hint">Tổng cộng: <strong><?= count($products) ?></strong> sản phẩm.</p>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/view/footer.php'; ?>
