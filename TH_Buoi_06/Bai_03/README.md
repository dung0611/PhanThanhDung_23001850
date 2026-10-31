# Bài 3 – Quản lý công việc cá nhân

**Sinh viên:** Phan Thanh Dũng · **MSSV:** 23001850.

Mở trực tiếp `bai_03.html` bằng trình duyệt. Vue 3.5.13 được lưu tại `assets/vendor/`, không cần npm hoặc Internet.

Nếu máy chủ đang chạy từ `TH_Buoi_06`, mở <http://127.0.0.1:8006/Bai_03/bai_03.html>. Để khởi động từ thư mục `TH_Buoi_06`:

```powershell
python -m http.server 8006 --bind 127.0.0.1
```

## Đối chiếu yêu cầu

| Yêu cầu | Triển khai |
| --- | --- |
| Dữ liệu công việc | `id`, `ten`, `daHoanThanh` trong `danhSachCongViec` |
| Thêm bằng nút hoặc Enter | Form `@submit.prevent="themCongViec"` và nút `type="submit"` |
| Chặn tên rỗng/khoảng trắng | `v-model.trim`, computed `tenCongViecHopLe`, kiểm tra `trim()` trong method |
| Danh sách với key ổn định | `v-for="congViec in danhSachDaLoc"`, `:key="congViec.id"` |
| Đánh dấu hoàn thành, gạch ngang | Checkbox `v-model="congViec.daHoanThanh"`, class `is-complete` |
| Xóa từng công việc | Method `xoaCongViec(id)` |
| Ba bộ lọc | Tất cả, Chưa hoàn thành, Đã hoàn thành; computed `danhSachDaLoc` |
| Đếm bằng computed | `tongSoCongViec`, `soViecChuaHoanThanh`, `soViecDaHoanThanh` |
| Xóa việc đã hoàn thành | Method `xoaCacViecDaHoanThanh`, nút khóa khi không có việc hoàn thành |
| Thông báo rỗng | Computed `thongBaoRong` phân biệt danh sách trống và từng bộ lọc trống |

## Kiểm tra

1. Ban đầu có 6 việc mẫu: 3 việc đầu đã hoàn thành, 3 việc còn lại chưa hoàn thành. Chọn từng bộ lọc và đối chiếu số lượng.
2. Thêm việc bằng **Thêm** hoặc **Enter**. Khoảng trắng ở hai đầu tên bị bỏ; tên rỗng hoặc chỉ có khoảng trắng không được thêm.
3. Đánh dấu hoặc bỏ đánh dấu checkbox: tên gạch ngang theo trạng thái, bộ đếm và tiến độ cập nhật ngay.
4. Xóa một việc ở từng bộ lọc: chỉ việc có đúng `id` bị xóa. Hai việc trùng tên vẫn có id riêng.
5. Nhấn **Xóa các việc đã hoàn thành**: chỉ xóa các việc đã xong, kể cả khi đang lọc việc chưa hoàn thành.
6. Khi chưa có việc hoàn thành, bộ lọc Đã hoàn thành hiển thị thông báo riêng. Khi mọi việc đã xong, bộ lọc Chưa hoàn thành hiển thị thông báo chúc mừng.
7. Xóa hết: tổng và số việc còn lại về 0, tiến độ 0%, hiển thị hướng dẫn thêm việc đầu tiên.
8. Thêm việc khi đang lọc Đã hoàn thành: tự chuyển về Tất cả để thấy việc mới.

Các thao tác không tải lại trang. Dữ liệu lưu trong bộ nhớ; tải lại trang khôi phục 6 việc mẫu. Vue dùng giấy phép MIT kèm tại `assets/vendor/vue-LICENSE.txt`.
