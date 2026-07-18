document.addEventListener('DOMContentLoaded', function () {
    var filterButtons = document.querySelectorAll('[data-enrollment-filter]');
    var rows = document.querySelectorAll('[data-enrollment-row]');
    var emptyState = document.querySelector('[data-enrollment-empty]');
    var currentFilter = 'all';

    function updateCounts() {
        var counts = { all: rows.length, pending: 0, accepted: 0 };
        rows.forEach(function (row) {
            var status = row.dataset.status;
            if (status === 'pending') counts.pending += 1;
            if (status === 'accepted') counts.accepted += 1;
        });
        Object.keys(counts).forEach(function (key) {
            var el = document.querySelector('[data-count="' + key + '"]');
            if (el) el.textContent = counts[key];
        });
    }

    function applyFilter() {
        var visibleCount = 0;
        rows.forEach(function (row) {
            var visible = currentFilter === 'all' || row.dataset.status === currentFilter;
            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount += 1;
        });
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            currentFilter = button.dataset.enrollmentFilter;
            filterButtons.forEach(function (b) { b.classList.toggle('pill-filter--active', b === button); });
            applyFilter();
        });
    });

    function setRowStatus(row, status) {
        row.dataset.status = status;

        var badge = row.querySelector('[data-status-badge]');
        if (badge) {
            badge.textContent = ENROLLMENT_STATUS_LABELS[status];
            badge.style.background = ENROLLMENT_STATUS_COLORS[status].bg;
            badge.style.color = ENROLLMENT_STATUS_COLORS[status].text;
        }

        var actions = row.querySelector('[data-status-actions]');
        if (actions) {
            actions.style.display = status === 'pending' ? 'flex' : 'none';
        }

        updateCounts();
        applyFilter();
    }

    rows.forEach(function (row) {
        var acceptButton = row.querySelector('[data-accept-button]');
        var rejectButton = row.querySelector('[data-reject-button]');
        // TODO (Paso 6 del plan de desarrollo, RN05, RN06): conectar estos botones a
        // ?action=accept_enrollment / ?action=reject_enrollment (EnrollmentController)
        // en vez de este cambio de estado solo en el navegador.
        if (acceptButton) acceptButton.addEventListener('click', function () { setRowStatus(row, 'accepted'); });
        if (rejectButton) rejectButton.addEventListener('click', function () { setRowStatus(row, 'rejected'); });
    });
});
