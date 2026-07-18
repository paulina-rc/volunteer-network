document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('[data-publish-opportunity-form]');
    var formWrapper = document.querySelector('[data-publish-form]');
    var success = document.querySelector('[data-publish-success]');

    if (!form || !formWrapper || !success) {
        return;
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        formWrapper.style.display = 'none';
        success.style.display = 'block';
    });
});
