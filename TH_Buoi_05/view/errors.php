<?php if (!empty($errors)): ?>
    <div class="alert alert-danger" role="alert">
        <strong>Vui lòng kiểm tra lại thông tin:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= h($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
