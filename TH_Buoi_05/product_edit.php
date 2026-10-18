<?php
require_once __DIR__ . '/common/app.php';
requireLogin();
require_once __DIR__ . '/model/product.php';
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;
$product = getProductById($id);
if (!$product) {
    $_SESSION['error_msg'] = 'Không tìm thấy sản phẩm.';
    redirect('product_list.php');
}
$pageTitle = 'Chỉnh sửa sản phẩm';
$errors = [];
$name = $product['name'];
$price = $product['price'];
$quantity = (string)$product['quantity'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name = trim(postString('name'));
    $price = trim(postString('price'));
    $quantity = trim(postString('quantity'));
    $errors = validateProduct($name, $price, $quantity);
    if (!$errors) {
        if (updateProduct($id, $name, (float)$price, (int)$quantity)) {
            $_SESSION['success_msg'] = 'Cập nhật sản phẩm thành công.';
            redirect('product_list.php');
        }
        $errors[] = 'Không thể cập nhật sản phẩm. Vui lòng thử lại.';
    }
}
$submitLabel = 'Lưu thay đổi';
include __DIR__ . '/view/header.php';
?>
<div class="card product-form-card">
    <div class="page-header"><h1 class="page-title">Chỉnh sửa sản phẩm</h1><a href="product_list.php" class="btn btn-secondary btn-sm">Quay lại</a></div>
    <?php include __DIR__ . '/view/product_form.php'; ?>
</div>
<?php include __DIR__ . '/view/footer.php'; ?>
