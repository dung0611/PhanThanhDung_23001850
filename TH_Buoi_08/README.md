# TH_Buoi_08 — Thực hành PHP và Vue.js

**Sinh viên:** Phan Thanh Dũng · **MSSV:** 23001850.

Ứng dụng quản lý sản phẩm dùng Vue 3, PHP, PDO và MySQL. Dữ liệu được lưu trong MySQL; giao diện gọi lại API sau mỗi lần thêm, sửa hoặc xóa, không tải lại trang. Vue 3.5.13 và giấy phép MIT được đặt trong `assets/vendor`, không cần npm hoặc Internet.

## Cấu trúc

```text
TH_Buoi_08/
├── database.sql          # Tạo CSDL, bảng và 3 sản phẩm mẫu (câu 1)
├── config/database.php   # Cấu hình kết nối PDO
├── api/products.php      # API JSON, kiểm tra dữ liệu và CRUD (câu 2)
├── index.html            # Bảng v-for, form v-model (câu 3)
├── assets/
│   ├── app.js            # Gọi API bằng fetch, xác nhận xóa, thông báo lỗi
│   ├── style.css
│   └── vendor/           # Vue và giấy phép
├── tests/
│   ├── frontend.cjs      # Kiểm tra logic giao diện bằng Node.js
│   └── api_test.py       # Kiểm thử API thật bằng Python (câu 4)
└── README.md
```

## Chạy bằng XAMPP

1. Sao chép nguyên thư mục `TH_Buoi_08` vào `C:\xampp\htdocs\`.
2. Mở XAMPP Control Panel, bật **Apache** và **MySQL**.
3. Mở `http://localhost/phpmyadmin/`, chọn **Import**, chọn `database.sql` rồi bấm **Go**. File tự tạo CSDL `th_buoi_08`.
4. Kiểm tra `config/database.php`: mặc định host `127.0.0.1`, cổng `3306`, CSDL `th_buoi_08`, tài khoản `root`, mật khẩu rỗng. Sửa nếu MySQL trên máy dùng thông tin khác.
5. Truy cập **http://localhost/TH_Buoi_08/**.

Không mở bằng `file://` hoặc VS Code Live Server vì các cách đó không chạy PHP. Cần PHP 7.4 trở lên có `pdo_mysql` và `ctype`, cùng MySQL/MariaDB (có sẵn trong XAMPP). Không cần `mbstring`. Chỉ import SQL khi cài bài; import lại sẽ đặt lại dữ liệu mẫu có ID 1–3.

Nếu đã có PHP trong PATH và MySQL đang chạy, sau khi import SQL, mở terminal tại `TH_Buoi_08`:

```powershell
php -S 127.0.0.1:8008 -t .
```

Sau đó mở `http://127.0.0.1:8008/`. Có thể đặt các biến môi trường `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` thay vì sửa file cấu hình.

## API

| Phương thức | Đường dẫn | Kết quả thành công |
| --- | --- | --- |
| GET | `api/products.php` | HTTP 200, `{ "success": true, "data": [...] }` |
| POST | `api/products.php` | HTTP 201, trả thông báo và sản phẩm mới trong `data` |
| PUT | `api/products.php?id=1` | HTTP 200, cập nhật sản phẩm theo ID |
| DELETE | `api/products.php?id=1` | HTTP 200, xóa sản phẩm theo ID |

POST và PUT nhận JSON, ví dụ:

```json
{ "name": "Bút chì", "price": 6000, "stock": 10 }
```

PHP kiểm tra trước khi kết nối/ghi CSDL: tên là chuỗi Unicode có 1–100 ký tự sau khi bỏ khoảng trắng hai đầu; `price` là số nguyên JSON > 0; `stock` là số nguyên JSON ≥ 0. Hai trường số tối đa 2.147.483.647 theo kiểu MySQL `INT`. Chuỗi `"5000"`, boolean, số thập phân, thiếu trường và `null` bị từ chối. Mọi truy vấn có tham số dùng PDO prepared statement.

Phản hồi lỗi có `success: false` và `message`: HTTP 400 (JSON hoặc ID sai), 422 (dữ liệu sai), 404 (sản phẩm cần sửa/xóa không tồn tại), 405 (phương thức không hỗ trợ), 500 (lỗi máy chủ/CSDL). Sửa mà giữ nguyên dữ liệu vẫn thành công.

## Kiểm tra trước khi nộp

| Thao tác | Kết quả mong đợi |
| --- | --- |
| Mở trang sau khi import SQL | Bút bi — 5.000 đ — 20; Vở ô ly — 12.000 đ — 15; Thước kẻ — 8.000 đ — 0 |
| Thêm sản phẩm hợp lệ, sau đó F5 | Sản phẩm mới vẫn xuất hiện vì đã lưu MySQL |
| Bấm Sửa, đổi đơn giá rồi Lưu | Bảng hiển thị đơn giá mới; F5 vẫn giữ giá đó |
| Sửa nhưng giữ nguyên dữ liệu | API trả thành công, không báo nhầm là không tồn tại |
| Bấm Xóa rồi chọn Hủy | Không gửi DELETE, sản phẩm giữ nguyên |
| Bấm Xóa, xác nhận, sau đó F5 | Sản phẩm đã xóa không còn trong danh sách |
| Nhập tên toàn khoảng trắng, giá 0 hoặc tồn kho -1 | Giao diện báo lỗi, không gửi dữ liệu sai |
| Gửi trực tiếp POST với `stock: -1` | HTTP 422, `success: false`, MySQL không có bản ghi mới |
| PUT/DELETE ID đã bị xóa | HTTP 404, có thông báo lỗi |
| Tắt MySQL rồi bấm Làm mới | Giao diện hiển thị lỗi API |

Để gửi trực tiếp yêu cầu sai, mở DevTools → Console trên trang ứng dụng:

```javascript
fetch('api/products.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name: 'Không được thêm', price: 5000, stock: -1 })
}).then(async response => console.log(response.status, await response.json()));
```

Kết quả: HTTP **422**, `success: false`, thông báo tồn kho không hợp lệ. Kiểm tra lại bảng trong phpMyAdmin để xác nhận không có sản phẩm này.

Kiểm tra tự động từ thư mục `TH_Buoi_08`:

```powershell
node tests/frontend.cjs
python tests/api_test.py http://localhost/TH_Buoi_08/
```

Nếu dùng máy chủ PHP tích hợp, thay URL bằng `http://127.0.0.1:8008/`. Kiểm thử API yêu cầu máy chủ PHP và MySQL thật đang chạy, tự tạo sản phẩm có tên ngẫu nhiên, kiểm tra thêm/đọc lại/sửa/xóa và dữ liệu sai, rồi dọn sản phẩm kiểm thử. Script không sửa hoặc xóa các sản phẩm có sẵn. Bài kiểm tra API đọc lại bằng yêu cầu GET mới; vẫn nên thực hiện F5 trong trình duyệt theo bảng trên.
