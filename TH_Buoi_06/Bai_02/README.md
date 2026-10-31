# Bài 2 – Đặt vé xem phim

**Sinh viên:** Phan Thanh Dũng · **MSSV:** 23001850.

Mở trực tiếp `bai_02.html` bằng trình duyệt. Vue 3.5.13 được lưu tại `assets/vendor/`, không cần Internet hoặc npm.

Nếu máy chủ đang chạy từ `TH_Buoi_06`, truy cập <http://127.0.0.1:8006/Bai_02/bai_02.html>. Để khởi động máy chủ từ thư mục `TH_Buoi_06`:

```powershell
python -m http.server 8006 --bind 127.0.0.1
```

## Đối chiếu yêu cầu

| Yêu cầu | Triển khai |
| --- | --- |
| Họ tên tự bỏ khoảng trắng hai đầu | `v-model.trim="form.hoTen"` |
| Chọn suất chiếu (7 suất) | `select`: 07:30, 10:00, 12:30, 15:00, 17:30, 20:00, 22:30 |
| Radio chọn loại vé | Thường 70.000 đồng; VIP 100.000 đồng |
| Số lượng nguyên 1–10 | `v-model.number`, `min`, `max`, `step`; computed `soLuongHopLe` dùng `Number.isInteger` |
| Checkbox combo | 45.000 đồng cho cả đơn, chỉ tính một lần |
| Tính tiền bằng computed | `giaVe`, `tienVe`, `tienCombo`, `tongTien` |
| Tăng, giảm, đặt lại | Methods `tangSoLuong`, `giamSoLuong`, `datLaiForm` |
| Chỉ bật nút Đặt vé khi hợp lệ | `:disabled="!coTheDatVe"` |
| Xác nhận ngay khi gửi | `@submit.prevent="datVe"`, lưu thông tin vào `xacNhan` và hiển thị bằng `v-if` |

## Kiểm tra

1. Nhập họ tên, chọn **Vé VIP**, nhập **2** vé, chọn combo: tổng tiền **245.000 đồng**. Nhấn **Đặt vé** để xem xác nhận.
2. Vé thường, 1 vé, không combo: **70.000 đồng**. 10 vé VIP kèm combo: **1.045.000 đồng**.
3. Nhập tên chỉ gồm khoảng trắng; số lượng trống, 0, 11, -1, 1.5: nút đặt vé bị khóa và không tạo xác nhận mới.
4. Nút giảm dừng ở 1, nút tăng dừng ở 10. Đổi vé, số lượng, combo sẽ tính lại tổng ngay.
5. Sau khi đặt thành công, form được ẩn, thông tin vé hiện lên phía trên và tiêu đề xác nhận được cuộn tới, nhận focus. Nhấn **Về trang chủ** để trở lại form trống, sẵn sàng đặt vé mới.
6. **Đặt lại form** xóa tên và xác nhận; khôi phục suất 17:30, vé thường, 1 vé, không combo.
7. Sau khi chọn **Về trang chủ**, mục **Thông tin vé bạn đã đặt** hiện phía trên form, gồm khách hàng, phim, suất chiếu, loại vé, giá vé, số lượng, combo và tổng tiền. Đặt thêm sẽ bổ sung đơn mới lên đầu; quay về hoặc đặt lại form không xóa lịch sử. Lịch sử giữ trong bộ nhớ của trang, tải lại trang sẽ xóa.

Bộ phim: **Trại buôn người**, thể loại Hành động, Hồi hộp, thời lượng 135 phút; thông tin và poster tham khảo từ [Touch Cinema](https://touchcinema.com/phim/trai-buon-nguoi). Poster lưu tại `assets/trai-buon-nguoi.jpg` và hiển thị trọn ảnh.

Bảy suất chiếu là dữ liệu mẫu phục vụ bài tập, không phải lịch chiếu thực tế của rạp. Đây là ứng dụng thực hành, không đặt vé hoặc thanh toán thật. Dữ liệu chỉ giữ trong bộ nhớ của trang.

## Cấu trúc

```text
Bai_02/
├── bai_02.html
├── README.md
└── assets/
    ├── bai_02.js
    ├── style.css
    ├── trai-buon-nguoi.jpg
    └── vendor/
        ├── vue.global.prod.js
        └── vue-LICENSE.txt
```

Vue 3.5.13 dùng giấy phép MIT, kèm tại `assets/vendor/vue-LICENSE.txt`.
