<?php
require_once __DIR__ . '/common/app.php';
if (currentUser()) {
    redirect('index.php');
}
require_once __DIR__ . '/model/user.php';
$pageTitle = 'Đăng ký';
$errors = [];
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim(postString('username'));
    $password = postString('password');
    $passwordConfirm = postString('password_confirm');
    if ($username === '') {
        $errors[] = 'Tên đăng nhập không được để trống.';
    } elseif (preg_match_all('/./us', $username) > 50) {
        $errors[] = 'Tên đăng nhập không được vượt quá 50 ký tự.';
    }
    if (trim($password) === '') {
        $errors[] = 'Mật khẩu không được để trống.';
    } else {
        // Đếm ký tự Unicode để mật khẩu tiếng Việt không bị tính theo số byte.
        $passwordLength = preg_match_all('/./us', $password);
        if ($passwordLength === false || strpos($password, "\0") !== false) {
            $errors[] = 'Mật khẩu chứa ký tự không hợp lệ.';
        } else {
            if ($passwordLength < 8 || $passwordLength > 15) {
                $errors[] = 'Mật khẩu phải có từ 8 đến 15 ký tự.';
            }
            if (!preg_match('/\p{Lu}/u', $password)) {
                $errors[] = 'Mật khẩu phải có ít nhất 1 chữ in hoa.';
            }
            if (!preg_match('/\p{Ll}/u', $password)) {
                $errors[] = 'Mật khẩu phải có ít nhất 1 chữ in thường.';
            }
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = 'Mật khẩu phải có ít nhất 1 chữ số (0–9).';
            }
            if (!preg_match('/[\p{P}\p{S}]/u', $password)) {
                $errors[] = 'Mật khẩu phải có ít nhất 1 ký tự đặc biệt (ví dụ: !, @, #).';
            }
        }
    }
    if ($passwordConfirm === '') {
        $errors[] = 'Vui lòng nhập lại mật khẩu để xác nhận.';
    } elseif ($passwordConfirm !== $password) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    }
    if (!$errors) {
        try {
            if (getUserByUsername($username) || !registerUser($username, $password)) {
                $errors[] = 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.';
            } else {
                $_SESSION['success_msg'] = 'Đăng ký thành công. Vui lòng đăng nhập.';
                redirect('login.php');
            }
        } catch (PDOException $e) {
            error_log('Lỗi đăng ký TH5: ' . $e->getMessage());
            $errors[] = 'Không thể tạo tài khoản lúc này. Vui lòng thử lại sau.';
        }
    }
}
include __DIR__ . '/view/header.php';
?>
<div class="card auth-card">
    <h1 class="page-title">Tạo tài khoản</h1>
    <p class="intro">Đăng ký để bắt đầu quản lý sản phẩm.</p>
    <?php include __DIR__ . '/view/errors.php'; ?>
    <form method="post" action="register.php">
        <?= csrfField() ?>
        <div class="form-group">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= h($username) ?>" maxlength="50" autocomplete="username" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" id="password" name="password" class="form-control" autocomplete="new-password"
                   pattern="(?=.*\p{Lu})(?=.*\p{Ll})(?=.*[0-9])(?=.*[\p{P}\p{S}]).{8,15}"
                   title="Mật khẩu từ 8 đến 15 ký tự, có ít nhất 1 chữ hoa, 1 chữ thường, 1 chữ số và 1 ký tự đặc biệt."
                   aria-describedby="password-rules" required>
            <ul id="password-rules" class="password-requirements" aria-label="Điều kiện mật khẩu">
                <li class="password-rule is-unmet" data-password-rule="length">
                    <span class="rule-icon" data-rule-icon aria-hidden="true">✕</span>
                    <span>Từ 8–15 ký tự <span id="password-length-count">(0 ký tự)</span></span>
                    <span class="rule-status" data-rule-status>Chưa đạt</span>
                </li>
                <li class="password-rule is-unmet" data-password-rule="uppercase">
                    <span class="rule-icon" data-rule-icon aria-hidden="true">✕</span>
                    <span>Ít nhất 1 chữ in hoa</span>
                    <span class="rule-status" data-rule-status>Chưa đạt</span>
                </li>
                <li class="password-rule is-unmet" data-password-rule="lowercase">
                    <span class="rule-icon" data-rule-icon aria-hidden="true">✕</span>
                    <span>Ít nhất 1 chữ in thường</span>
                    <span class="rule-status" data-rule-status>Chưa đạt</span>
                </li>
                <li class="password-rule is-unmet" data-password-rule="digit">
                    <span class="rule-icon" data-rule-icon aria-hidden="true">✕</span>
                    <span>Ít nhất 1 chữ số (0–9)</span>
                    <span class="rule-status" data-rule-status>Chưa đạt</span>
                </li>
                <li class="password-rule is-unmet" data-password-rule="special">
                    <span class="rule-icon" data-rule-icon aria-hidden="true">✕</span>
                    <span>Ít nhất 1 ký tự đặc biệt (!, @, #…)</span>
                    <span class="rule-status" data-rule-status>Chưa đạt</span>
                </li>
            </ul>
        </div>
        <div class="form-group">
            <label for="password_confirm" class="form-label">Xác nhận mật khẩu</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-control" autocomplete="new-password" aria-describedby="password-confirm-status" required>
            <p id="password-confirm-status" class="password-rule is-unmet confirmation-rule" data-password-rule="confirmation">
                <span class="rule-icon" data-rule-icon aria-hidden="true">✕</span>
                <span>Mật khẩu xác nhận khớp</span>
                <span class="rule-status" data-rule-status>Chưa đạt</span>
            </p>
        </div>
        <button type="submit" class="btn btn-primary full-width">Đăng ký</button>
    </form>
    <p class="auth-switch">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</div>
<script src="assets/js/register.js?v=<?= filemtime(__DIR__ . '/assets/js/register.js') ?>" defer></script>
<?php include __DIR__ . '/view/footer.php'; ?>
