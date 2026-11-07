const ungDungGioHang = Vue.createApp({
    data() {
        return {
            gioHang: [
                { id: 1, ten: 'Bút bi xanh', donGia: 5000, soLuong: 2 },
                { id: 2, ten: 'Vở kẻ ngang', donGia: 12000, soLuong: 3 }
            ],
            thongBao: ''
        };
    },
    computed: {
        tongSoLuong() { return this.gioHang.reduce((tong, sp) => tong + sp.soLuong, 0); },
        tongTien() { return this.gioHang.reduce((tong, sp) => tong + sp.donGia * sp.soLuong, 0); }
    },
    methods: {
        dinhDangTien(gia) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(gia);
        },
        doiSoLuong(id, soLuongMoi) {
            // Kiểm tra ở cha dù con đã giới hạn giá trị trước khi emit.
            const sp = this.gioHang.find(sp => sp.id === id);
            if (!sp || !Number.isInteger(soLuongMoi) || soLuongMoi < 1 || soLuongMoi > 99) {
                this.thongBao = 'Số lượng không hợp lệ. Chỉ nhận số nguyên từ 1 đến 99.';
                return;
            }
            sp.soLuong = soLuongMoi;
            this.thongBao = `Đã cập nhật số lượng “${sp.ten}”.`;
        },
        xoaSanPham(id) {
            const sp = this.gioHang.find(sp => sp.id === id);
            if (!sp) return;
            this.gioHang = this.gioHang.filter(sp => sp.id !== id);
            this.thongBao = `Đã xóa “${sp.ten}” khỏi giỏ.`;
        }
    }
});
ungDungGioHang.component('dong-gio-hang', DongGioHang);
ungDungGioHang.mount('#app');
