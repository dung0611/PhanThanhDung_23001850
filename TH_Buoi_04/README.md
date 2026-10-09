# TH_04 – Hệ thống quản lý sản phẩm

## Thông tin sinh viên

- **Họ và tên:** Phan Thanh Dũng
- **Mã sinh viên:** 23001850
- **Môn học:** Phát triển ứng dụng Web
- **Bài thực hành:** TH_04 (Buổi 04)

## Mô tả bài thực hành

Xây dựng website quản lý sản phẩm bằng PHP và MySQL, tổ chức mã nguồn theo các phần: kết nối cơ sở dữ liệu (Common), xử lý dữ liệu (Model), giao diện dùng chung (View) và các trang điều khiển. Bài thực hành áp dụng các thao tác CRUD để quản lý tên sản phẩm, giá bán và số lượng tồn kho.

## Chức năng

- **Trang chủ:** hiển thị tổng số mặt hàng và tổng số lượng tồn kho.
- **Xem danh sách:** hiển thị sản phẩm, giá bán theo VNĐ và trạng thái tồn kho.
- **Thêm sản phẩm:** nhập tên, giá và số lượng; kiểm tra dữ liệu trước khi lưu.
- **Sửa sản phẩm:** tải dữ liệu theo ID và cập nhật thông tin.
- **Xóa sản phẩm:** hiển thị thông tin sản phẩm và yêu cầu xác nhận trước khi xóa.

Dữ liệu hợp lệ yêu cầu tên không để trống, giá lớn hơn 0 và số lượng là số nguyên không âm. Các truy vấn có tham số sử dụng PDO Prepared Statements. Giao diện sử dụng tông màu tím–hồng.

## Công nghệ sử dụng

- PHP, PDO và phần mở rộng `pdo_mysql`.
- MySQL hoặc MariaDB.
- HTML và CSS.

## Cấu trúc mã nguồn

| File / thư mục | Vai trò |
| --- | --- |
| `database.sql` | Tạo database, bảng sản phẩm và nạp 9 sản phẩm mẫu |
| `common/dbConnect.php` | Cấu hình và tạo kết nối cơ sở dữ liệu |
| `model/product.php` | Xử lý truy vấn và các thao tác CRUD |
| `view/` | Header và footer dùng chung |
| `assets/css/style.css` | Định dạng giao diện |
| `index.php` | Trang chủ và thống kê |
| `product_list.php` | Danh sách sản phẩm |
| `product_add.php` | Thêm sản phẩm |
| `product_edit.php` | Sửa sản phẩm |
| `product_delete.php` | Xác nhận và xóa sản phẩm |

## Cơ sở dữ liệu

Database có tên `shopping_cart`, sử dụng bảng `products`:

| Cột | Kiểu dữ liệu | Ý nghĩa |
| --- | --- | --- |
| `id` | INT, khóa chính, tự tăng | Mã sản phẩm |
| `name` | VARCHAR(100) | Tên sản phẩm |
| `price` | DECIMAL(10,2) | Giá bán |
| `quantity` | INT | Số lượng tồn kho |

## Hướng dẫn chạy

1. Chuẩn bị PHP có phần mở rộng `pdo_mysql` và khởi động MySQL/MariaDB (có thể dùng XAMPP).
2. Import file `database.sql` bằng phpMyAdmin hoặc công cụ dòng lệnh để tạo database và dữ liệu mẫu. Chạy lại file sẽ cập nhật các bản ghi có ID từ 1 đến 9.
3. Kiểm tra cấu hình trong `common/dbConnect.php`. Cấu hình hiện tại: host `localhost`, database `shopping_cart`, tài khoản `root`, mật khẩu trống.
4. Mở terminal tại thư mục bài thực hành và chạy:

   ```bash
   php -S 127.0.0.1:8000
   ```

5. Truy cập [http://127.0.0.1:8000](http://127.0.0.1:8000) để sử dụng ứng dụng.

## Mục tiêu thực hành

Rèn luyện cách kết nối PHP với cơ sở dữ liệu, xử lý form, kiểm tra dữ liệu đầu vào, thực hiện CRUD và tổ chức mã nguồn thành các phần có trách nhiệm rõ ràng.
