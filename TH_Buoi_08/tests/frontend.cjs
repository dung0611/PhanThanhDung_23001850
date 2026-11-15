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
        Intl,
        Vue: { createApp(options) { definition = options; return { mount() {} }; } },
        window: { location: { protocol: 'http:' } },
        fetch: async (url, options) => {
            calls.push({ url, ...options });
            assert.ok(queue.length, 'Unexpected network request');
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
        nameInput: { focus() {} },
        deleteDialog: { showModal() { dialogOpen = true; }, close() { dialogOpen = false; } },
    };
    const row = { id: 7, name: 'Bút bi', price: 5000, stock: 20 };

    queue = [{ success: true, data: [row] }];
    await app.loadProducts();
    assert.equal(app.products.length, 1);
    assert.equal(app.totalStock, 20);

    app.form = { name: '  Vở mới  ', price: 12000, stock: 0 };
    queue = [{ success: true, message: 'Added' }, { success: true, data: [row] }];
    await app.saveProduct();
    assert.equal(calls.at(-2).method, 'POST');
    assert.equal(JSON.parse(calls.at(-2).body).name, 'Vở mới');
    assert.equal(calls.at(-1).url, 'api/products.php');
    assert.equal(app.editingId, null);

    app.editProduct(row);
    app.form.price = 8000;
    assert.equal(row.price, 5000, 'Editing must not mutate the table before the API succeeds');
    queue = [{ success: true, message: 'Updated' }, { success: true, data: [{ ...row, price: 8000 }] }];
    await app.saveProduct();
    assert.equal(calls.at(-2).method, 'PUT');
    assert.equal(calls.at(-2).url, 'api/products.php?id=7');
    assert.equal(app.products[0].price, 8000);

    const count = calls.length;
    app.form = { name: 'Invalid', price: 5000, stock: -1 };
    await app.saveProduct();
    assert.equal(calls.length, count);
    assert.ok(app.error);
    app.deleteProduct(row);
    assert.equal(dialogOpen, true);
    assert.equal(app.pendingDelete.id, 7);
    assert.equal(calls.length, count, 'Opening confirmation must never send DELETE');
    app.cancelDelete();
    assert.equal(dialogOpen, false);
    assert.equal(app.pendingDelete, null);
    assert.equal(calls.length, count, 'Cancel must never send DELETE');

    app.deleteProduct(row);
    queue = [{ success: false, message: 'Delete API error' }];
    await app.confirmDelete();
    assert.equal(app.deleteError, 'Delete API error');
    assert.equal(dialogOpen, true);
    assert.equal(app.pendingDelete.id, 7);
    assert.equal(app.saving, false);

    queue = [{ success: true, message: 'Deleted' }, { success: true, data: [] }];
    await app.confirmDelete();
    assert.equal(calls.at(-2).method, 'DELETE');
    assert.equal(calls.at(-2).url, 'api/products.php?id=7');
    assert.equal(app.products.length, 0);
    assert.equal(dialogOpen, false);
    assert.equal(app.pendingDelete, null);

    app.form = { name: 'Keep form', price: 5000, stock: 1 };
    queue = [{ success: false, message: 'API error example' }];
    await app.saveProduct();
    assert.equal(app.error, 'API error example');
    assert.equal(app.form.name, 'Keep form');
    assert.equal(app.saving, false);

    // Compile the actual Vue template using the shipped Vue build.
    const vueContext = vm.createContext({ console });
    vm.runInContext(fs.readFileSync(path.join(root, 'assets/vendor/vue.global.prod.js'), 'utf8'), vueContext);
    const html = fs.readFileSync(path.join(root, 'index.html'), 'utf8');
    const template = html.match(/<main\b[^>]*>([\s\S]*?)<\/main>/)[1];
    const errors = [];
    vueContext.Vue.compile(template, { onError: error => errors.push(error) });
    assert.equal(errors.length, 0, errors.map(error => error.message).join('\n'));
    assert.equal(queue.length, 0);
    console.log('PASS: Vue template, initial GET, POST/PUT/DELETE and refresh, edit isolation, validation, cancel, API errors.');
}

main().catch(error => { console.error(error); process.exitCode = 1; });
