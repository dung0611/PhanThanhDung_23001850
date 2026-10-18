<?php
require_once __DIR__ . '/common/app.php';
if (currentUser()) {
    redirect('index.php');
}
require_once __DIR__ . '/model/user.php';
$pageTitle = 'Đăng nhập';
$errors = [];
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim(postString('username'));
    $password = postString('password');
    if ($username === '' || $password === '') {
        $errors[] = 'Vui lòng nhập tên đăng nhập và mật khẩu.';
    } else {
        try {
            $user = getUserByUsername($username);
            if ($user && strlen($password) <= 72 && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = ['id' => (int)$user['id'], 'username' => $user['username']];
                unset($_SESSION['csrf_token']);
                $_SESSION['success_msg'] = 'Đăng nhập thành công.';
                redirect('index.php');
            }
            $errors[] = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
        } catch (PDOException $e) {
            error_log('Lỗi đăng nhập TH5: ' . $e->getMessage());
            $errors[] = 'Không thể đăng nhập lúc này. Vui lòng thử lại sau.';
        }
    }
}
include __DIR__ . '/view/header.php';
?>
<div class="card auth-card">
    <h1 class="page-title">Đăng nhập</h1>
    <p class="intro">Chào mừng bạn trở lại với Shopping Cart.</p>
    <?php include __DIR__ . '/view/errors.php'; ?>
    <form method="post" action="login.php">
        <?= csrfField() ?>
        <div class="form-group">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= h($username) ?>" maxlength="50" autocomplete="username" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-primary full-width">Đăng nhập</button>
    </form>
    <p class="auth-switch">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
</div>
<?php include __DIR__ . '/view/footer.php'; ?>
