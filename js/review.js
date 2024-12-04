document.querySelectorAll('.scroll-link').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector(this.getAttribute('href')).scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
    });
});

document.querySelector('#btnAddComment').addEventListener('click', function() {
    console_log('hi'); 
    alert('hi');  
});