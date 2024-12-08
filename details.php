<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

include_once './classes/db.php';
include 'cartpopup.php';
include './classes/Admin.php';
include './classes/Book.php';
include './classes/Review.php';
include './classes/Order.php';

// Maak databaseverbinding
$db = new Database();
$conn = $db->connect();

// Haal de huidige gebruiker op
$email = $_SESSION['email'];
$user_id = $_SESSION['user_id'];

// Controleer of gebruiker admin is
$admin = new Admin($conn);
$isAdmin = $admin->isAdmin($email);

// books ophalen
$bookObj = new Book($conn);
$books = $bookObj->getBooksByGenre('romance', 10);


// Haal het book_id uit de URL
if (!isset($_GET['id'])) {
    exit("Book not found");
}
$book_id = $_GET['id'];

// Haal het boek op
$bookStatement = $conn->prepare('SELECT * FROM books WHERE id = ?');
$bookStatement->bind_param('i', $book_id);
$bookStatement->execute();
$bookResult = $bookStatement->get_result();
$book = $bookResult->fetch_assoc();

if (!$book) {
    exit("Book not found");
}

// Haal de auteur op
$author = null;
if ($book) {
    $authorStatement = $conn->prepare('SELECT * FROM authors WHERE id = ?');
    $authorStatement->bind_param('i', $book['author_id']);
    $authorStatement->execute();
    $authorResult = $authorStatement->get_result();
    $author = $authorResult->fetch_assoc();
}

// Haal de reviews op voor dit boek
$reviewObj = new Review($conn);
$allReviews = $reviewObj->getReviews($book_id);

$notpurchased = false;
$orderObj = new Order($conn, $user_id);
$purchased = $orderObj->purchased($user_id, $book_id);


// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> </title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="./css/details.css">
    <link rel="stylesheet" href="./css/inc.footer.css">
