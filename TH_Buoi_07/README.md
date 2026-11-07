# TH_Buoi_07 — Bài tập thực hành Vue 3

**Sinh viên:** Phan Thanh Dũng · **MSSV:** 23001850.

## Chạy bài

Mở `index.html` bằng trình duyệt rồi chọn bài. Có thể mở trực tiếp:

- `Bai_01/bai_01.html`
- `Bai_02/bai_02.html`
- `Bai_03/bai_03.html`

Không cần npm hoặc Internet. **Mỗi bài là một thư mục độc lập**, có CSS, JavaScript, Vue 3.5.13 và giấy phép MIT riêng. Bài 2 có thêm ảnh SVG riêng. Có thể sao chép riêng `Bai_01`, `Bai_02` hoặc `Bai_03` sang vị trí khác rồi mở HTML trong đó; không cần hai bài còn lại hoặc thư mục cha. Các file JavaScript dùng script thường và `defer` để chạy được cả khi mở bằng `file://`.

```text
TH_Buoi_07/
├── Bai_01/  (bai_01.html, assets/, README.md)
├── Bai_02/  (bai_02.html, assets/, components/, README.md)
├── Bai_03/  (bai_03.html, assets/, components/, README.md)
├── index.html  (chỉ là trang chọn bài)
└── README.md
```

Nếu dùng máy chủ, mở terminal tại `TH_Buoi_07` rồi chạy:

```powershell
python -m http.server 8007 --bind 127.0.0.1
```

Truy cập <http://127.0.0.1:8007/>. Dữ liệu riêng của mỗi bài lưu trong bộ nhớ, tải lại trang sẽ khôi phục dữ liệu mẫu.

## Bài 1 — Danh sách sản phẩm có tìm kiếm và thống kê

Mã chính: `Bai_01/assets/bai_01.js`.

| Yêu cầu | Cách triển khai |
| --- | --- |
| Sản phẩm thuộc ít nhất 3 danh mục | 12 sản phẩm có `id`, `ten`, `gia`, `danhMuc`, `tonKho`; ban đầu 3 danh mục |
| Cột ID riêng | Hiển thị ID 4 chữ số bằng `String(sp.id).padStart(4, '0')`, ví dụ `0001`; giữ nguyên ID khi lọc/sắp xếp |
| Bảng và trạng thái tồn kho | `v-for`, `:key`, `:class`; nhãn xanh “Còn hàng”, đỏ “Hết hàng” |
| Tìm kiếm không phân biệt hoa thường | `v-model`, `trim()`, `toLocaleLowerCase('vi')`, `includes()` |
| Lọc danh mục và sắp xếp kết hợp | Computed `sanPhamHienThi` lọc theo cả tên và danh mục rồi sắp xếp |
| Không đổi thứ tự mảng gốc | `filter()` tạo mảng mới trước khi `sort()` |
| Thêm có kiểm tra | Method `themSanPham`: tên trim không rỗng, giá hữu hạn > 0, tồn kho nguyên >= 0; ô số trống bị từ chối |
| Thêm danh mục mới | Chọn “Khác (nhập danh mục mới)” rồi nhập tên; danh mục hợp lệ tự xuất hiện trong bộ lọc, không tạo trùng khi chỉ khác chữ hoa/thường |
| Xóa sau xác nhận | `yeuCauXoa` chỉ mở hộp xác nhận; `xacNhanXoa` mới xóa; có nút Hủy |
| Thống kê bằng computed | `soSanPhamHienThi`, `tongSoSanPham`, `tongGiaTriTonKho` tính từ danh sách đang hiển thị |
| Watch từ khóa | Báo khi đúng 1 ký tự, xóa thông báo khi rỗng hoặc từ 2 ký tự |
| Mounted | Đặt `trangThai` thành “Sẵn sàng” |

Kiểm tra thủ công:

