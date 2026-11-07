# Bài 3 — Giỏ hàng với props và $emit

Phan Thanh Dũng · 23001850.

Mở `bai_03.html` trực tiếp bằng trình duyệt. Có Vue, CSS và mã nguồn riêng trong thư mục này, không cần hai bài khác hoặc Internet.

- Cha tại `assets/bai_03.js` giữ giỏ hàng; component tại `components/DongGioHang.js` nhận props và phát sự kiện.
- Con phát `doi-so-luong(id, soLuongMoi)` và `yeu-cau-xoa(id)`, không sửa props. Cha kiểm tra mã và số lượng nguyên từ 1 đến 99 trước khi cập nhật.
- Giỏ ban đầu có **2 loại sản phẩm**, ID **001** (Bút bi xanh) và **002** (Vở kẻ ngang). Mỗi mục hiển thị ID, tên, đơn giá, số lượng và thành tiền. ID giữ nguyên khi tăng/giảm hoặc xóa sản phẩm khác.
- **2 bút × 5.000 + 3 vở × 12.000 = 46.000 đ**, tổng số lượng **5**. Tăng một bút bi xanh → **6**, **51.000 đ**.
- Thử nhập 0, 100, số lẻ hoặc để trống: ô nhập trở về số lượng hợp lệ hiện tại. Nút giảm/tăng khóa tại 1/99.
- Xóa hết hiện **Giỏ hàng trống**, các tổng về 0.
- Dữ liệu nằm trong bộ nhớ; tải lại sẽ khôi phục mẫu.

Có thể sao chép riêng cả thư mục này sang vị trí khác để chạy.
