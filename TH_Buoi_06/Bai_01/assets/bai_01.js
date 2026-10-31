// Dữ liệu mẫu: thay tên lớp bằng lớp thực tế của bạn.
const taoSinhVienMau = () => ({
    maSinhVien: '23001850',
    hoTen: 'Phan Thanh Dũng',
    lop: 'K68A3',
    anhDaiDien: 'assets/avatar.jpg',
    diemTrungBinh: 8.5,
    dangHoc: true
});

Vue.createApp({
    data() {
        return {
            sinhVien: taoSinhVienMau(),
            hienGioiThieu: true
        };
    },
    computed: {
        diemHopLe() {
            const diem = this.sinhVien.diemTrungBinh;
            return typeof diem === 'number' && Number.isFinite(diem) && diem >= 0 && diem <= 10;
        },
        // :class liên kết màu xếp loại với điểm trung bình.
        mauXepLoai() {
            if (!this.diemHopLe) return 'chua-xac-dinh';
            const diem = this.sinhVien.diemTrungBinh;
            if (diem >= 8) return 'gioi';
            if (diem >= 6.5) return 'kha';
            if (diem >= 5) return 'trung-binh';
            return 'chua-dat';
        }
    },
    methods: {
        khoiPhuc() {
            this.sinhVien = taoSinhVienMau();
            this.hienGioiThieu = true;
        },
        dungAnhMacDinh(event) {
            // Chỉ đổi ảnh hiển thị; giữ đường dẫn đã nhập để người dùng sửa.
            const duongDanMau = new URL('assets/avatar.svg', document.baseURI).href;
            if (event.target.src !== duongDanMau) event.target.src = duongDanMau;
        }
    }
}).mount('#app');
