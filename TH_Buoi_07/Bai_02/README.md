# Bài 2 — Danh mục sản phẩm bằng component

Phan Thanh Dũng · 23001850.

Mở `bai_02.html` trực tiếp bằng trình duyệt. Có Vue, CSS, ảnh SVG và mã nguồn riêng trong thư mục này, không dùng tài nguyên bên ngoài.

- Cha giữ 4 sản phẩm và binding các prop tên, giá, ảnh, mô tả, tồn kho xuống `<the-san-pham>`.
- Component tại `components/TheSanPham.js`; props có kiểu, có required và default.
- `data()` tạo trạng thái mở mô tả riêng cho từng thẻ. Thử mở hai thẻ rồi đóng một thẻ; thẻ còn lại vẫn mở.
- Kẹp giấy hết hàng có nhãn riêng và khóa nút Mua. Nút Mua trên thẻ còn hàng chỉ minh họa bằng thông báo.
- Nạp và đăng ký component trước khi mount trong `assets/bai_02.js`.

Có thể sao chép riêng cả thư mục này sang vị trí khác để chạy.
