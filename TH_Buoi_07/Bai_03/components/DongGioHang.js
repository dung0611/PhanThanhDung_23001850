const DongGioHang = {
    props: {
        id: { type: Number, required: true },
        ten: { type: String, required: true },
        donGia: { type: Number, required: true },
        soLuong: { type: Number, required: true }
    },
    emits: {
        'doi-so-luong': (id, soLuongMoi) => Number.isInteger(id) && Number.isInteger(soLuongMoi) && soLuongMoi >= 1 && soLuongMoi <= 99,
        'yeu-cau-xoa': id => Number.isInteger(id)
    },
    computed: {
        thanhTien() { return this.donGia * this.soLuong; }
    },
    methods: {
        dinhDangTien(gia) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(gia);
        },
        yeuCauSoLuong(soLuongMoi) {
            // Con chỉ phát sự kiện, không gán lại bất kỳ prop nào.
            if (Number.isInteger(soLuongMoi) && soLuongMoi >= 1 && soLuongMoi <= 99)
                this.$emit('doi-so-luong', this.id, soLuongMoi);
        },
        nhapSoLuong(event) {
            const giaTri = event.target.value;
            const soLuongMoi = Number(giaTri);
            if (giaTri.trim() !== '') this.yeuCauSoLuong(soLuongMoi);
            // Khôi phục ô nhập từ prop; nếu hợp lệ, cha cập nhật prop ở lượt render tiếp theo.
            event.target.value = this.soLuong;
        }
    },
    template: `
        <article class="cart-row">
            <div class="cart-item-title"><span class="item-icon cart-product-id"><small>ID</small><strong>{{ String(id).padStart(3, '0') }}</strong></span><div><h3>{{ ten }}</h3><small>Đơn giá: {{ dinhDangTien(donGia) }}</small></div></div>
            <div class="cart-quantity"><label :for="'quantity-' + id">Số lượng</label><div class="quantity"><button type="button" :disabled="soLuong <= 1" @click="yeuCauSoLuong(soLuong - 1)" :aria-label="'Giảm số lượng ' + ten">−</button><input :id="'quantity-' + id" type="number" min="1" max="99" step="1" :value="soLuong" @change="nhapSoLuong" :aria-label="'Số lượng ' + ten"><button type="button" :disabled="soLuong >= 99" @click="yeuCauSoLuong(soLuong + 1)" :aria-label="'Tăng số lượng ' + ten">+</button></div></div>
            <div class="number cart-line-total"><small>Thành tiền</small><strong>{{ dinhDangTien(thanhTien) }}</strong></div>
            <button type="button" class="danger" @click="$emit('yeu-cau-xoa', id)" :aria-label="'Xóa ' + ten">Xóa</button>
        </article>`
};
