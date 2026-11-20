<?php
declare(strict_types=1);

// Không để cảnh báo PHP làm hỏng định dạng JSON của phản hồi.
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function respond(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function readProduct(): array
{
    try {
        $input = json_decode(file_get_contents('php://input'), false, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        respond(400, ['success' => false, 'message' => 'Nội dung JSON không hợp lệ.']);
    }

    if (!($input instanceof stdClass)) {
        respond(400, ['success' => false, 'message' => 'Dữ liệu phải là một đối tượng JSON.']);
    }
    if (!isset($input->name) || !is_string($input->name)) {
        respond(422, ['success' => false, 'message' => 'Tên sản phẩm phải là chuỗi từ 1 đến 100 ký tự.']);
    }

    // Đếm ký tự Unicode, không đếm số byte tiếng Việt; không cần extension mbstring.
    $name = preg_replace('/^\s+|\s+$/u', '', $input->name);
    $length = preg_match_all('/./us', $name);
    if ($length < 1 || $length > 100) {
        respond(422, ['success' => false, 'message' => 'Tên sản phẩm sau khi bỏ khoảng trắng phải có từ 1 đến 100 ký tự.']);
    }

    // Chỉ chấp nhận số nguyên JSON; từ chối chuỗi số, boolean và số thập phân.
    // Giới hạn trên tương ứng kiểu INT có dấu trong MySQL.
    if (!isset($input->price) || !is_int($input->price) || $input->price < 1 || $input->price > 2147483647) {
        respond(422, ['success' => false, 'message' => 'Đơn giá phải là số nguyên từ 1 đến 2.147.483.647 đồng.']);
    }
    if (!isset($input->stock) || !is_int($input->stock) || $input->stock < 0 || $input->stock > 2147483647) {
        respond(422, ['success' => false, 'message' => 'Số lượng tồn phải là số nguyên từ 0 đến 2.147.483.647.']);
    }

    return ['name' => $name, 'price' => $input->price, 'stock' => $input->stock];
}

function readId(): int
{
    $value = $_GET['id'] ?? null;
    if (!is_string($value) || !ctype_digit($value)) {
        respond(400, ['success' => false, 'message' => 'Mã sản phẩm trên URL phải là số nguyên dương.']);
    }
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
    if ($id === false) {
        respond(400, ['success' => false, 'message' => 'Mã sản phẩm trên URL không hợp lệ.']);
    }
    return $id;
}

function readPage(): int
{
    if (!isset($_GET['page'])) return 1;
    $value = $_GET['page'];
    if (!is_string($value) || !ctype_digit($value)) {
        respond(400, ['success' => false, 'message' => 'page phải là số nguyên dương.']);
    }
    $page = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
    if ($page === false) respond(400, ['success' => false, 'message' => 'page không hợp lệ.']);
    return $page;
}

function readLimit(): int
{
    if (!isset($_GET['limit'])) return 5;
    if ($_GET['limit'] !== '5') {
        respond(400, ['success' => false, 'message' => 'limit phải bằng 5.']);
    }
    return 5;
}

$pdo = null;
try {
    $method = $_SERVER['REQUEST_METHOD'];
    if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
        header('Allow: GET, POST, PUT, DELETE');
        respond(405, ['success' => false, 'message' => 'Phương thức không được hỗ trợ.']);
    }

    // Từ chối dữ liệu sai trước cả bước kết nối và ghi vào MySQL.
    $id = in_array($method, ['PUT', 'DELETE'], true) ? readId() : null;
    $page = $method === 'GET' ? readPage() : null;
    $limit = $method === 'GET' ? readLimit() : null;
    $product = in_array($method, ['POST', 'PUT'], true) ? readProduct() : null;
    $pdo = require __DIR__ . '/../config/database.php';

    if ($method === 'GET') {
        $total = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;
        $statement = $pdo->prepare('SELECT id, name, price, stock, image FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $products = $statement->fetchAll();
        foreach ($products as &$row) {
            $row['id'] = (int) $row['id'];
            $row['price'] = (int) $row['price'];
            $row['stock'] = (int) $row['stock'];
        }
        unset($row);
        respond(200, ['success' => true, 'data' => $products, 'pagination' => [
            'page' => $page, 'limit' => $limit, 'total' => $total, 'total_pages' => $totalPages,
        ]]);
    }

    if ($method === 'POST') {
        $statement = $pdo->prepare('INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)');
        $statement->execute($product);
        respond(201, [
            'success' => true,
            'message' => 'Đã thêm sản phẩm.',
            'data' => array_merge(['id' => (int) $pdo->lastInsertId()], $product, ['image' => null]),
        ]);
    }

    if ($method === 'PUT') {
        // Khóa dòng để kiểm tra tồn tại và cập nhật trong cùng một giao dịch.
        // Không dùng rowCount của UPDATE: lưu giá trị cũ vẫn là một lần sửa hợp lệ.
        $pdo->beginTransaction();
        $statement = $pdo->prepare('SELECT id FROM products WHERE id = :id FOR UPDATE');
        $statement->execute(['id' => $id]);
        if ($statement->fetch() === false) {
            $pdo->rollBack();
            respond(404, ['success' => false, 'message' => 'Sản phẩm cần sửa không tồn tại.']);
        }
        $statement = $pdo->prepare('UPDATE products SET name = :name, price = :price, stock = :stock WHERE id = :id');
        $statement->execute(array_merge($product, ['id' => $id]));
        $pdo->commit();
        respond(200, ['success' => true, 'message' => 'Đã cập nhật sản phẩm.']);
    }

    $pdo->beginTransaction();
    $statement = $pdo->prepare('SELECT image FROM products WHERE id = :id FOR UPDATE');
    $statement->execute(['id' => $id]);
    $existing = $statement->fetch();
    if ($existing === false) {
        $pdo->rollBack();
        respond(404, ['success' => false, 'message' => 'Sản phẩm cần xóa không tồn tại.']);
    }
    $statement = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
    $pdo->commit();
    if (is_string($existing['image']) && preg_match('~^uploads/[a-f0-9]{32}\.(jpg|png)$~', $existing['image'])) {
        @unlink(__DIR__ . '/../' . $existing['image']);
    }
    respond(200, ['success' => true, 'message' => 'Đã xóa sản phẩm.']);
} catch (Throwable $exception) {
    if ($pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('TH_Buoi_09: ' . $exception->getMessage());
    respond(500, [
        'success' => false,
        'message' => 'Không thể xử lý dữ liệu. Hãy kiểm tra MySQL, nhập database.sql và kiểm tra config/database.php.',
    ]);
}
