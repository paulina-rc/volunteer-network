document.addEventListener('DOMContentLoaded', function () {
    initHeroCarousel();
    initHowItWorksTabs();
});

function initHeroCarousel() {
    var slides = document.querySelectorAll('.hero__slide');
    var dots = document.querySelectorAll('.hero__dot');
    if (slides.length === 0) {
        return;
    }

    var current = 0;
    var intervalId = null;

    function goTo(index) {
        current = (index + slides.length) % slides.length;
        slides.forEach(function (slide, i) {
            slide.classList.toggle('hero__slide--active', i === current);
        });
        dots.forEach(function (dot, i) {
            dot.classList.toggle('hero__dot--active', i === current);
        });
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    function startAutoplay() {
        intervalId = setInterval(next, 6000);
    }
    function stopAutoplay() {
        clearInterval(intervalId);
    }

    var prevButton = document.querySelector('[data-hero-prev]');
    var nextButton = document.querySelector('[data-hero-next]');
    if (prevButton) prevButton.addEventListener('click', function () { prev(); stopAutoplay(); startAutoplay(); });
    if (nextButton) nextButton.addEventListener('click', function () { next(); stopAutoplay(); startAutoplay(); });

    dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () { goTo(i); stopAutoplay(); startAutoplay(); });
    });

    startAutoplay();
}

function initHowItWorksTabs() {
    var tabButtons = document.querySelectorAll('[data-how-tab]');
    var panels = document.querySelectorAll('[data-how-panel]');

    tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var target = button.dataset.howTab;

            tabButtons.forEach(function (b) {
                b.classList.toggle('pill-filter--active', b === button);
            });
            panels.forEach(function (panel) {
                panel.style.display = panel.dataset.howPanel === target ? 'grid' : 'none';
            });
        });
    });
}