</head>
<body>
    <?php include 'inc.nav.php'; ?>

    <div class="book-details">
    <div class="detailsflex">
        <div class="book-details-image">
            <img src="<?php echo $book['image_URL']; ?>" alt="Book cover">
        </div>
        <div class="book-details-info">
            <h1><?php echo $book['title'];?></h1>
            <p class="subgenre"><?php echo $book['subgenre'];?></p>
            <p class="auteur"><?php echo $author ? $author['first_name'] . " " . $author['last_name'] : 'Author unknown'; ?></p>
            <div class="pricesection">
                <p class="price">€<?php echo $book['price'];?></p>
            </div>
            <!-- <p>Rating: 4.5</p> -->
            <!-- <p>Pages: 300</p> -->
            <div class="checkInfo">
                <div class="check-1">
                    <img src="./images/yes.png" alt="check">
                    <p>Delivery within 1 to 2 working days</p>
                </div>
                <div class="check-2">
                    <img src="./images/yes.png" alt="check">
                    <p><strong>Pick up after 1 hour</strong> in 127 stores.</p>
                </div>
            </div>

            <div class="add-to-cart"><a class="add" data-product-id="<?php echo $book['id']; ?>"><img src="./images/cart.svg" alt=""><p>Add to cart</p></a></div>
            


            <div class="cartInfo">
                <div class="info">
                    <img src="./images/shopping-bag.svg" alt="shopping bag">
                    <p>Easy to order</p>
                </div>
                <div class="info">
                    <img src="./images/truck.svg" alt="truck">
                    <p>Free home delivery from €30</p>
                </div>
                <div class="info">
                    <img src="./images/lock.svg" alt="lock">
                    <p>Secure payment</p>
                </div>
                <div class="info">
                    <img src="./images/home.svg" alt="home">
                    <p>Free delivery in our bookstore near you</p>
                </div>

            <?php if($isAdmin): ?>
            <div class="admin-panel">
                <a href="editproduct.php?id=<?php echo $book['id']?>"> Edit details </a>
            </div>
        <?php endif; ?>
            </div>
        </div>
        </div>
            <div class="secondflex">
                <div class="omschrijving">
                    <h3>Summary</h3>
                    <p><?php echo $book['detailed_description'];?></p>
                </div>
                <div class="specificaties">
                    <h3>Specifications</h3>
                    <div class="">
                        <h4>Those involved</h4>
                        <div class="author flex">
                            <p>Auteur: </p>
                            <p class="author"><?php echo $author ? $author['first_name'] . " " . $author['last_name'] : 'Author unknown'; ?></p>
                        </div>
                        <div class="uitgeverij flex">
                            <p>Publisher: </p>
                            <p>Gallery Books</p>
                        </div>  
                    </div>
                    <h4>Features</h4>
                    <div class="flex grey">
                        <p>ISBN: </p>
                        <p><?php echo $book['ISBN'];?></p>
                    </div>
                <div class="flex white">
                        <p>Published date: </p>
                        <p><?php echo $book['published_date'];?></p>
                </div>
                <div class="flex grey">
                        <p>Type: </p>
                        <p><?php echo $book['Type'];?></p>
                    </div>
                </div>
            </div>
        
      
        <div class="author-info">
            <div class="author-info-image">
                <img src="<?php echo $author['profile_picture']; ?>" alt="author image">
            </div>
            <div class="author-info-details">
                <h4><?php echo  $author['first_name'] . " " . $author['last_name']; ?></h4>
                <p><?php echo $author['biography'];?></p>
            </div>
        </div>


        <div class="review" id="scroll-to">
                <img src="./images/stars.svg" alt="star">
                <button class="write-review">
                    <img src="./images/write.svg" alt="write" class="writeimg">
                    <a href="#scroll-to" class="scroll-link">write a review</a>
                </button>
        </div>
        <p class="hiddenpurchase">You must purchase this book to leave a review.</p>
        <div class="review-form-and-list">
        <?php if ($purchased): ?>
        <div class="review-form">
        <h4>Write a review</h4>
        <p class="inf"><span>*</span> Indicates a required field</p>
        <form id="reviewForm" class="form-postreview">
            <div class="score">
                <p><span>*</span> Score: </p>
                <div class="stars">
                    <input type="radio" id="star5" name="score" value="5" data-rating="1" required>
                    <label for="star5"></label>
                    <input type="radio" id="star4" name="score" value="4" data-rating="2">
                    <label for="star4"></label>
                    <input type="radio" id="star3" name="score" value="3" data-rating="3">
                    <label for="star3"></label>
                    <input type="radio" id="star2" name="score" value="2" data-rating="4">
                    <label for="star2"></label>
                    <input type="radio" id="star1" name="score" value="1" data-rating="5" checked>
                    <label for="star1"></label>
                </div>
            </div>
            <label for="Book"> Book: </label>
            <input readonly class="booktitle" placeholder="<?php echo $book['title']; ?>" name="booktitle">
            <label for="Title"><span>*</span> Title: </label>
            <input required type="text" name="title">
            <label for="review"><span>*</span> Comment:</label>
            <textarea required name="review"></textarea>
            <button class="post" id="btnAddComment" type="submit">Post</button>
        </form>
        <div id="responseMessage"></div> <!-- To display success or error message -->
    </div>
    <?php else: ?>
        <?php $notpurchased = true; ?>
    <?php endif; ?>

       <!-- Display Reviews -->
       <div class="reviews">
        <h4>Reviews (<?php echo count($allReviews); ?>)</h4>
        <?php foreach ($allReviews as $review): ?>
            <li class="displayreview">
                <div class="reviewflex">
                    <p id="reviewname"><strong><?php echo htmlspecialchars($review['name']); ?></strong></p>
                    <p id="reviewscore">
                        <?php echo str_repeat('<img src="./images/star.svg" alt="star" style="width: 15px;">', $review['score']); ?>
                        <?php echo str_repeat('<img src="./images/greystar.svg" alt="star" style="width: 15px; padding-left: 2px;">', 5 - $review['score']); ?>
                    </p>
                </div>
                <p id="reviewtitle"><?php echo htmlspecialchars($review['title']); ?></p>
                <p id="reviewcomment"><?php echo htmlspecialchars($review['comment']); ?></p>
            </li>
        <?php endforeach; ?>
        </div>
    </div>
    </div>

    <section class="bestsellers">
        <div class="section-header">
            <h2>More books from <span>Romance</span></h2>
            <a href="all.php?genre=romance" class="view-all">View All <img src="./images/rightarrow.svg" alt=""></a>
        </div>

        <div class="scroll-container">
            <button class="scroll-btn left-btn"><img src="./images/leftarrow.svg" alt=""></button>
            <div class="products">
                <?php if (!empty($books)): ?>
                    <?php foreach($books as $book): ?>
                        <div class="product-item">
                            <a href="details.php?id=<?php echo $book['id']?>"><img src="<?php echo $book['image_URL']; ?>" alt="Book cover"></a>
                            <div class="product-info">
                                <a class="product-title" href="details.php?id=<?php echo $book['id']?>"><h3><?php echo $book['title']; ?></h3></a>
                                <div class="subgenre"><?php echo ($book['subgenre']); ?></div>
                                <div class="product-author">
                                    <?php echo $book['first_name'] . " " . $book['last_name'];?>
                                </div>
                                <div class="product-price">€<?php echo number_format($book['price'], 2); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="scroll-btn right-btn"><img src="./images/rightarrow.svg" alt=""></button> 
        </div>
    </section>

    <?php include 'inc.footer.php'; ?>

    <script src="./js/index.js"></script>
    <script src="./js/cart.js"></script>
    <script>
    const notPurchased = <?php echo $notpurchased ? 'true' : 'false'; ?>;
    document.querySelector(".scroll-link").addEventListener("click", function(e) {
    e.preventDefault();
    if (notPurchased) {
        document.querySelector('.hiddenpurchase').style.display = 'inline-block';
    } else {
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
    });

    document.getElementById("reviewForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let book_id = <?php echo $book_id; ?>;
    let comment = document.querySelector('textarea[name="review"]').value;
    let score = document.querySelector('input[name="score"]:checked').value;
    let title = document.querySelector('input[name="title"]').value;

    // Get form data
    let formData = new FormData();
    formData.append('book_id', <?php echo $book_id; ?>);
    formData.append('name', '<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'name'; ?>');
    formData.append('comment', comment);
    formData.append('score', score);
    formData.append('title', title);

    // Post the review
    fetch("AJAX/savereview.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.json())
        .then(result => {
            let newReview = document.createElement('li');
            newReview.classList.add('displayreview');

            let reviewFlex = document.createElement('div');
            reviewFlex.classList.add('reviewflex');
            newReview.appendChild(reviewFlex);

            let newName = document.createElement('p');
            newName.id = 'reviewname';
            newName.innerHTML = `<strong>${result.name}</strong>`;
            reviewFlex.appendChild(newName);

            let newScore = document.createElement('p');
            newScore.id = 'reviewscore';
            newScore.innerHTML = 
                '<img src="./images/star.svg" alt="star" style="width: 20px;">'.repeat(result.score) + 
                '<img src="./images/greystar.svg" alt="star" style="width: 20px; padding-left: 2px;">'.repeat(5 - result.score);
            reviewFlex.appendChild(newScore);

            let newTitle = document.createElement('p');
            newTitle.id = 'reviewtitle';
            newTitle.textContent = result.title;
            newReview.appendChild(newTitle);

            let newComment = document.createElement('p');
            newComment.id = 'reviewcomment';
            newComment.textContent = result.body;
            newReview.appendChild(newComment);

            document.querySelector('.reviews').appendChild(newReview);

            document.querySelector('textarea[name="review"]').value = '';
            document.querySelector('input[name="score"]:checked').checked = false;
            document.querySelector('input[name="title"]').value = '';
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });

    </script>
</body>
</html>
