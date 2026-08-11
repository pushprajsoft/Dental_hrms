(function () {
    const header = document.getElementById('header');
    let lastScrollY = window.scrollY;
    let ticking = false;
    const SCROLL_THRESHOLD = 10;

    function handleScroll() {
        const currentScrollY = window.scrollY;
        const diff = currentScrollY - lastScrollY;

        if (Math.abs(diff) > SCROLL_THRESHOLD) {
            if (diff > 0 && currentScrollY > 80) {
                header.classList.add('header-hidden');
                document.body.classList.add('header-is-hidden');
            } else {
                header.classList.remove('header-hidden');
                document.body.classList.remove('header-is-hidden');
            }
            lastScrollY = currentScrollY;
        }
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (!ticking) {
            requestAnimationFrame(handleScroll);
            ticking = true;
        }
    }, { passive: true });
})();