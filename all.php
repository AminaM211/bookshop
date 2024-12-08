<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

include 'cartpopup.php';
include_once './classes/Db.php';
include './classes/Admin.php';
include './classes/Book.php';

// Maak databaseverbinding
$db = new Database();
$conn = $db->connect();

// Haal de huidige gebruiker op
$email = $_SESSION['email'];

// admin check
$admin = new Admin($conn);
$isAdmin = $admin->isAdmin($email);

// // Genre selectie
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : 'all';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : 'all';

// books ophalen
$bookObj = new Book($conn);
$books = $bookObj->getBooks($genreFilter, $typeFilter);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <link rel="stylesheet" href="./css/all.css">
</head>
<body>
    <?php include 'inc.nav.php'; ?>
    
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
                            <div class="add-to-cart"><a class="add" data-product-id="<?php echo $book['id']; ?>">Add to cart</a></div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No books found.</p>
        <?php endif; ?>
    </div>
</section>

    <?php include 'inc.footer.php'; ?>

    <script src="./js/index.js"></script>
    <script src="./js/cart.js"></script>
    
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
