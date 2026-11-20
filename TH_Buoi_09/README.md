# TH_Buoi_09 — Upload ảnh và phân trang sản phẩm

**Sinh viên:** Phan Thanh Dũng · **MSSV:** 23001850.

Bài này tiếp tục ứng dụng CRUD PHP, PDO, MySQL và Vue 3 của buổi 08. Thư mục buổi 08 được giữ nguyên. Buổi 09 có cơ sở dữ liệu riêng `th_buoi_09` với 20 sản phẩm mẫu (12 ban đầu và 8 bổ sung). Dữ liệu cài mới có 4 trang, mỗi trang đúng một sản phẩm hết hàng.

## Cài đặt bằng XAMPP

1. Sao chép nguyên thư mục `TH_Buoi_09` vào `D:\xampp\htdocs\` trên máy này (hoặc thư mục `htdocs` của XAMPP nếu cài ở vị trí khác).
2. Bật Apache và MySQL trong XAMPP.
3. Mở `http://localhost/phpmyadmin/`, import `database.sql`. File tạo CSDL `th_buoi_09`, bảng `products` có cột `image VARCHAR(255) NULL`, và 20 sản phẩm mẫu.
4. Mở `http://localhost/TH_Buoi_09/`.

Mặc định `config/database.php` dùng host `127.0.0.1`, cổng `3306`, user `root` và mật khẩu rỗng. Có thể sửa file này hoặc đặt biến môi trường `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`. Cần PHP có `pdo_mysql`, `fileinfo` và `ctype`; cần cho phép ghi vào thư mục `uploads`. Cấu hình PHP `upload_max_filesize` và `post_max_size` phải lớn hơn 2 MB để nhận được ảnh 2 MB. Vue đã có sẵn trong `assets/vendor`, không cần npm hay Internet.

Nếu muốn dùng tiếp **dữ liệu MySQL của buổi 08** thay vì CSDL mới: chạy `ALTER TABLE th_buoi_08.products ADD COLUMN image VARCHAR(255) NULL;` một lần trong phpMyAdmin, bổ sung sản phẩm mẫu đến tối thiểu 12 bản ghi, và đặt `DB_NAME=th_buoi_08` cho buổi 09. Không import `database.sql` của buổi 09 vào CSDL buổi 08.

## API

| Phương thức | URL | Mô tả |
| --- | --- | --- |
| GET | `api/products.php?page=1&limit=5` | Trả `{success, data, pagination}`; sắp xếp ID giảm dần, 5 sản phẩm mỗi trang |
| POST | `api/products.php` | Nhận JSON `{name, price, stock}`; trả `data.id` mới |
| PUT | `api/products.php?id=ID` | Sửa thông tin bằng JSON; không thay ảnh nếu không upload |
| DELETE | `api/products.php?id=ID` | Xóa sản phẩm |
| POST | `api/upload.php` | Nhận multipart/form-data với `product_id` và `image`; trả đường dẫn trong `data.image` |

GET mặc định trang 1, giới hạn 5. `page` phải là số nguyên dương, `limit` nếu có phải đúng `5`. Trang vượt quá trang cuối được đưa về trang cuối. Với bảng rỗng, `pagination` là `{page:1, limit:5, total:0, total_pages:1}` và `data` là mảng rỗng.

Ảnh là tùy chọn, tối đa một ảnh cho mỗi sản phẩm. Vue kiểm tra phần mở rộng, MIME do trình duyệt cung cấp và dung lượng tối đa 2 MB trước khi gửi. Lỗi chọn ảnh hiển thị ngay dưới ô chọn file; chọn ảnh hợp lệ hoặc bấm **Tiếp tục không có ảnh** để lưu sản phẩm. PHP kiểm tra lại dung lượng và loại ảnh thực tế bằng `getimagesize` cùng `finfo`, chỉ nhận JPEG/PNG; lỗi PHP trả về cũng hiển thị dưới ô chọn file. File có tên ngẫu nhiên trong `uploads`; đường dẫn ảnh cũ chỉ đổi sau khi lưu file mới và cập nhật CSDL thành công. Nếu upload lỗi sau khi lưu thông tin, giao diện giữ sản phẩm và file đã chọn để bấm **Thử tải ảnh lại**.

## Kiểm tra

Từ thư mục `TH_Buoi_09`:

```powershell
node tests/frontend.cjs
python tests/api_test.py http://localhost/TH_Buoi_09/
```

Kiểm thử API cần Apache/PHP và MySQL đang chạy. Ngoài kiểm thử tự động, hãy thử chọn ảnh PNG thật trong trình duyệt; sửa sản phẩm không chọn ảnh; chuyển ba trang 5, 5, 2; và xóa bản ghi duy nhất trên trang cuối của một bộ dữ liệu thử. Giao diện cập nhật bằng fetch, không tải lại toàn trang.
