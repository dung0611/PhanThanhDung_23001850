(() => {
    'use strict';

    const password = document.getElementById('password');
    const confirmation = document.getElementById('password_confirm');
    const lengthCount = document.getElementById('password-length-count');
    if (!password || !confirmation || !lengthCount) {
        return;
    }

    const ruleRows = {};
    document.querySelectorAll('[data-password-rule]').forEach((row) => {
        ruleRows[row.dataset.passwordRule] = row;
    });

    function updateRule(name, met) {
        const row = ruleRows[name];
        if (!row || row.classList.contains('is-met') === met) {
            return;
        }
        row.classList.toggle('is-met', met);
        row.classList.toggle('is-unmet', !met);
        row.querySelector('[data-rule-icon]').textContent = met ? '✓' : '✕';
        row.querySelector('[data-rule-status]').textContent = met ? 'Đạt' : 'Chưa đạt';
    }

    function updatePasswordRules() {
        const value = password.value;
        // Cùng cách đếm ký tự Unicode với kiểm tra phía PHP.
        const length = Array.from(value).length;
        lengthCount.textContent = `(${length} ký tự)`;
        updateRule('length', length >= 8 && length <= 15);
        updateRule('uppercase', /\p{Lu}/u.test(value));
        updateRule('lowercase', /\p{Ll}/u.test(value));
        updateRule('digit', /[0-9]/.test(value));
        updateRule('special', /[\p{P}\p{S}]/u.test(value));

        const matches = confirmation.value !== '' && confirmation.value === value;
        updateRule('confirmation', matches);
        confirmation.setCustomValidity(
            confirmation.value !== '' && !matches ? 'Mật khẩu xác nhận không khớp.' : ''
        );
    }

    [password, confirmation].forEach((input) => {
        input.addEventListener('input', updatePasswordRules);
        input.addEventListener('change', updatePasswordRules);
    });
    window.addEventListener('pageshow', updatePasswordRules);
    updatePasswordRules();
})();
