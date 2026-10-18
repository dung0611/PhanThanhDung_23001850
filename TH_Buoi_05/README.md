# TH_Buoi_05 - Quản lý sản phẩm, đăng ký và đăng nhập

- **Sinh viên:** Phan Thanh Dũng - **MSSV:** 23001850.
- **Môn học:** Phát triển ứng dụng Web.

## Chức năng

- Xem danh sách, thêm, sửa, xác nhận xóa sản phẩm; kiểm tra tên, giá và số lượng.
- Đăng ký tài khoản, xác nhận mật khẩu bằng cách nhập lại, kiểm tra tên đăng nhập trùng và lưu mật khẩu bằng `password_hash()`.
- Mật khẩu đăng ký dài 8–15 ký tự, có ít nhất 1 chữ hoa, 1 chữ thường, 1 chữ số (0–9) và 1 ký tự đặc biệt; kiểm tra trên form và tại máy chủ, hỗ trợ chữ tiếng Việt.
- Các điều kiện mật khẩu và xác nhận mật khẩu cập nhật ngay khi nhập: xanh lá khi đạt, đỏ khi chưa đạt, kèm ký hiệu và trạng thái.
- Đăng nhập bằng `password_verify()`, chuyển đến trang chủ `index.php`, quản lý phiên và hiển thị tên tài khoản.
- Đăng xuất bằng form xác nhận, xóa session và cookie phiên.
- Bảo vệ cả bốn trang quản lý sản phẩm: chưa đăng nhập sẽ chuyển về `login.php`.
- Dùng PDO Prepared Statements, escape HTML và token CSRF cho các form thay đổi dữ liệu.

## Cấu trúc

```text
TH_Buoi_05/
├── common/
│   ├── app.php             # Session, xác thực, kiểm tra form, CSRF
│   └── dbConnect.php       # Kết nối MySQL/PDO
├── model/
│   ├── product.php         # 5 hàm CRUD theo đề
│   └── user.php            # Tìm tài khoản, đăng ký và băm mật khẩu
├── view/
│   ├── header.php
│   ├── footer.php
│   ├── errors.php
│   └── product_form.php
├── assets/css/style.css
├── assets/js/register.js   # Hiển thị trạng thái điều kiện mật khẩu khi nhập
├── database.sql
├── index.php
├── register.php
├── login.php
├── logout.php
├── product_list.php
├── product_add.php
├── product_edit.php
└── product_delete.php
```

## Cách chạy

1. Chuẩn bị PHP 7.4 trở lên có `pdo_mysql`, MySQL hoặc MariaDB (ví dụ XAMPP).
2. Khởi động MySQL và import `database.sql` qua phpMyAdmin. File tạo database `shopping_cart`, bảng `products`, bảng `users`, 9 sản phẩm mẫu và một tài khoản mẫu.
3. Kiểm tra `common/dbConnect.php`: mặc định host `localhost`, port `3306`, database `shopping_cart`, user `root`, mật khẩu trống. Có thể ghi đè bằng các biến môi trường `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`.
4. Mở terminal trong thư mục **TH_Buoi_05** rồi chạy:

   ```powershell
   php -S 127.0.0.1:8005
   ```

5. Truy cập <http://127.0.0.1:8005>, chọn **Đăng nhập** hoặc **Đăng ký**.

**Tài khoản mẫu:** `admin` / `admin123`. Trong SQL chỉ lưu chuỗi hash được tạo bởi `password_hash()`, không lưu mật khẩu thuần. Tài khoản này phục vụ thực hành trên máy cá nhân.

TH4 và TH5 mặc định dùng chung database theo đề. Import lại SQL sẽ cập nhật 9 sản phẩm mẫu có ID từ 1 đến 9; các sản phẩm khác và tài khoản đã có được giữ lại. Nếu tên `admin` đã tồn tại, SQL giữ nguyên mật khẩu tài khoản đó. Để tách dữ liệu TH5, đổi tên database trong cả SQL và cấu hình kết nối trước khi import.

## Kiểm tra chức năng

1. Chưa đăng nhập, truy cập trực tiếp từng trang `product_list.php`, `product_add.php`, `product_edit.php?id=1`, `product_delete.php?id=1`: đều chuyển về đăng nhập.
2. Đăng ký tài khoản mới; thử tên trùng, tên trống, mật khẩu trống, xác nhận mật khẩu trống và hai mật khẩu không khớp. Thử mật khẩu dưới 8 hoặc trên 15 ký tự, thiếu chữ hoa, chữ thường, chữ số hoặc ký tự đặc biệt. Đăng ký hợp lệ chuyển đến trang đăng nhập và thông báo thành công.
3. Thử sai mật khẩu rồi đăng nhập hợp lệ: chuyển đến trang chủ, hiển thị tên tài khoản và thống kê. Từ trang chủ, chọn xem danh sách hoặc thêm sản phẩm.
4. Thêm, sửa và xóa một sản phẩm; thử giá bằng 0, số lượng âm hoặc thập phân, ID không tồn tại và hủy xóa.
5. Chọn Đăng xuất, xác nhận rồi truy cập lại trang quản lý: chuyển về đăng nhập.
