<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

include_once './classes/db.php';
include 'cartpopup.php';
include './classes/Admin.php';
include './classes/Review.php';
include './classes/Book.php';

// Maak databaseverbinding
$db = new Database();
$conn = $db->connect();

// Haal de huidige gebruiker op
$email = $_SESSION['email'];

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

// als de form wordt gepost, worden de reviews opgeslagen


// Haal alle reviews op
$reviews = Review::getAll($conn, $book_id);

// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - Details</title>
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


        <div class="review">
                <img src="./images/stars.svg" alt="star">
                <div class="write-review">
                    <img src="./images/write.svg" alt="write" class="writeimg">
                    <a href="#">write a review</a>
                </div>
        </div>
 <!--
        <div class="review-form">
            <h4>Write a review</h4>
            <p class="inf" ><span>*</span> Indicates a required field</p>
            <form class="form-postreview" action="submit_review.php" method="post">
                <div class="score">
                    <p><span>*</span> Score: </p>
                    <div class="stars">
                        <input type="radio" id="star5" name="score" value="5" data-rating=1 required>
                        <label for="star5"></label>
                        <input type="radio" id="star4" name="score" value="4" data-rating=2>
                        <label for="star4"></label>
                        <input type="radio" id="star3" name="score" value="3" data-rating=3>
                        <label for="star3"></label>
                        <input type="radio" id="star2" name="score" value="2" data-rating=4>
                        <label for="star2"></label>
                        <input type="radio" id="star1" name="score" value="1" data-rating=5>
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
        </div>-->

        <div class=postt">
            <div class="post__comments">
                <div class="post__comment__form">
                    <input type="text" name="title">
                    <a href="#" id="btnAddComment">Add Review</a>
                </div>
                
                <ul class="post__comments__likes">
                    <li>This is a first comment</li>
                </ul>
            </div>
        </div>

        <div class="reviews-list">
            <h4>Reviews (<?php echo count($reviews); ?>)</h4>
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <p><strong><?php echo htmlspecialchars($review['user_id']); ?></strong></p>
                        <p>Title: <?php echo htmlspecialchars($review['title']); ?></p>
                        <p>Comment: <?php echo htmlspecialchars($review['comment']); ?></p>
                        <p>Score: <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews yet.</p>
            <?php endif; ?>
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
    <!-- <script src="./js/review.js"></script> -->
    <script>
        document.querySelectorAll('.postt').forEach(anchor => {
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
    </script>
</body>
</html>
