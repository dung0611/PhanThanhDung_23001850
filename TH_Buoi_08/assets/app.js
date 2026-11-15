'use strict';

const API_URL = 'api/products.php';
const MAX_INT = 2147483647;
const currency = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });

Vue.createApp({
    data() {
        return {
            products: [],
            form: { name: '', price: '', stock: 0 },
            editingId: null,
            loading: false,
            saving: false,
            loaded: false,
            error: '',
            success: '',
            pendingDelete: null,
            deleteError: '',
        };
    },
    computed: {
        busy() { return this.loading || this.saving; },
        totalStock() { return this.products.reduce((total, product) => total + product.stock, 0); },
        outOfStock() { return this.products.filter(product => product.stock === 0).length; },
    },
    methods: {
        formatPrice(value) { return currency.format(value); },
        async request(url = API_URL, options = {}) {
            let response;
            try {
                response = await fetch(url, { cache: 'no-store', ...options });
            } catch (error) {
                throw new Error('Không thể kết nối API. Hãy kiểm tra máy chủ PHP và kết nối mạng.');
            }
            let result;
            try {
                result = await response.json();
            } catch (error) {
                throw new Error('Máy chủ không trả về JSON hợp lệ. Hãy mở trang qua máy chủ PHP, không mở trực tiếp file HTML.');
            }
            if (!response.ok || !result || result.success !== true) {
                throw new Error(result?.message || 'Không thể thực hiện yêu cầu.');
            }
            return result;
        },
        async loadProducts() {
            this.loading = true;
            this.error = '';
            try {
                const result = await this.request();
                if (!Array.isArray(result.data)) throw new Error('Danh sách sản phẩm trả về không hợp lệ.');
                this.products = result.data;
                this.loaded = true;
            } catch (error) {
                this.error = error.message;
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.editingId = null;
            this.form = { name: '', price: '', stock: 0 };
        },
        editProduct(product) {
            if (this.busy) return;
            this.editingId = product.id;
            this.form = { name: product.name, price: product.price, stock: product.stock };
            this.error = '';
            this.success = '';
            this.$nextTick(() => this.$refs.nameInput.focus());
        },
        async saveProduct() {
            if (this.busy) return;
            this.error = '';
            this.success = '';
            const product = { name: this.form.name.trim(), price: this.form.price, stock: this.form.stock };
            if (Array.from(product.name).length < 1 || Array.from(product.name).length > 100) {
                this.error = 'Tên sản phẩm sau khi bỏ khoảng trắng phải có từ 1 đến 100 ký tự.';
                return;
            }
            if (!Number.isInteger(product.price) || product.price < 1 || product.price > MAX_INT) {
                this.error = 'Đơn giá phải là số nguyên từ 1 đến 2.147.483.647 đồng.';
                return;
            }
            if (!Number.isInteger(product.stock) || product.stock < 0 || product.stock > MAX_INT) {
                this.error = 'Số lượng tồn phải là số nguyên từ 0 đến 2.147.483.647.';
                return;
            }
            this.saving = true;
            try {
                const isEditing = this.editingId !== null;
                const url = isEditing ? `${API_URL}?id=${this.editingId}` : API_URL;
                const result = await this.request(url, {
                    method: isEditing ? 'PUT' : 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(product),
                });
                this.resetForm();
                this.success = result.message;
                await this.loadProducts();
            } catch (error) {
                this.error = error.message;
            } finally {
                this.saving = false;
            }
        },
        deleteProduct(product) {
            if (this.busy) return;
            this.pendingDelete = { id: product.id, name: product.name };
            this.deleteError = '';
            this.$nextTick(() => this.$refs.deleteDialog.showModal());
        },
        cancelDelete() {
            if (this.saving) return;
            this.$refs.deleteDialog.close();
            this.pendingDelete = null;
            this.deleteError = '';
        },
        async confirmDelete() {
            if (this.busy || this.pendingDelete === null) return;
            const product = this.pendingDelete;
            this.error = '';
            this.success = '';
            this.deleteError = '';
            this.saving = true;
            try {
                const result = await this.request(`${API_URL}?id=${product.id}`, { method: 'DELETE' });
                this.$refs.deleteDialog.close();
                this.pendingDelete = null;
                if (this.editingId === product.id) this.resetForm();
                this.success = result.message;
                await this.loadProducts();
            } catch (error) {
                this.deleteError = error.message;
            } finally {
                this.saving = false;
            }
        },
    },
    mounted() {
        if (window.location.protocol === 'file:') {
            this.error = 'Hãy chạy Apache/PHP rồi truy cập http://localhost/TH_Buoi_08/ theo README.md.';
            return;
        }
        this.loadProducts();
    },
}).mount('#app');
