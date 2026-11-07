Vue.createApp({
    data() {
        return {
            trangThai: 'Đang khởi tạo',
            tuKhoa: '', danhMucLoc: '', sapXep: 'mac-dinh',
            thongBaoTuKhoa: '', thongBaoThaoTac: '', loi: [],
            idTiepTheo: 13, idChoXoa: null,
            danhMuc: ['Văn phòng phẩm', 'Sổ & vở', 'Phụ kiện'],
            sanPhamMoi: { ten: '', gia: '', danhMuc: 'Văn phòng phẩm', danhMucKhac: '', tonKho: '' },
            sanPham: [
                { id: 1, ten: 'Bút bi xanh', gia: 5000, danhMuc: 'Văn phòng phẩm', tonKho: 40 },
                { id: 2, ten: 'Bút chì 2B', gia: 8000, danhMuc: 'Văn phòng phẩm', tonKho: 0 },
                { id: 3, ten: 'Vở kẻ ngang', gia: 12000, danhMuc: 'Sổ & vở', tonKho: 30 },
                { id: 4, ten: 'Sổ tay bìa vải', gia: 65000, danhMuc: 'Sổ & vở', tonKho: 12 },
                { id: 5, ten: 'Túi đựng bút', gia: 45000, danhMuc: 'Phụ kiện', tonKho: 8 },
                { id: 6, ten: 'Kẹp giấy màu', gia: 18000, danhMuc: 'Phụ kiện', tonKho: 0 },
                { id: 7, ten: 'Thước kẻ 30 cm', gia: 15000, danhMuc: 'Văn phòng phẩm', tonKho: 20 },
                { id: 8, ten: 'Tẩy trắng', gia: 6000, danhMuc: 'Văn phòng phẩm', tonKho: 25 },
                { id: 9, ten: 'Vở ô ly', gia: 15000, danhMuc: 'Sổ & vở', tonKho: 25 },
                { id: 10, ten: 'Sổ lò xo A5', gia: 35000, danhMuc: 'Sổ & vở', tonKho: 10 },
                { id: 11, ten: 'Giấy ghi chú', gia: 22000, danhMuc: 'Phụ kiện', tonKho: 15 },
                { id: 12, ten: 'Bìa hồ sơ', gia: 10000, danhMuc: 'Phụ kiện', tonKho: 20 }
            ]
        };
    },
    computed: {
        sanPhamHienThi() {
            const tuKhoa = this.tuKhoa.trim().toLocaleLowerCase('vi');
            // filter tạo mảng mới; sort chỉ tác động lên mảng kết quả này.
            const ketQua = this.sanPham.filter(sp =>
                sp.ten.toLocaleLowerCase('vi').includes(tuKhoa) &&
                (!this.danhMucLoc || sp.danhMuc === this.danhMucLoc));
            if (this.sapXep === 'tang') ketQua.sort((a, b) => a.gia - b.gia);
            if (this.sapXep === 'giam') ketQua.sort((a, b) => b.gia - a.gia);
            return ketQua;
        },
        soSanPhamHienThi() { return this.sanPhamHienThi.length; },
        tongSoSanPham() { return this.sanPham.length; },
        tongSoLuongTonKho() {
            return this.sanPhamHienThi.reduce((tong, sp) => tong + sp.tonKho, 0);
        },
        tongGiaTriTonKho() {
            return this.sanPhamHienThi.reduce((tong, sp) => tong + sp.gia * sp.tonKho, 0);
        },
        sanPhamChoXoa() { return this.sanPham.find(sp => sp.id === this.idChoXoa); }
    },
    watch: {
        tuKhoa(giaTriMoi) {
            this.thongBaoTuKhoa = Array.from(giaTriMoi).length === 1
                ? 'Từ khóa chỉ có một ký tự. Bạn có thể nhập thêm để tìm chính xác hơn.' : '';
        }
    },
    methods: {
        dinhDangTien(giaTri) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(giaTri);
        },
        datLaiBoLoc() { this.tuKhoa = ''; this.danhMucLoc = ''; this.sapXep = 'mac-dinh'; },
        themSanPham() {
            const ten = this.sanPhamMoi.ten.trim();
            const gia = Number(this.sanPhamMoi.gia);
            const tonKho = Number(this.sanPhamMoi.tonKho);
            const laDanhMucKhac = this.sanPhamMoi.danhMuc === '__khac__';
            const danhMucNhap = laDanhMucKhac
                ? (this.sanPhamMoi.danhMucKhac || '').trim().replace(/\s+/g, ' ')
                : this.sanPhamMoi.danhMuc;
            // Dùng lại tên chuẩn nếu danh mục đã tồn tại, kể cả khác chữ hoa/thường.
            const danhMucDaCo = this.danhMuc.find(dm =>
                dm.toLocaleLowerCase('vi') === danhMucNhap.toLocaleLowerCase('vi'));
            this.loi = [];
            this.thongBaoThaoTac = '';
            if (!ten) this.loi.push('Tên sản phẩm không được để trống.');
            if (String(this.sanPhamMoi.gia).trim() === '' || !Number.isFinite(gia) || gia <= 0)
                this.loi.push('Giá phải là số lớn hơn 0.');
            if (String(this.sanPhamMoi.tonKho).trim() === '' || !Number.isInteger(tonKho) || tonKho < 0)
                this.loi.push('Tồn kho phải là số nguyên không âm.');
            if (laDanhMucKhac && !danhMucNhap) this.loi.push('Vui lòng nhập tên danh mục mới.');
            if (!laDanhMucKhac && !danhMucDaCo) this.loi.push('Danh mục không hợp lệ.');
            if (this.loi.length) return;
            const danhMuc = danhMucDaCo || danhMucNhap;
            // Chỉ thêm danh mục sau khi toàn bộ dữ liệu sản phẩm đã hợp lệ.
            if (!danhMucDaCo) this.danhMuc.push(danhMuc);
            this.sanPham.push({ id: this.idTiepTheo++, ten, gia, danhMuc, tonKho });
            this.sanPhamMoi = { ten: '', gia: '', danhMuc, danhMucKhac: '', tonKho: '' };
            this.thongBaoThaoTac = `Đã thêm “${ten}”.`;
        },
        yeuCauXoa(id) {
            this.idChoXoa = id;
            this.$nextTick(() => this.$refs.nutHuyXoa?.focus());
        },
        huyXoa() { this.idChoXoa = null; },
        xacNhanXoa() {
            if (!this.sanPhamChoXoa) return;
            const ten = this.sanPhamChoXoa.ten;
            this.sanPham = this.sanPham.filter(sp => sp.id !== this.idChoXoa);
            this.idChoXoa = null;
            this.thongBaoThaoTac = `Đã xóa “${ten}”.`;
        }
    },
    mounted() { this.trangThai = 'Sẵn sàng'; }
}).mount('#app');
