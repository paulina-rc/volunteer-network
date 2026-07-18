document.addEventListener('DOMContentLoaded', function () {
    var enrollButton = document.querySelector('[data-enroll-button]');
    var form = document.querySelector('[data-enrollment-form]');
    var success = document.querySelector('[data-enrollment-success]');

    if (!enrollButton || !form || !success) {
        return;
    }

    enrollButton.addEventListener('click', function () {
        form.style.display = 'none';
        success.style.display = 'block';
    });
});
