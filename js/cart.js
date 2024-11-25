document.querySelectorAll('.add').forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Voorkom dat de pagina opnieuw wordt geladen bij klik

        const bookId = this.dataset.productId;

        // this.disabled = true; // Zet de knop uit zodat deze niet opnieuw aangeklikt kan worden

        // Verstuur het product-ID naar de server met AJAX
        fetch('addtocart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ book_id: bookId })
        })
        .then(response => response.json())
        .then(data => {
            // Alleen tonen dat het product is toegevoegd, zonder pop-up
            if (data.success) {                
                document.querySelector('.cartpopup').style.display = 'block';
                // alert('syucces');
            } else {
                console.log('Failed to add book. ' + data.error);
                // this.disabled = false; // Re-enable the button if adding to cart fails
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong.');
            // this.disabled = false; // Re-enable the button if there is an error
        });
    });
});