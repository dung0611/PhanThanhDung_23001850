<?php include __DIR__ . '/errors.php'; ?>
<form method="post">
    <?= csrfField() ?>
    <div class="form-group">
        <label for="name" class="form-label">Tên sản phẩm *</label>
        <input type="text" id="name" name="name" class="form-control" value="<?= h($name) ?>" maxlength="100" required>
    </div>
    <div class="form-group">
        <label for="price" class="form-label">Giá sản phẩm (VNĐ) *</label>
        <input type="number" id="price" name="price" class="form-control" value="<?= h($price) ?>" min="0.01" max="99999999.99" step="0.01" required>
        <p class="form-hint">Giá lớn hơn 0, tối đa 2 chữ số thập phân.</p>
    </div>
    <div class="form-group">
        <label for="quantity" class="form-label">Số lượng tồn kho *</label>
        <input type="number" id="quantity" name="quantity" class="form-control" value="<?= h($quantity) ?>" min="0" max="2147483647" step="1" required>
        <p class="form-hint">Số lượng là số nguyên không âm.</p>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?= h($submitLabel) ?></button>
        <a href="product_list.php" class="btn btn-secondary">Hủy bỏ</a>
    </div>
</form>
