# Bài 1 — Danh sách sản phẩm có tìm kiếm và thống kê

Phan Thanh Dũng · 23001850.

Mở `bai_01.html` trực tiếp bằng trình duyệt. Thư mục này độc lập, có Vue và CSS riêng trong `assets/`, không cần Internet hoặc các bài khác.

- 12 sản phẩm mẫu thuộc 3 danh mục, cột ID riêng hiển thị 4 chữ số: `0001`, `0002`, …, `0012`; sản phẩm mới tiếp tục từ `0013`. Ban đầu: **12 / 12**, tồn kho **3.405.000 đ**.
- Cột và ô nhập **Số lượng tồn kho** thể hiện số lượng từng sản phẩm. Ô thống kê **Tổng số lượng tồn kho** dùng computed cộng số lượng của các sản phẩm đang hiển thị: ban đầu **205**, lọc `BÚT` thuộc `Văn phòng phẩm` còn **40**, không có kết quả thì **0**.
- Chọn danh mục trong bộ lọc; kết hợp tìm tên không phân biệt hoa thường và sắp xếp giá. Thứ tự mảng gốc không đổi.
- Chọn **Khác (nhập danh mục mới)** ở form thêm sản phẩm để nhập tên danh mục. Khi thêm hợp lệ, danh mục mới tự xuất hiện trong bộ lọc. Tên trùng khác chữ hoa/thường dùng lại danh mục đã có.
- Tên sản phẩm không rỗng, giá > 0, tồn kho nguyên không âm. Danh mục mới không được trống.
- Xóa phải xác nhận. Thống kê dùng computed, thông báo một ký tự dùng watch, mounted đặt “Sẵn sàng”.
- Dữ liệu nằm trong bộ nhớ; tải lại sẽ khôi phục mẫu.

Kiểm tra: tìm `BÚT`, chọn `Văn phòng phẩm`, giá giảm dần → ID 2 rồi 1, **2 / 12**, **200.000 đ**. Tìm chuỗi không tồn tại → **0 / 12**, **0 đ**. Thêm `Keo dán`, giá 10.000, tồn 2, danh mục Khác → `Đồ thủ công`; lọc danh mục mới sẽ có 1 sản phẩm, giá trị tồn 20.000 đ.

Có thể chạy máy chủ riêng từ thư mục này:

```powershell
python -m http.server 8001 --bind 127.0.0.1
```

Mở <http://127.0.0.1:8001/bai_01.html>.
