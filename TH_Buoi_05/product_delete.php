<?php
require_once __DIR__ . '/common/app.php';
requireLogin();
require_once __DIR__ . '/model/product.php';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($isPost) {
    verifyCsrf();
}
$id = filter_var($isPost ? ($_POST['id'] ?? null) : ($_GET['id'] ?? null), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;
$product = getProductById($id);
if (!$product) {
    $_SESSION['error_msg'] = 'Sản phẩm không tồn tại.';
    redirect('product_list.php');
}
if ($isPost && postString('confirm_delete') === '1') {
    if (deleteProduct($id)) {
        $_SESSION['success_msg'] = 'Xóa sản phẩm thành công.';
    } else {
        $_SESSION['error_msg'] = 'Không thể xóa sản phẩm. Vui lòng thử lại.';
    }
    redirect('product_list.php');
}
$pageTitle = 'Xóa sản phẩm';
include __DIR__ . '/view/header.php';
?>
<div class="card confirm-box">
    <h1 class="page-title">Xác nhận xóa sản phẩm</h1>
    <p class="intro">Bạn có chắc chắn muốn xóa sản phẩm này?</p>
    <div class="product-preview-card">
        <p><strong>Tên sản phẩm:</strong> <?= h($product['name']) ?></p>
        <p><strong>Giá:</strong> <?= number_format((float)$product['price'], 2, ',', '.') ?> đ</p>
        <p><strong>Số lượng tồn:</strong> <?= (int)$product['quantity'] ?></p>
    </div>
    <form method="post" action="product_delete.php" class="action-row centered">
        <?= csrfField() ?>
        <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
        <input type="hidden" name="confirm_delete" value="1">
        <a href="product_list.php" class="btn btn-secondary">Hủy bỏ</a>
        <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
    </form>
</div>
<?php include __DIR__ . '/view/footer.php'; ?>
