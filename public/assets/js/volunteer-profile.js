document.addEventListener('DOMContentLoaded', function () {
    var tabButtons = document.querySelectorAll('[data-profile-tab]');
    var panels = document.querySelectorAll('[data-profile-panel]');
    var displayByPanel = { enrollments: 'flex', skills: 'block' };

    tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var target = button.dataset.profileTab;

            tabButtons.forEach(function (b) {
                b.classList.toggle('pill-filter--active', b === button);
            });
            panels.forEach(function (panel) {
                var key = panel.dataset.profilePanel;
                panel.style.display = key === target ? displayByPanel[key] : 'none';
            });
        });
    });
});
