const ungDungDanhMuc = Vue.createApp({
    data() {
        return {
            sanPham: [
                { id: 1, ten: 'Bút bi xanh', gia: 5000, anh: 'assets/images/pen.svg', moTa: 'Ngòi 0,5 mm, mực xanh đều nét. Thân bút nhẹ, vừa tay cho những trang viết hằng ngày.', tonKho: 40 },
                { id: 2, ten: 'Sổ tay bìa vải', gia: 65000, anh: 'assets/images/notebook.svg', moTa: 'Sổ A5 bìa vải xanh rêu, 120 trang giấy kem. Một nơi nhỏ để lưu lại những ý tưởng lớn.', tonKho: 12 },
                { id: 3, ten: 'Túi đựng bút', gia: 45000, anh: 'assets/images/pouch.svg', moTa: 'Túi vải canvas màu tự nhiên, khóa kéo chắc chắn. Giữ góc học tập và balô luôn gọn gàng.', tonKho: 8 },
                { id: 4, ten: 'Kẹp giấy màu', gia: 18000, anh: 'assets/images/clips.svg', moTa: 'Hộp 20 kẹp giấy nhiều màu, giúp phân loại tài liệu và đánh dấu các trang yêu thích.', tonKho: 0 }
            ]
        };
    }
});
// Nạp TheSanPham.js và đăng ký component trước khi mount.
ungDungDanhMuc.component('the-san-pham', TheSanPham);
ungDungDanhMuc.mount('#app');
