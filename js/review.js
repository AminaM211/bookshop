document.querySelector(".scroll-link").addEventListener("click", function(e) {
    e.preventDefault();
    document.querySelector(this.getAttribute('href')).scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
});

