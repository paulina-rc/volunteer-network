/**
 * Opportunity search filters.
 *
 * Filtering happens on the server (OpportunityController::search), so this file
 * only submits the form when a control changes. Without JavaScript the page
 * still works: the "Aplicar filtros" button submits, and the sort select is
 * bound to the form through its form attribute.
 */
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('[data-search-form]');
    if (!form) {
        return;
    }

    function submitForm() {
        form.submit();
    }

    // Checkboxes and the two selects apply immediately.
    form.querySelectorAll('[data-category-filter]').forEach(function (checkbox) {
        checkbox.addEventListener('change', submitForm);
    });

    var dateSelect = form.querySelector('[name="date"]');
    if (dateSelect) {
        dateSelect.addEventListener('change', submitForm);
    }

    var sortSelect = document.querySelector('[data-sort-select]');
    if (sortSelect) {
        sortSelect.addEventListener('change', submitForm);
    }

    // The text inputs wait for Enter or the button, so the page does not reload
    // on every keystroke.
    form.querySelectorAll('input[type="text"]').forEach(function (input) {
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                submitForm();
            }
        });
    });
});
