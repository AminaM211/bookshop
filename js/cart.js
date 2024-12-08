document.querySelectorAll('.add').forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault(); 

        const bookId = this.dataset.productId;

        fetch('addtocart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ book_id: bookId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {                
                document.querySelector('.cartpopup').style.display = 'block';
            } else {
                console.log('Failed to add book. ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});