document.addEventListener('DOMContentLoaded', function () {
    var tabButtons = document.querySelectorAll('.tabs__button');
    var panels = document.querySelectorAll('.tabs__panel');

    tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var target = button.dataset.tab;
            tabButtons.forEach(function (b) { b.classList.toggle('tabs__button--active', b === button); });
            panels.forEach(function (p) { p.classList.toggle('tabs__panel--active', p.dataset.panel === target); });
        });
    });

    var roleFields = {
        volunteer: document.querySelector('[data-role-field="volunteer"]'),
        organization: document.querySelector('[data-role-field="organization"]'),
    };
    var roleInputs = {
        volunteer: document.getElementById('register-full-name'),
        organization: document.getElementById('register-organization-name'),
    };
    var roleRadios = document.querySelectorAll('input[name="role"]');

    function updateRoleFields() {
        var selected = document.querySelector('input[name="role"]:checked');
        var role = selected ? selected.value : 'volunteer';

        Object.keys(roleFields).forEach(function (key) {
            var visible = key === role;
            roleFields[key].classList.toggle('hidden', !visible);
            roleInputs[key].required = visible;
        });
    }

    roleRadios.forEach(function (radio) {
        radio.addEventListener('change', updateRoleFields);
    });

    updateRoleFields();
});
