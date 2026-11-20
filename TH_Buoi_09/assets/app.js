'use strict';

const API_URL = 'api/products.php';
const UPLOAD_URL = 'api/upload.php';
const MAX_INT = 2147483647;
const MAX_IMAGE_SIZE = 2 * 1024 * 1024;
const currency = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });

Vue.createApp({
    data() {
        return {
            products: [],
            pagination: { page: 1, limit: 5, total: 0, total_pages: 1 },
            form: { name: '', price: '', stock: 0 },
            editingId: null,
            selectedFile: null,
            currentImage: '',
            imagePreview: '',
            objectUrl: '',
            pendingUploadId: null,
            loading: false,
            saving: false,
            loaded: false,
            error: '',
            imageError: '',
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
                throw new Error('Máy chủ không trả về JSON hợp lệ. Hãy mở trang qua máy chủ PHP.');
            }
            if (!response.ok || !result || result.success !== true) {
                throw new Error(result?.message || 'Không thể thực hiện yêu cầu.');
            }
            return result;
        },
        async loadProducts(page = this.pagination.page) {
            this.loading = true;
            this.error = '';
            try {
                const result = await this.request(`${API_URL}?page=${page}&limit=5`);
                if (!Array.isArray(result.data) || !result.pagination || !Number.isInteger(result.pagination.page)) {
                    throw new Error('Danh sách sản phẩm trả về không hợp lệ.');
                }
                this.products = result.data;
                this.pagination = result.pagination;
                this.loaded = true;
            } catch (error) {
                this.error = error.message;
            } finally {
                this.loading = false;
            }
        },
        changePage(page) {
            if (this.busy || page < 1 || page > this.pagination.total_pages || page === this.pagination.page) return;
            this.loadProducts(page);
        },
        clearImageSelection() {
            if (this.objectUrl) URL.revokeObjectURL(this.objectUrl);
            this.objectUrl = '';
            this.selectedFile = null;
            if (this.$refs.imageInput) this.$refs.imageInput.value = '';
        },
        resetForm() {
            this.clearImageSelection();
            this.editingId = null;
            this.pendingUploadId = null;
            this.currentImage = '';
            this.imagePreview = '';
            this.imageError = '';
            this.form = { name: '', price: '', stock: 0 };
        },
        editProduct(product) {
            if (this.busy) return;
            this.resetForm();
            this.editingId = product.id;
            this.form = { name: product.name, price: product.price, stock: product.stock };
            this.currentImage = product.image || '';
            this.imagePreview = this.currentImage;
            this.error = '';
            this.success = '';
            this.$nextTick(() => this.$refs.nameInput.focus());
        },
        onImageChange(event) {
            const file = event.target.files?.[0];
            this.clearImageSelection();
            this.imageError = '';
            this.error = '';
            if (!file) {
                this.imagePreview = this.currentImage;
                return;
            }
            if (!['image/jpeg', 'image/png'].includes(file.type) || !/\.(jpe?g|png)$/i.test(file.name)) {
                this.imageError = 'Chỉ chọn ảnh JPG, JPEG hoặc PNG.';
                this.imagePreview = this.currentImage;
                event.target.value = '';
                return;
            }
            if (file.size < 1 || file.size > MAX_IMAGE_SIZE) {
                this.imageError = 'Ảnh phải có dung lượng tối đa 2 MB.';
                this.imagePreview = this.currentImage;
                event.target.value = '';
                return;
            }
            this.selectedFile = file;
            this.objectUrl = URL.createObjectURL(file);
            this.imagePreview = this.objectUrl;
        },
        ignoreImageError() {
            this.clearImageSelection();
            this.imageError = '';
            this.imagePreview = this.currentImage;
        },
        async uploadImage(productId) {
            const data = new FormData();
            data.append('product_id', String(productId));
            data.append('image', this.selectedFile);
            return this.request(UPLOAD_URL, { method: 'POST', body: data });
        },
        async retryUpload() {
            if (this.busy || !this.pendingUploadId || !this.selectedFile) return;
            this.saving = true;
            this.error = '';
            this.imageError = '';
            try {
                await this.uploadImage(this.pendingUploadId);
                this.resetForm();
                await this.loadProducts(this.pagination.page);
                this.success = 'Ảnh đã được tải lên thành công.';
            } catch (error) {
                this.imageError = error.message;
            } finally {
                this.saving = false;
            }
        },
        async saveProduct() {
            if (this.busy) return;
            if (this.imageError && !this.selectedFile) return;
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
                const result = await this.request(isEditing ? `${API_URL}?id=${this.editingId}` : API_URL, {
                    method: isEditing ? 'PUT' : 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(product),
                });
                const savedId = isEditing ? this.editingId : result.data?.id;
                const targetPage = isEditing ? this.pagination.page : 1;
                let uploadError = '';
                if (this.selectedFile) {
                    try {
                        await this.uploadImage(savedId);
                    } catch (error) {
                        uploadError = error.message;
                        this.pendingUploadId = savedId;
                        this.editingId = savedId;
                    }
                }
                if (!uploadError) this.resetForm();
                await this.loadProducts(targetPage);
                if (uploadError) {
                    this.error = 'Thông tin sản phẩm đã lưu, nhưng ảnh chưa tải lên.';
                    this.imageError = uploadError;
                    this.success = '';
                } else {
                    this.success = result.message;
                }
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
                await this.loadProducts(this.pagination.page);
                this.success = result.message;
            } catch (error) {
                this.deleteError = error.message;
            } finally {
                this.saving = false;
            }
        },
    },
    mounted() {
        if (window.location.protocol === 'file:') {
            this.error = 'Hãy chạy Apache/PHP rồi truy cập http://localhost/TH_Buoi_09/ theo README.md.';
            return;
        }
        this.loadProducts(1);
    },
}).mount('#app');
