const taoFormMacDinh = () => ({
    hoTen: '',
    suatChieu: '17:30',
    loaiVe: 'thuong',
    soLuong: 1,
    coCombo: false
});

Vue.createApp({
    data() {
        return {
            phim: {
                ten: 'Trại buôn người',
                theLoai: 'Hành động, Hồi hộp',
                thoiLuong: 135,
                poster: 'assets/trai-buon-nguoi.jpg'
            },
            cacSuatChieu: ['07:30', '10:00', '12:30', '15:00', '17:30', '20:00', '22:30'],
            bangGia: { thuong: 70000, vip: 100000 },
            giaCombo: 45000,
            form: taoFormMacDinh(),
            xacNhan: null,
            lichSuDatVe: [],
            idDonTiepTheo: 1
        };
    },
    computed: {
        soLuongHopLe() {
            return Number.isInteger(this.form.soLuong)
                && this.form.soLuong >= 1 && this.form.soLuong <= 10;
        },
        giaVe() {
            return this.bangGia[this.form.loaiVe] ?? 0;
        },
        tenLoaiVe() {
            return this.form.loaiVe === 'vip' ? 'Vé VIP' : 'Vé thường';
        },
        tienVe() {
            return this.soLuongHopLe ? this.giaVe * this.form.soLuong : 0;
        },
        tienCombo() {
            // Combo chỉ tính một lần cho cả đơn, không nhân số lượng vé.
            return this.form.coCombo ? this.giaCombo : 0;
        },
        tongTien() {
            return this.tienVe + this.tienCombo;
        },
        coTheDatVe() {
            return this.form.hoTen.trim().length > 0
                && this.soLuongHopLe
                && this.cacSuatChieu.includes(this.form.suatChieu)
                && Object.hasOwn(this.bangGia, this.form.loaiVe);
        }
    },
    methods: {
        dinhDangTien(soTien) {
            return new Intl.NumberFormat('vi-VN').format(soTien) + ' đồng';
        },
        tangSoLuong() {
            if (this.soLuongHopLe && this.form.soLuong < 10) this.form.soLuong++;
        },
        giamSoLuong() {
            if (this.soLuongHopLe && this.form.soLuong > 1) this.form.soLuong--;
        },
        datLaiForm() {
            this.form = taoFormMacDinh();
            this.xacNhan = null;
        },
        hienThiTieuDe(tenRef) {
            // Đợi Vue cập nhật màn hình rồi đưa tiêu đề vào vùng nhìn và nhận focus.
            this.$nextTick(() => {
                const tieuDe = this.$refs[tenRef];
                if (!tieuDe) return;
                tieuDe.focus({ preventScroll: true });
                tieuDe.scrollIntoView({ block: 'start' });
            });
        },
        veTrangChu() {
            this.datLaiForm();
            this.hienThiTieuDe('tieuDeTrang');
        },
        datVe() {
            // Kiểm tra lại cả khi gửi bằng Enter hoặc gọi method trực tiếp.
            if (!this.coTheDatVe) return;

            // Lưu bản chụp giá trị; sửa form sau đó không đổi xác nhận cũ.
            this.xacNhan = {
                id: this.idDonTiepTheo++,
                hoTen: this.form.hoTen.trim(),
                tenPhim: this.phim.ten,
                suatChieu: this.form.suatChieu,
                tenLoaiVe: this.tenLoaiVe,
                giaVe: this.giaVe,
                soLuong: this.form.soLuong,
                coCombo: this.form.coCombo,
                tienCombo: this.tienCombo,
                tongTien: this.tongTien
            };
            // Lưu từng đơn riêng; về trang chủ hoặc đặt lại form không xóa lịch sử.
            this.lichSuDatVe.unshift({ ...this.xacNhan });
            this.hienThiTieuDe('tieuDeXacNhan');
        }
    }
}).mount('#app');
