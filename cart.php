<?php
session_start();
if($_SESSION['loggedin'] !== true){
    header('Location: login.php');
    exit();
}

include 'inc.nav.php';

$conn = new mysqli('localhost', 'root', '', 'bookstore');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Assuming you have a cart table with a user_id column
$userId = $_SESSION['user_id']; // Make sure to store user ID in session during login
$sql = "SELECT books.* FROM books
        INNER JOIN cart ON books.id = cart.book_id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$cartBooks = $result->fetch_all(MYSQLI_ASSOC);


$email = $_SESSION['email'];
$userStatement = $conn->prepare('SELECT * FROM users WHERE email = ?');
$userStatement->bind_param('s', $email);
$userStatement->execute();
$userResult = $userStatement->get_result();
$user = $userResult->fetch_assoc(); // Verkrijg de gebruiker

// Genre selectie
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : 'all';

// Query boeken op basis van genre
if ($genreFilter === 'all') {
    $sql = "SELECT books.*, authors.first_name, authors.last_name 
            FROM books 
            LEFT JOIN authors ON books.author_id = authors.id
            ORDER BY RAND()";
} else {
    $sql = "SELECT books.*, authors.first_name, authors.last_name 
            FROM books 
            LEFT JOIN authors ON books.author_id = authors.id 
            WHERE category_id = (SELECT id FROM categories WHERE name = ?)";
}

$stmt = $conn->prepare($sql);

if ($genreFilter !== 'all') {
    $stmt->bind_param('s', htmlspecialchars($genreFilter));  // Bind het geselecteerde genre
}

$stmt->execute();
$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);

if(!isset($_GET['id']) || empty($_GET['id'])){
    $isCartEmpty = true; 
    $book = null;
    $author = null;
} else {
    $isCartEmpty = false;
    $book_id = $_GET['id']; 

    $bookStatement = $conn->prepare('SELECT * FROM books WHERE id = ?'); 
    $bookStatement->bind_param('i', $book_id); 
    $bookStatement->execute(); 
    $bookResult = $bookStatement->get_result(); 
    $book = $bookResult->fetch_assoc();

    //authors database linken
    $authorStatement = $conn->prepare('SELECT * FROM authors WHERE id = ?');
    $authorStatement->bind_param('i', $book['author_id']);
    $authorStatement->execute();
    $authorResult = $authorStatement->get_result();
    $author = $authorResult->fetch_assoc();
}

//authors database linken
// $authorStatement = $conn->prepare('SELECT * FROM authors WHERE id = ?');
// $authorStatement->bind_param('i', $book['author_id']);
// $authorStatement->execute();
// $authorResult = $authorStatement->get_result();
// $author = $authorResult->fetch_assoc();

$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Page</title>
    <link rel="stylesheet" href="./css/cart.css">
    <link rel="stylesheet" href="./css/inc.footer.css">
