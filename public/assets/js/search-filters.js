document.addEventListener('DOMContentLoaded', function () {
    var checkboxes = document.querySelectorAll('[data-category-filter]');
    var cards = document.querySelectorAll('[data-opportunity-card]');
    var resultsText = document.querySelector('[data-results-text]');
    var emptyState = document.querySelector('[data-empty-state]');
    var clearButton = document.querySelector('[data-clear-filters]');

    function activeCategories() {
        return Array.from(checkboxes)
            .filter(function (checkbox) { return checkbox.checked; })
            .map(function (checkbox) { return checkbox.dataset.categoryFilter; });
    }

    function applyFilters() {
        var active = activeCategories();
        var visibleCount = 0;

        cards.forEach(function (card) {
            var visible = active.indexOf(card.dataset.category) !== -1;
            card.style.display = visible ? '' : 'none';
            if (visible) visibleCount += 1;
        });

        if (resultsText) {
            resultsText.textContent = visibleCount + ' oportunidades activas en este momento';
        }
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', applyFilters);
    });

    if (clearButton) {
        clearButton.addEventListener('click', function () {
            checkboxes.forEach(function (checkbox) { checkbox.checked = true; });
            applyFilters();
        });
    }

    applyFilters();
});
