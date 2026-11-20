'use strict';
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

async function main() {
    const root = path.resolve(__dirname, '..');
    let definition;
    let queue = [];
    const calls = [];
    const context = vm.createContext({
        Intl, FormData, Blob,
        URL: { createObjectURL: () => 'blob:preview', revokeObjectURL() {} },
        Vue: { createApp(options) { definition = options; return { mount() {} }; } },
        window: { location: { protocol: 'http:' } },
        fetch: async (url, options) => {
            calls.push({ url, ...options });
            assert.ok(queue.length, `Unexpected request: ${url}`);
            const result = queue.shift();
            return { ok: result.success, json: async () => result };
        },
    });
    vm.runInContext(fs.readFileSync(path.join(root, 'assets/app.js'), 'utf8'), context);
    const app = definition.data();
    for (const [name, method] of Object.entries(definition.methods)) app[name] = method.bind(app);
    for (const [name, getter] of Object.entries(definition.computed)) {
        Object.defineProperty(app, name, { get: getter.bind(app) });
    }
    app.$nextTick = callback => callback();
    let dialogOpen = false;
    app.$refs = {
        nameInput: { focus() {} }, imageInput: { value: '' },
        deleteDialog: { showModal() { dialogOpen = true; }, close() { dialogOpen = false; } },
    };
    const row = id => ({ id, name: `Bút ${id}`, price: 5000, stock: 2, image: null });
    const page = (n, total, ids) => ({ success: true, data: ids.map(row), pagination: {
        page: n, limit: 5, total, total_pages: Math.max(1, Math.ceil(total / 5)),
    } });

    queue = [page(1, 12, [12, 11, 10, 9, 8])];
    await app.loadProducts(1);
    assert.equal(calls.at(-1).url, 'api/products.php?page=1&limit=5');
    assert.equal(app.pagination.total_pages, 3);
    assert.equal(app.products.length, 5);
    queue = [page(3, 12, [2, 1])];
    await app.loadProducts(3);
    assert.equal(app.pagination.page, 3);
    assert.equal(app.products.length, 2);

    app.form = { name: '  Vở mới  ', price: 12000, stock: 0 };
    queue = [{ success: true, message: 'Đã thêm', data: { id: 13 } }, page(1, 13, [13, 12, 11, 10, 9])];
    await app.saveProduct();
    assert.equal(calls.at(-2).method, 'POST');
    assert.equal(JSON.parse(calls.at(-2).body).name, 'Vở mới');
    assert.equal(calls.at(-1).url, 'api/products.php?page=1&limit=5');
    assert.equal(app.editingId, null);

    const imageRow = { ...row(13), image: 'uploads/old.png' };
    app.editProduct(imageRow);
    assert.equal(app.imagePreview, 'uploads/old.png');
    app.form.price = 8000;
    queue = [{ success: true, message: 'Đã sửa' }, page(1, 13, [13, 12, 11, 10, 9])];
    await app.saveProduct();
    assert.equal(calls.at(-2).method, 'PUT');
    assert.equal(calls.at(-2).url, 'api/products.php?id=13');
    assert.equal(calls.filter(call => call.url === 'api/upload.php').length, 0);

    app.editProduct(imageRow);
    const badFile = { name: 'wrong.gif', type: 'image/gif', size: 100 };
    app.onImageChange({ target: { files: [badFile], value: 'wrong.gif' } });
    assert.equal(app.selectedFile, null);
    assert.equal(app.imagePreview, 'uploads/old.png');
    assert.ok(app.imageError.includes('JPG'));
    const largeFile = { name: 'big.png', type: 'image/png', size: 2 * 1024 * 1024 + 1 };
    app.onImageChange({ target: { files: [largeFile], value: 'big.png' } });
    assert.equal(app.selectedFile, null);
    assert.ok(app.imageError.includes('2 MB'));
    const beforeInvalidSave = calls.length;
    await app.saveProduct();
    assert.equal(calls.length, beforeInvalidSave, 'Invalid image selection must not silently save without an image');
    app.ignoreImageError();
    assert.equal(app.imageError, '');

    const file = new Blob(['valid placeholder'], { type: 'image/png' });
    file.name = 'image.png';
    app.onImageChange({ target: { files: [file], value: 'image.png' } });
    assert.equal(app.imagePreview, 'blob:preview');
    app.form.price = 9000;
    queue = [
        { success: true, message: 'Đã sửa' },
        { success: false, message: 'Ảnh lỗi' },
        page(1, 13, [13, 12, 11, 10, 9]),
    ];
    await app.saveProduct();
    assert.equal(calls.at(-2).url, 'api/upload.php');
    assert.equal(calls.at(-2).body.get('product_id'), '13');
    assert.equal(calls.at(-2).body.get('image').size, file.size);
    assert.equal(calls.at(-2).headers, undefined, 'Browser must set multipart boundary');
    assert.equal(app.pendingUploadId, 13);
    assert.equal(app.editingId, 13);
    assert.ok(app.error.includes('Thông tin sản phẩm đã lưu'));
    assert.equal(app.imageError, 'Ảnh lỗi', 'Vue must display the PHP error near the file input');
    queue = [{ success: true, message: 'Đã tải ảnh' }, page(1, 13, [13, 12, 11, 10, 9])];
    await app.retryUpload();
    assert.equal(calls.at(-2).url, 'api/upload.php');
    assert.equal(app.pendingUploadId, null);
    assert.equal(app.selectedFile, null);

    queue = [page(3, 11, [1])];
    await app.loadProducts(3);
    app.deleteProduct(row(1));
    assert.equal(dialogOpen, true);
    queue = [{ success: true, message: 'Đã xóa' }, page(2, 10, [6, 5, 4, 3, 2])];
    await app.confirmDelete();
    assert.equal(calls.at(-2).url, 'api/products.php?id=1');
    assert.equal(calls.at(-1).url, 'api/products.php?page=3&limit=5');
    assert.equal(app.pagination.page, 2);
    assert.equal(dialogOpen, false);
    queue = [page(1, 0, [])];
    await app.loadProducts(1);
    assert.equal(app.pagination.total, 0);
    assert.equal(app.pagination.page, 1);
    assert.equal(app.pagination.total_pages, 1);

    const vueContext = vm.createContext({ console, document: {
        createElement() { return {
            textContent: '',
            children: [],
            set innerHTML(value) {
                const decode = text => text.replace(/&quot;/g, '"').replace(/&amp;/g, '&');
                this.textContent = decode(value);
                const match = value.match(/foo="([\s\S]*)"/);
                this.children = match ? [{ getAttribute: () => decode(match[1]) }] : [];
            },
        }; },
    } });
    vm.runInContext(fs.readFileSync(path.join(root, 'assets/vendor/vue.global.prod.js'), 'utf8'), vueContext);
    const html = fs.readFileSync(path.join(root, 'index.html'), 'utf8');
    const template = html.match(/<main\b[^>]*>([\s\S]*?)<\/main>/)[1];
    const errors = [];
    vueContext.Vue.compile(template, { onError: error => errors.push(error) });
    assert.equal(errors.length, 0, errors.map(error => error.message).join('\n'));
    assert.equal(queue.length, 0);
    console.log('PASS: Vue template, pagination, add/edit/delete, image validation, upload failure and retry.');
}

main().catch(error => { console.error(error); process.exitCode = 1; });
