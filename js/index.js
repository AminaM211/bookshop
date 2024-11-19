//PREVENT DEFAULT ON ANCHOR TAGS
// document.querySelectorAll('a').forEach(anchor => {
//   anchor.addEventListener('click', function(e) {
//     if (this.getAttribute('href') === '#') {
//       e.preventDefault();
//     }
//   });
// });


document.getElementById('check').addEventListener('change', function() {
    var menuIcon = document.querySelector('.checkbtn img');
    if (this.checked) {
        menuIcon.src = './images/hovermenu.svg';
    } else {
        menuIcon.src = './images/menu.svg';
    }
});

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


