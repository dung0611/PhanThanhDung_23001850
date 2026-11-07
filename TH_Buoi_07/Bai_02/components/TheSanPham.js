// Script thường để mở bằng file:// mà không cần máy chủ hoặc bundler.
const TheSanPham = {
    props: {
        ten: { type: String, required: true },
        gia: { type: Number, required: true },
        anh: { type: String, default: 'assets/images/notebook.svg' },
        moTa: { type: String, default: 'Sản phẩm chưa có mô tả.' },
        tonKho: { type: Number, default: 0 }
    },
    data() {
        // Vue tạo đối tượng mới cho mỗi thẻ: mở mô tả không ảnh hưởng thẻ khác.
        return { hienMoTa: false, thongBaoMua: '' };
    },
    methods: {
        dinhDangTien(gia) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(gia);
        },
        mua() {
            if (this.tonKho <= 0) return;
            this.thongBaoMua = `Đã chọn “${this.ten}”. Đây là thao tác mua minh họa.`;
        }
    },
    template: `
        <article class="product-card">
            <div class="product-image"><img :src="anh" :alt="ten" width="320" height="320"><span class="badge" :class="{ out: tonKho <= 0 }">{{ tonKho > 0 ? 'Còn hàng' : 'Hết hàng' }}</span></div>
            <div class="product-body"><span class="product-code">Góc học tập / Bộ sưu tập 07</span><h2>{{ ten }}</h2><p class="price">{{ dinhDangTien(gia) }}</p><span class="stock">{{ tonKho > 0 ? 'Còn ' + tonKho + ' sản phẩm' : 'Tạm hết hàng' }}</span>
                <button type="button" class="link-button" :aria-expanded="hienMoTa" @click="hienMoTa = !hienMoTa">{{ hienMoTa ? 'Ẩn mô tả −' : 'Xem mô tả +' }}</button>
                <p v-if="hienMoTa" class="description">{{ moTa }}</p>
                <button type="button" class="primary wide" :disabled="tonKho <= 0" @click="mua" :aria-label="'Mua ' + ten">{{ tonKho > 0 ? 'Mua' : 'Mua · Hết hàng' }}</button>
                <p v-if="thongBaoMua" class="feedback" role="status">{{ thongBaoMua }}</p>
            </div>
        </article>`
};
