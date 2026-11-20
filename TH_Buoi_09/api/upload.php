<?php
declare(strict_types=1);

ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function respond(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(405, ['success' => false, 'message' => 'Chỉ hỗ trợ phương thức POST.']);
}

$rawId = $_POST['product_id'] ?? null;
if (!is_string($rawId) || !ctype_digit($rawId) ||
    ($id = filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]])) === false) {
    respond(400, ['success' => false, 'message' => 'Mã sản phẩm không hợp lệ.']);
}

if (!isset($_FILES['image']) || !is_array($_FILES['image'])) {
    respond(400, ['success' => false, 'message' => 'Chưa nhận được ảnh. Kiểm tra giới hạn upload của PHP.']);
}
$file = $_FILES['image'];
if (is_array($file['error'] ?? null) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    $tooLarge = in_array($file['error'] ?? null, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true);
    respond($tooLarge ? 413 : 400, [
        'success' => false,
        'message' => $tooLarge ? 'Ảnh vượt quá giới hạn dung lượng cho phép.' : 'Không thể nhận file ảnh. Hãy chọn ảnh và thử lại.',
    ]);
}

$temporary = $file['tmp_name'] ?? '';
if (!is_string($temporary) || !is_uploaded_file($temporary)) {
    respond(400, ['success' => false, 'message' => 'File tải lên không hợp lệ.']);
}
$size = filesize($temporary);
if ($size === false || $size < 1 || $size > 2 * 1024 * 1024) {
    respond(413, ['success' => false, 'message' => 'Ảnh phải có dung lượng tối đa 2 MB.']);
}

$imageInfo = @getimagesize($temporary);
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($temporary);
$formats = [IMAGETYPE_JPEG => ['image/jpeg', 'jpg'], IMAGETYPE_PNG => ['image/png', 'png']];
$type = $imageInfo[2] ?? null;
if (!isset($formats[$type]) || $mime !== $formats[$type][0]) {
    respond(422, ['success' => false, 'message' => 'Chỉ chấp nhận ảnh JPG, JPEG hoặc PNG hợp lệ.']);
}

$pdo = null;
$destination = null;
try {
    $pdo = require __DIR__ . '/../config/database.php';
    $statement = $pdo->prepare('SELECT image FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
    $existing = $statement->fetch();
    if ($existing === false) {
        respond(404, ['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
    }

    $directory = __DIR__ . '/../uploads';
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('Không tạo được thư mục uploads.');
    }
    $filename = bin2hex(random_bytes(16)) . '.' . $formats[$type][1];
    $destination = $directory . '/' . $filename;
    if (!move_uploaded_file($temporary, $destination)) {
        throw new RuntimeException('Không lưu được ảnh tải lên.');
    }
    $path = 'uploads/' . $filename;
    $statement = $pdo->prepare('UPDATE products SET image = :image WHERE id = :id');
    $statement->execute(['image' => $path, 'id' => $id]);
    if ($statement->rowCount() !== 1) {
        throw new RuntimeException('Sản phẩm không còn tồn tại khi cập nhật ảnh.');
    }
    if (is_string($existing['image']) && preg_match('~^uploads/[a-f0-9]{32}\.(jpg|png)$~', $existing['image'])) {
        @unlink(__DIR__ . '/../' . $existing['image']);
    }
    respond(200, ['success' => true, 'message' => 'Đã tải ảnh lên.', 'data' => ['product_id' => $id, 'image' => $path]]);
} catch (Throwable $exception) {
    if ($destination !== null && is_file($destination)) unlink($destination);
    error_log('TH_Buoi_09 upload: ' . $exception->getMessage());
    respond(500, ['success' => false, 'message' => 'Không thể lưu ảnh. Hãy kiểm tra MySQL và quyền ghi thư mục uploads, rồi thử lại.']);
}
