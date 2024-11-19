document.querySelector('#btnAddReview').addEventListener('click', function() {
    //postid?

    //commenttext?
    let postid = this.dataset.postid;
    let text = document.querySelector('#reviewText').value;
    //post naar database (AJAX)
    let formData = new FormData();

    formData.append("text", text);
    formData.append("postid", postid);


    fetch('AJAX/savereview.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        let newReview = document.createElement('li');
        newReview.innerHTML = result.body;
        document.querySelector('.post_reviews_list').appendChild(newReview);
    })
    .catch(error => {
        console.error('Error:', error);
    });
    //antwoord ok? toon comment onderaan
});