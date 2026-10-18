<?php
require_once __DIR__ . '/common/app.php';
requireLogin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }
    session_destroy();
    redirect('login.php');
}
$pageTitle = 'Đăng xuất';
include __DIR__ . '/view/header.php';
?>
<div class="card auth-card">
    <h1 class="page-title">Đăng xuất</h1>
    <p class="intro">Bạn muốn kết thúc phiên đăng nhập hiện tại?</p>
    <form method="post" action="logout.php" class="action-row">
        <?= csrfField() ?>
        <button type="submit" class="btn btn-primary">Xác nhận đăng xuất</button>
        <a href="product_list.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
<?php include __DIR__ . '/view/footer.php'; ?>
