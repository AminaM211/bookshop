    // Add to Cart button click event using AJAX
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const bookId = this.closest('form').querySelector('input[name="id"]').value;
            
            // Send AJAX request to addtocart.php
            fetch('addtocart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'id': bookId
                })
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Display the response, e.g., "Success" or "Already in cart"
            })
            .catch(error => console.error('Error:', error));
        });
    });
