<?php

require_once __DIR__ . '/model/product.php';

$pageTitle = 'Trang chủ';
$products = getAllProducts();
$totalProducts = count($products);
$totalQuantity = array_sum(array_column($products, 'quantity'));

include __DIR__ . '/view/header.php';
?>

<div class="card">
    <div class="page-header">
        <h2 class="page-title">Chào mừng đến với Hệ thống Quản lý Sản phẩm</h2>
        <span class="badge badge-in-stock">Hoạt động bình thường</span>
    </div>

    <p style="margin-bottom: 1.5rem; font-size: 1.05rem; color: #655073;">
        Hệ thống được xây dựng theo mô hình phân tách tầng độc lập (Common - Model - View - Page Controllers) 
        cung cấp đầy đủ các thao tác <strong>CRUD (Create - Read - Update - Delete)</strong> cho sản phẩm giỏ hàng.
    </p>

    <!-- Thống kê nhanh -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div style="background: #f3e8ff; border-left: 4px solid #a855f7; padding: 1.25rem; border-radius: 6px;">
            <div style="font-size: 0.875rem; color: #6b21a8; font-weight: 600;">Tổng số mặt hàng</div>
            <div style="font-size: 2rem; font-weight: 700; color: #581c87;"><?php echo $totalProducts; ?></div>
        </div>
        <div style="background: #fdf2f8; border-left: 4px solid #db2777; padding: 1.25rem; border-radius: 6px;">
            <div style="font-size: 0.875rem; color: #9d174d; font-weight: 600;">Tổng tồn kho</div>
            <div style="font-size: 2rem; font-weight: 700; color: #831843;"><?php echo $totalQuantity; ?> <span style="font-size: 1rem; font-weight: normal;">sản phẩm</span></div>
        </div>
        <div style="background: #eef2ff; border-left: 4px solid #6366f1; padding: 1.25rem; border-radius: 6px;">
            <div style="font-size: 0.875rem; color: #4338ca; font-weight: 600;">Cơ sở dữ liệu</div>
            <div style="font-size: 1.25rem; font-weight: 700; color: #3730a3; margin-top: 0.5rem;">shopping_cart.products</div>
        </div>
    </div>

    <!-- Phím tắt điều hướng -->
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="product_list.php" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
            📋 Xem danh sách sản phẩm
        </a>
        <a href="product_add.php" class="btn btn-success" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
            ➕ Thêm sản phẩm mới
        </a>
    </div>
</div>

<div class="card" style="margin-top: 1.5rem;">
    <h3 style="font-size: 1.15rem; margin-bottom: 0.75rem; color: #58366e;">✨ Các chức năng chính của hệ thống:</h3>
    <ul style="margin-left: 1.5rem; line-height: 1.8; color: #655073;">
        <li><strong>Xem danh sách (Read):</strong> Hiển thị bảng sản phẩm trực quan, phân loại tồn kho, định dạng tiền tệ chuẩn VNĐ.</li>
        <li><strong>Thêm sản phẩm (Create):</strong> Form kiểm tra tính hợp lệ dữ liệu (tên không rỗng, giá &gt; 0, số lượng &ge; 0).</li>
        <li><strong>Chỉnh sửa sản phẩm (Update):</strong> Nạp dữ liệu cũ theo ID và cập nhật an toàn với Prepared Statement.</li>
        <li><strong>Xóa sản phẩm (Delete):</strong> Xác thực sự tồn tại và yêu cầu xác nhận an toàn trước khi xóa để tránh mất mát dữ liệu.</li>
    </ul>
</div>

<?php include __DIR__ . '/view/footer.php'; ?>
