<?php
session_start();
if($_SESSION['loggedin'] !== true){
    header('Location: login.php');
    exit();
}

include 'inc.nav.php';
include 'cartpopup.php';


// $conn = new mysqli('localhost', 'root', '', 'bookstore');
$conn = new mysqli('junction.proxy.rlwy.net', 'root', 'JoTRKOPYmfOIxHylrywjlCkBrYGpOWvB', 'bookstore', 11795);


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

// alleen romance boeken
$sql = "SELECT books.*, authors.first_name, authors.last_name 
        FROM books 
        LEFT JOIN authors ON books.author_id = authors.id 
        WHERE category_id = (SELECT id FROM categories WHERE name = 'romance')
        LIMIT 6";

$stmt = $conn->prepare($sql);

if ($genreFilter !== 'all') {
    $stmt->bind_param('s', $genreFilter);  // Bind het geselecteerde genre
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
    <link rel="stylesheet" href="css/home.css">
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

    <div id="categories" class="filters">
            <a href="all.php?genre=all" class="filter-btn <?php echo ($genreFilter === 'all') ? 'active' : ''; ?>">All</a>
            <a href="all.php?genre=fiction" class="filter-btn <?php echo ($genreFilter === 'fiction') ? 'active' : ''; ?>">Fiction</a>
            <a href="all.php?genre=nonfiction" class="filter-btn <?php echo ($genreFilter === 'nonfiction') ? 'active' : ''; ?>">Non-Fiction</a>
            <a href="all.php?genre=romance" class="filter-btn <?php echo ($genreFilter === 'romance') ? 'active' : ''; ?>">Romance</a>
            <a href="all.php?genre=thriller" class="filter-btn <?php echo ($genreFilter === 'thriller') ? 'active' : ''; ?>">Thriller</a>
        </div>

    <section class="banner">
        <h1>Discover Your Next Great Read</h1>
        <p>Explore our curated selection of books across all genres</p>
        <a href="all.php?genre=all" class="cta-button">Explore</a>
    </section>

<section class="bestsellers">
    <div class="section-header">
        <h2>Bestsellers in Romance</h2>
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
                            <div class="price">€<?php echo number_format($book['price'], 2); ?></div>
    
                            <div class="add-to-cart"><a class="add" data-product-id="<?php echo $book['id']; ?>"><img src="./images/shopping-cart2.svg" alt=""></a></div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button class="scroll-btn right-btn"><img src="./images/rightarrow.svg" alt=""></button> 
    </div>
</section>

<div class="genres">
    <h2>Genres</h2>
    <div class="scrollcont">
    <div class="genre-list">
        <a href="all.php?genre=fiction" class="filter-btn <?php echo ($genreFilter === 'fiction') ? 'active' : ''; ?> list-fiction">
            <p>Fiction</p>
        </a>
        <a href="all.php?genre=nonfiction" class="filter-btn <?php echo ($genreFilter === 'nonfiction') ? 'active' : ''; ?> list-nonfiction">
            <p>Non-Fiction</p>
        </a>
        <a href="all.php?genre=romance" class="filter-btn <?php echo ($genreFilter === 'romance') ? 'active' : ''; ?> list-romance">
            <p>Romance</p>
        </a>
        <a href="all.php?genre=thriller" class="filter-btn <?php echo ($genreFilter === 'thriller') ? 'active' : ''; ?> list-thriller">
            <p>Thriller</p>
        </a>
    </div>
    </div>
</div>

    <section class="newsletter">
        <div class="newsletter-content">
            <h2>Subscribe to our Newsletter</h2>
            <p>Get the latest updates on new books and upcoming sales</p>
            <form action="subscribe.php" method="post">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>


    <?php include 'inc.footer.php'; ?>

    <script src="./js/index.js"></script>
    <script src="./js/cart.js"></script>
</body>
</html>
