<?php
require_once __DIR__ . '/common/app.php';
requireLogin();
require_once __DIR__ . '/model/product.php';
$pageTitle = 'Thêm sản phẩm';
$errors = [];
$name = $price = $quantity = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name = trim(postString('name'));
    $price = trim(postString('price'));
    $quantity = trim(postString('quantity'));
    $errors = validateProduct($name, $price, $quantity);
    if (!$errors) {
        if (addProduct($name, (float)$price, (int)$quantity)) {
            $_SESSION['success_msg'] = 'Thêm sản phẩm thành công.';
            redirect('product_list.php');
        }
        $errors[] = 'Không thể lưu sản phẩm. Vui lòng thử lại.';
    }
}
$submitLabel = 'Thêm sản phẩm';
include __DIR__ . '/view/header.php';
?>
<div class="card product-form-card">
    <div class="page-header"><h1 class="page-title">Thêm sản phẩm mới</h1><a href="product_list.php" class="btn btn-secondary btn-sm">Quay lại</a></div>
    <?php include __DIR__ . '/view/product_form.php'; ?>
</div>
<?php include __DIR__ . '/view/footer.php'; ?>