1. Ban đầu: **12 / 12**, giá trị tồn kho **3.405.000 đ**; ID hiển thị từ `0001` đến `0012`.
2. Bộ lọc danh mục có `Văn phòng phẩm`, `Sổ & vở`, `Phụ kiện`, mỗi danh mục ban đầu có 4 sản phẩm. Tìm `BÚT`, chọn `Văn phòng phẩm`, sắp xếp giảm dần: Bút chì 2B rồi Bút bi xanh; **2 / 12**, **200.000 đ**. Đổi về thứ tự ban đầu để kiểm tra mảng gốc.
3. Tìm chuỗi không tồn tại: **0 / 12**, **0 đ**, thông báo không có sản phẩm phù hợp.
4. Nhập 1 ký tự rồi 2 ký tự hoặc xóa hết: thông báo xuất hiện rồi biến mất.
5. Thử tên toàn khoảng trắng, giá 0/âm/trống, tồn kho -1/1.5/trống: không thêm. Giá 10.000 và tồn kho 0 hợp lệ.
6. Nhấn Xóa rồi Hủy: dữ liệu giữ nguyên. Nhấn Xóa rồi Xác nhận xóa: đúng sản phẩm bị xóa, thống kê cập nhật theo bộ lọc. Xóa hết: **0 / 0**, **0 đ**.
7. Chọn danh mục “Khác”, nhập `Đồ thủ công`, thêm sản phẩm hợp lệ: danh mục này xuất hiện trong bộ lọc. Chọn nó để xem đúng sản phẩm vừa thêm. Để tên danh mục mới trống hoặc sản phẩm sai dữ liệu: không thêm sản phẩm hay danh mục.

## Bài 2 — Danh mục sản phẩm bằng component

Component: `Bai_02/components/TheSanPham.js`. Cha: `Bai_02/assets/bai_02.js`.

- Cha giữ 4 sản phẩm; binding `:ten`, `:gia`, `:anh`, `:mo-ta`, `:ton-kho` truyền dữ liệu xuống `<the-san-pham>`.
- Mỗi prop có kiểu `String` hoặc `Number`; `ten`, `gia` bắt buộc; `anh`, `moTa`, `tonKho` có giá trị mặc định.
- `data()` trả về `hienMoTa` riêng cho từng thẻ; mở/đóng nhiều mô tả độc lập.
- Sản phẩm hết hàng có nhãn đỏ và nút Mua bị khóa. Mua trên thẻ còn hàng hiển thị thông báo minh họa, không thực hiện giao dịch.
- HTML nạp Vue → `components/TheSanPham.js` → script cha; cha đăng ký bằng `.component()` trước `.mount()`.

Kiểm tra: mở mô tả thẻ thứ nhất rồi thẻ thứ hai; cả hai cùng mở. Đóng thẻ thứ nhất thì thẻ thứ hai vẫn mở. Kẹp giấy màu hết hàng không mua được.

## Bài 3 — Giỏ hàng với props và $emit

Component: `Bai_03/components/DongGioHang.js`. Cha: `Bai_03/assets/bai_03.js`.

- Cha quản lý mảng `gioHang` với `id`, `ten`, `donGia`, `soLuong`.
- Con nhận các prop tương ứng, dùng computed `thanhTien` và hiển thị nút tăng/giảm/xóa cùng ô nhập số lượng.
- Khai báo `emits`: `doi-so-luong(id, soLuongMoi)` và `yeu-cau-xoa(id)`.
- Con chỉ phát sự kiện; cha tìm đúng mã, kiểm tra số nguyên 1–99 rồi cập nhật dữ liệu. Con không gán lại prop hoặc sửa đối tượng từ cha.
- Nút giảm khóa ở 1, tăng khóa ở 99. Nhập trống, số âm, lẻ hoặc ngoài giới hạn sẽ trở về số lượng hiện tại.
- Cha dùng computed `tongSoLuong`, `tongTien`. Xóa hết hiển thị “Giỏ hàng trống”, các tổng về 0.

Giỏ ban đầu gồm **2 loại sản phẩm**, ID hiển thị **001** và **002**. Mỗi mục hiển thị ID, tên, đơn giá, số lượng và thành tiền. Với **2 bút × 5.000 + 3 vở × 12.000**, tổng số lượng là **5**, tổng tiền **46.000 đồng** theo ví dụ của đề. Tăng một bút bi xanh: tổng **6** sản phẩm, **51.000 đồng**. Thử các biên 1, 99, 0, 100, số lẻ và xóa từng dòng.