</head>
<body>
<nav>
        <input type="checkbox" id="check">
        <label for="check" class="checkbtn">
            <i><img src="./images/menu.svg" alt=""></i>
        </label>
        <div id="popup" class="filters">
            <a href="all.php?genre=all" class="filter-btn <?php echo ($genreFilter === 'all') ? 'active' : ''; ?>">All</a>
            <a href="all.php?genre=fiction" class="filter-btn <?php echo ($genreFilter === 'fiction') ? 'active' : ''; ?>">Fiction</a>
            <a href="all.php?genre=nonfiction" class="filter-btn <?php echo ($genreFilter === 'nonfiction') ? 'active' : ''; ?>">Non-Fiction</a>
            <a href="all.php?genre=romance" class="filter-btn <?php echo ($genreFilter === 'romance') ? 'active' : ''; ?>">Romance</a>
            <a href="all.php?genre=thriller" class="filter-btn <?php echo ($genreFilter === 'thriller') ? 'active' : ''; ?>">Thriller</a>
        </div>
    </nav>
    
    <div id="categories">
        <a href="all.php?genre=all" class="filter-btn <?php echo ($genreFilter === 'all') ? 'active' : ''; ?>">All</a>
        <a href="all.php?genre=fiction" class="filter-btn <?php echo ($genreFilter === 'fiction') ? 'active' : ''; ?>">Fiction</a>
        <a href="all.php?genre=nonfiction" class="filter-btn <?php echo ($genreFilter === 'nonfiction') ? 'active' : ''; ?>">Non-Fiction</a>
        <a href="all.php?genre=romance" class="filter-btn <?php echo ($genreFilter === 'romance') ? 'active' : ''; ?>">Romance</a>
        <a href="all.php?genre=thriller" class="filter-btn <?php echo ($genreFilter === 'thriller') ? 'active' : ''; ?>">Thriller</a>
    </div>

    <div class="cart-container">
        <div class="column-1">
            <?php if ($isCartEmpty): ?>
            <div class="empty-cart">
                <img class="cartimg" src="./images/emptycart.png" alt="">
                <h3>Your shopping cart is empty.</h3>
                <a href="index.php" class="btn-continue-shopping">Continue Shopping</a>
            </div>
            <?php else: ?>
            <h1>My shoppingcart</h1>
            <div class="column-book-cover">
                <a href="details.php?id=<?php echo $book['id']?>">
                    <img src="<?php echo $book['image_URL']; ?>" alt="Book cover">
                </a>
            </div>
            <div class="column-book-details">
                <div class="title-flex">
                    <h5><?php echo $book['title'];?></h5>
                    <a data-bind="css: { disabled: updating() }, click: updating() ? '' : removeFromBasketWithApproval.bind($data, 'ca5d943fbc094758ba20d6906cbac344')" data-action="remove" class="c-link c-link--minor c-basket-product__remove o-flex__fixed" href="#"> Verwijderen </a>
                </div>
                <p class="auteur"><?php echo  $author['first_name'] . " " . $author['last_name']; ?></p>
                <div class="type"><?php echo $book['Type']; ?> | <?php echo $book['subgenre']; ?></div>
                <div class="pricesection">
                    <p class="price">€<?php echo $book['price'];?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        
    </div>

    <footer>
        <div class="footer-section">
            <div class="foot">
                <h3>Klantendienst</h3>
                <ul>
                    <li><a href="#">Help</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Veelgestelde vragen</a></li>
                </ul>
            </div>
            <div class="foot">
                <h3>Over Ons</h3>
                <ul>
                    <li><a href="#">Ons Verhaal</a></li>
                    <li><a href="#">Ons Team</a></li>
                    <li><a href="#">Werken bij ons</a></li>
                </ul>
            </div>
            <div class="foot">
                <h3>Services</h3>
                <ul>
                    <li><a href="#">Gift Cards</a></li>
                    <li><a href="#">Verzending</a></li>
                    <li><a href="#">Retourneren</a></li>
                </ul>
            </div>
            <div class="foot">
                <h3>B2B</h3>
                <ul>
                    <li><a href="#">Bibliotheken</a></li>
                    <li><a href="#">Facturatie</a></li>
                </ul>
            </div>
            <div class="foot">
                <h3>Volg Ons</h3>
                <ul>
                    <li><a href="instagram.com">Instagram</a></li>
                    <li><a href="Facebook.com">Facebook</a></li>
                    <li><a href="Twitter.com">X</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <div class="kleineletters"> 
        <p>
            <a href="#">VERKOOPVOORWAARDEN</a>
            &nbsp;|&nbsp;
            <a href="#">PRIVACYVERKLARING</a>
            &nbsp;|&nbsp;
            <a href="#">DISCLAIMER</a>
            &nbsp;|&nbsp;
            <a href="#">COOKIEVERKLARING</a>
            &nbsp;|&nbsp;
            <a href="#">VOORWAARDEN VOOR REVIEWS</a>
        </p>
    </div>

   
    <div class="paymentmethods">
        <img src="./images/bancontact.png" alt="bancontact">
        <img src="./images/visa.png" alt="visa">
        <img src="./images/mastercard.png" alt="mastercard">
        <img src="./images/applepay.png" alt="applepay">
        <img src="./images/kbc.png" alt="kbc">
        <img src="./images/belfius.png" alt="belfius">
        <img src="./images/ideal.png" alt="ideal">
        <img src="./images/overschrijving.png" alt="overschrijving">
    </div>

   

    <div class="footer-bottom">
    <p>© 2024 Pageturners</p>

    <script src="./js/cart.js"></script>
</body>
</html>