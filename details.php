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
    $stmt->bind_param('s', $genreFilter);  // Bind het geselecteerde genre
}

$stmt->execute();
$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);

if (!isset($_GET['id']) ){ 
    exit("Book not found"); 
} 

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


// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Online bookstore</title>
    <link rel="stylesheet" href="./css/details.css">
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

    <div class="book-details">
    <div class="detailsflex">
        <div class="book-details-image">
            <img src="<?php echo $book['image_URL']; ?>" alt="Book cover">
        </div>
        <div class="book-details-info">
            <h1><?php echo $book['title'];?></h1>
            <p class="subgenre"><?php echo $book['subgenre'];?></p>
            <p class="auteur"><?php echo  $author['first_name'] . " " . $author['last_name']; ?></p>
            <div class="pricesection">
                <p class="price">€<?php echo $book['price'];?></p>
            </div>
            <!-- <p>Rating: 4.5</p> -->
            <!-- <p>Pages: 300</p> -->
            <div class="checkInfo">
                <div class="check-1">
                    <img src="./images/yes.png" alt="check">
                    <p>Levering 1 à 2 werkdagen</p>
                </div>
                <div class="check-2">
                    <img src="./images/yes.png" alt="check">
                    <p><strong>Haal af na 1 uur</strong> in 127 winkel(s).</p>
                </div>
            </div>
            <div class="add-to-cart">
                <img src="./images/cart.svg" alt="cart" class="cartimg">
                <a href="cart.php?book_id=<?php echo $book['id']; ?>">Add to cart</a>
            </div>
            <div class="cartInfo">
                <div class="info">
                    <img src="./images/shopping-bag.svg" alt="shopping bag">
                    <p>Eenvoudig bestellen</p>
                </div>
                <div class="info">
                    <img src="./images/truck.svg" alt="truck">
                    <p>Gratis thuislevering vanaf € 30</p>
                </div>
                <div class="info">
                    <img src="./images/lock.svg" alt="lock">
                    <p>Veilig betalen</p>
                </div>
                <div class="info">
                    <img src="./images/home.svg" alt="home">
                    <p>Gratis levering in onze boekhandel in jouw buurt</p>
                </div>
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
                            <p class="author"><?php echo  $author['first_name'] . " " . $author['last_name']; ?></p>
                        </div>
                        <div class="uitgeverij flex">
                            <p>Uitgeverij: </p>
                            <p>Gallery Books</p>
                        </div>  
                    </div>
                    <h4>Eigenschappen</h4>
                    <div class="flex grey">
                        <p>ISBN: </p>
                        <p><?php echo $book['ISBN'];?></p>
                    </div>
                <div class="flex white">
                        <p>Verschijningsdatum: </p>
                        <p><?php echo $book['published_date'];?></p>
                </div>
                <div class="flex grey">
                        <p>Uitvoering: </p>
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
                    <a href="bookreview.php?book_id=<?php echo $book['id']; ?>">write a review</a>
                </div>
        </div>

        <div class="review-form">
            <h4>Write a review</h4>
            <p class="inf" ><span>*</span> Indicates a required field</p>
            <form action="bookreview.php" method="post">
                <div class="score">
                    <p><span>*</span> Score: </p>
                    <div class="stars">
                        <input type="radio" id="star5" name="score" value="5" required>
                        <label for="star5"></label>
                        <input type="radio" id="star4" name="score" value="4">
                        <label for="star4"></label>
                        <input type="radio" id="star3" name="score" value="3">
                        <label for="star3"></label>
                        <input type="radio" id="star2" name="score" value="2">
                        <label for="star2"></label>
                        <input type="radio" id="star1" name="score" value="1">
                        <label for="star1"></label>
                    </div>
                </div>
                <label for="Book"> Book: </label>
                <input readonly class="booktitle" placeholder="<?php echo $book['title']; ?>" name="booktitle">
                <label for="Title"><span>*</span> Title: </label>
                <input required type="text" name="title">
                <label for="review"><span>*</span> Comment:</label>
                <textarea required name="review"></textarea>
                <button class="post" type="submit">Post</button>
            </form>
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
                            <div class="product-author">
                                <?php
                                // Controleer of de voornaam en achternaam beschikbaar zijn
                                if (isset($book['first_name']) && isset($book['last_name'])) {
                                    echo $book['first_name'] . " " . $book['last_name'];
                                } else {
                                    echo "Author unknown"; // fallback als auteur niet gevonden wordt
                                }
                                ?>
                            </div>
                            <div class="product-price">€<?php echo number_format($book['price'], 2); ?></div>
                            <div class="product-add-to-cart"><a href="cart.php?book_id=<?php echo $book['id']; ?>"><img src="./images/shopping-cart2.svg" alt=""></a></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button class="scroll-btn right-btn"><img src="./images/rightarrow.svg" alt=""></button> 
    </div>
</section>

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

    <script src="./js/index.js"></script>

    <script>
        document.querySelectorAll('.product-item').forEach(function(item) {
            var stock = parseInt(item.querySelector('.stock p').textContent);
            var checkImg = item.querySelector('.check');
            if (stock === 0) {
                checkImg.src = "./images/no.png";
                item.querySelector('.stock p').style.color = 'red';
            } else {
                checkImg.src = "./images/yes.png";
            }
            if (stock < 5 && stock > 0) {
                item.querySelector('.stock p').innerHTML = 'only ' + stock + ' left!';
            }
        });

        document.getElementById('check').addEventListener('change', function() {
        var menuIcon = document.querySelector('.checkbtn img');
        if (this.checked) {
            menuIcon.src = './images/close.svg';
        } else {
            menuIcon.src = './images/menu.svg';
        }
        });
    </script>
   

</body>
</html>
