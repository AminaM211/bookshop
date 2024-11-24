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

// admin check
$email = $_SESSION['email'];
$isAdmin = false;
$adminStatement = $conn->prepare('SELECT * FROM users WHERE email = ?');
$adminStatement->bind_param('s', $email);
$adminStatement->execute();
$adminResult = $adminStatement->get_result();
$admin = $adminResult->fetch_assoc(); // Verkrijg de gebruiker
if($admin['is_admin'] === 1){
    $isAdmin = true;
}


$email = $_SESSION['email'];
$userStatement = $conn->prepare('SELECT * FROM users WHERE email = ?');
$userStatement->bind_param('s', $email);
$userStatement->execute();
$userResult = $userStatement->get_result();
$user = $userResult->fetch_assoc(); // Verkrijg de gebruiker

// Genre selectie
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : 'all';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : 'all';


// Query boeken op basis van genre
if ($genreFilter === 'all' && $typeFilter === 'all') {
    $sql = "SELECT books.*, authors.first_name, authors.last_name 
            FROM books 
            LEFT JOIN authors ON books.author_id = authors.id
            ORDER BY RAND()";
} else {
    $sql = "SELECT books.*, authors.first_name, authors.last_name 
            FROM books 
            LEFT JOIN authors ON books.author_id = authors.id 
            WHERE 1=1";
        
    if ($genreFilter !== 'all') {
        $sql .= " AND category_id = (SELECT id FROM categories WHERE name = ?)";
    }
    if ($typeFilter !== 'all') {
        $sql .= " AND books.Type = ?";
    }
}

$stmt = $conn->prepare($sql);

if ($genreFilter !== 'all' && $typeFilter !== 'all') {
    $stmt->bind_param('ss', $genreFilter, $typeFilter);  // Bind het geselecteerde genre en type
} elseif ($genreFilter !== 'all') {
    $stmt->bind_param('s', $genreFilter);
} elseif ($typeFilter !== 'all') {
    $stmt->bind_param('s', $typeFilter);
}


$stmt->execute();
$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);
// $authors = $result->fetch_all(MYSQLI_ASSOC);


// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Online bookstore</title>
    <link rel="stylesheet" href="./css/all.css">
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

    <div class="product-container">
    <div class="section-header">
        <h2><?php echo ucfirst($genreFilter); ?> Books</h2>
    </div>


    <div class="filters">
        <form method="GET" action="all.php">
            <!-- <label class="filter-title" for="type"></label> -->
            <select name="type" id="type">
                <option value="all" <?php echo ($typeFilter === 'all') ? 'selected' : ''; ?>>All</option>
                <option value="hardcover" <?php echo ($typeFilter === 'hardcover') ? 'selected' : ''; ?>>Hardcover</option>
                <option value="paperback" <?php echo ($typeFilter === 'paperback') ? 'selected' : ''; ?>>Paperback</option>
                <option value="box set" <?php echo ($typeFilter === 'boxset') ? 'selected' : ''; ?>>Boxset</option>
            </select>
        </form>
    </div>
    </div>

    <?php if($isAdmin): ?>
        <div class="admin-panel">
            <a href="addproduct.php">+ Add Product</a>
        </div>
    <?php endif; ?>

    <section class="bestsellers">
    <div class="products">
        <?php if (!empty($books)): ?>
            <?php foreach($books as $book): ?>
                <div class="product-item">
                    <a href="details.php?id=<?php echo $book['id']?>"><img src="<?php echo $book['image_URL']; ?>" alt="Book cover"></a>
                    <div class="product-info">
                        <div class="firstflex">
                        <a class="product-title" href="details.php?id=<?php echo $book['id']?>"><h3><?php echo $book['title']; ?></h3></a>
                            <div class="author">
                                <?php
                                // Controleer of de voornaam en achternaam beschikbaar zijn
                                if (isset($book['first_name']) && isset($book['last_name'])) {
                                    echo "by " . $book['first_name'] . " " . $book['last_name'];
                                } else {
                                    echo "Author unknown"; // fallback als auteur niet gevonden wordt
                                }
                                ?>
                            </div>
                            <div class="type"><?php echo $book['Type']; ?> | <?php echo $book['subgenre']; ?></div>
                            <div class="description text">
                                <?php echo $book['description']; ?>
                                <a href="#" class="leesmeer">Lees meer</a>
                            </div>
                        </div>
                        <div class="secondflex">
                            <div class="price">€<?php echo number_format($book['price'], 2); ?></div>
                            <div class="stars">★★★★★</div> 
                            <div class="stock">
                                <img class="check" src="./images/yes.png" alt=""> 
                                <p><?php echo $book['stock']; ?> left</p>
                            </div>
                            <div class="add-to-cart"><a href="cart.php?book_id=<?php echo $book['id']; ?>">Add to cart</a></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No books found.</p>
        <?php endif; ?>
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
