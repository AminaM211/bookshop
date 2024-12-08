document.addEventListener('DOMContentLoaded', function () {
const productsContainer = document.querySelector('.products');
const leftButton = document.querySelector('.left-btn');
const rightButton = document.querySelector('.right-btn');

const scrollAmount = productsContainer.offsetWidth * 0.50; // 75% van de containerbreedte

leftButton.addEventListener('click', () => {
    productsContainer.scrollBy({
        left: -scrollAmount,
        behavior: 'smooth'
    });
});

rightButton.addEventListener('click', () => {
    productsContainer.scrollBy({
        left: scrollAmount,
        behavior: 'smooth'
    });
});
});
