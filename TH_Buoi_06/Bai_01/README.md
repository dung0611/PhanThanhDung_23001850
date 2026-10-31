# Thực hành số 6 – VueJS

## Bài 1: Thẻ thông tin sinh viên

**Sinh viên:** Phan Thanh Dũng · **MSSV:** 23001850.

Mở trực tiếp `bai_01.html` bằng trình duyệt. Không cần PHP, npm hay kết nối Internet: Vue 3.5.13 và ảnh đại diện được lưu trong `assets/`. Ảnh mặc định `assets/avatar.jpg` là ảnh sinh viên cung cấp; `assets/avatar.svg` là ảnh dự phòng khi đường dẫn ảnh không tải được.

Có thể chạy qua máy chủ cục bộ từ thư mục cha `TH_Buoi_06` (chứa thư mục `Bai_01`):

```powershell
python -m http.server 8006 --bind 127.0.0.1
```

Sau đó mở <http://127.0.0.1:8006/Bai_01/bai_01.html>.

## Đối chiếu yêu cầu

| Yêu cầu | Vị trí triển khai |
| --- | --- |
| `createApp`, `data()` với đủ 6 dữ liệu | `assets/bai_01.js`: `sinhVien` chứa mã, tên, lớp, ảnh, điểm, trạng thái |
| Họ tên bằng `{{ }}`, lớp bằng `v-text` | Tiêu đề thẻ và dòng lớp trong `bai_01.html` |
| `v-bind` cho ảnh và nội dung thay thế | `v-bind:src` và `v-bind:alt` trên ảnh đại diện |
| Xếp loại theo điểm | Chuỗi `v-if` / `v-else-if` / `v-else`, đúng mốc 8; 6,5; 5 |
| Đổi màu theo xếp loại | `:class="mauXepLoai"`: xanh lá, xanh dương, vàng nâu, đỏ |
| Đang học / Đã nghỉ | `v-if="sinhVien.dangHoc"` / `v-else` |
| Nút bật/tắt giới thiệu | `@click` đổi `hienGioiThieu`, `v-show` trên phần giới thiệu |

## Thử tương tác

- Sửa họ tên, mã sinh viên, tên lớp và đường dẫn ảnh trong biểu mẫu; thẻ thay đổi ngay nhờ `v-model`.
- Tên lớp `CNTT01` là dữ liệu mẫu, cần đổi theo lớp thực tế.
- Nhập các điểm `0`, `4.99`, `5`, `6.49`, `6.5`, `7.99`, `8`, `10` để kiểm tra ranh giới xếp loại và màu.
- Xóa điểm hoặc nhập điểm ngoài 0–10: hiển thị hướng dẫn và “Chưa xác định”, không xếp loại sai.
- Bật/tắt “Sinh viên đang học”, nhấn “Ẩn giới thiệu” / “Hiện giới thiệu”.
- Nhập đường dẫn ảnh sai: ảnh mẫu được dùng thay thế, nội dung thay thế vẫn gắn với họ tên.
- Nhấn “Khôi phục dữ liệu mẫu” để đưa thông tin và phần giới thiệu về ban đầu.

Dữ liệu chỉ lưu trong bộ nhớ của trang; tải lại trang sẽ khôi phục dữ liệu mẫu.

## Tài liệu và thư viện

- [Vue – Quick Start](https://vuejs.org/guide/quick-start.html)
- [Vue – Conditional Rendering](https://vuejs.org/guide/essentials/conditional.html)
- Vue 3.5.13: `assets/vendor/vue.global.prod.js`, lấy từ bản phân phối [unpkg](https://unpkg.com/vue@3.5.13/dist/vue.global.prod.js), giấy phép MIT kèm trong `assets/vendor/vue-LICENSE.txt`.
