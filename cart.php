<?php
session_start();
if ($_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

include 'inc.nav.php';

$conn = new mysqli('localhost', 'root', '', 'bookstore');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ophalen van auteurs
$authorsStatement = $conn->prepare('SELECT id, CONCAT(first_name, " ", last_name) AS full_name FROM authors');
$authorsStatement->execute();
$authorsResult = $authorsStatement->get_result();
$authors = $authorsResult->fetch_all(MYSQLI_ASSOC);

$userId = $_SESSION['user_id']; // Ensure user ID is in session
$sql = "SELECT books.*, cart.quantity, authors.first_name, authors.last_name FROM books
        INNER JOIN cart ON books.id = cart.book_id
        INNER JOIN authors ON books.author_id = authors.id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$cartBooks = $result->fetch_all(MYSQLI_ASSOC);

// Get user info
$email = $_SESSION['email'];
$userStatement = $conn->prepare('SELECT * FROM users WHERE email = ?');
$userStatement->bind_param('s', $email);
$userStatement->execute();
$userResult = $userStatement->get_result();
$user = $userResult->fetch_assoc();

// when adding another book to cart, check if the book is already in the cart, if so, increase quantity
// ...

// update quantity met - 1 + 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'];
    $action = $_POST['action'];

    if ($action === 'increase') {
        // Voeg 1 toe aan de hoeveelheid en verdubbel de prijs
        $stmt = $conn->prepare("UPDATE cart 
                                INNER JOIN books ON cart.book_id = books.id 
                                SET cart.quantity = cart.quantity + 1
                                WHERE cart.user_id = ? AND cart.book_id = ?");
    } elseif ($action === 'decrease') {
        // Verwijder 1 van de hoeveelheid, maar niet onder 1
        $stmt = $conn->prepare("UPDATE cart 
                                INNER JOIN books ON cart.book_id = books.id 
                                SET cart.quantity = GREATEST(cart.quantity - 1, 1)
                                WHERE cart.user_id = ? AND cart.book_id = ?");
    }

    // Update quantity
    $stmt->bind_param('ii', $userId, $bookId);
    $stmt->execute();

    // Als quantity 1 is en actie is decrease, verwijder het product uit de cart
    if ($action === 'decrease') {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND book_id = ? AND quantity = 1");
        $stmt->bind_param('ii', $userId, $bookId);
        $stmt->execute();
    }

    $stmt->bind_param('ii', $userId, $bookId);
    $stmt->execute();

    // Refresh de pagina om de wijzigingen te laten zien
    echo "<meta http-equiv='refresh' content='0'>";
    // exit();
}



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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
            <?php if (empty($cartBooks)): ?>
                <div class="empty-cart">
                    <img class="cartimg" src="./images/emptycart.png" alt="">
                    <h3>Your shopping cart is empty.</h3>
                    <a href="index.php" class="btn-continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <h1>My Shopping Cart</h1>
                <div class="row">
                    <div class="book-products">
                    <?php
                        $total = 0;
                        foreach ($cartBooks as $book):
                            $bookTotal = $book['price'] * $book['quantity'];
                            $total += $bookTotal;
                        ?>
                        <div class="column-book">
                            <div class="column-book-cover">
                                <a href="details.php?id=<?php echo $book['id']; ?>">
                                    <img src="<?php echo $book['image_URL']; ?>" alt="Book cover">
                                </a>
                            </div>
                            <div class="column-book-details">
                                <div class="title-flex">
                                    <h5><?php echo $book['title'];?></h5>
                                </div>

                                <p class="auteur"><?php echo $book['first_name'] . " " . $book['last_name']; ?></p>
                                <div class="type"><?php echo $book['Type']; ?> | <?php echo $book['subgenre']; ?></div>
                                <div class="basket-amount">

                                    <div class="quantity">
                                        <form method="POST" action="cart.php">
                                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                            <button type="submit" name="action" value="decrease">-</button>
                                        </form>
                                        <input type="text" value="<?php echo $book['quantity']; ?>" readonly class="quantity-input">
                                        <form method="POST" action="cart.php">
                                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                            <button type="submit" name="action" value="increase">+</button>
                                        </form>
                                    </div>

                                    <div class="singleprice">
                                        <p class="price">€<?php echo $book['price']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                    <div class="secondflex">
                        <div class="subtotal">
                            <p>Subtotal</p>
                            <p class="price">€<?php echo $total; ?></p>
                        </div>
                        <div class="Delivery">
                            <p>Delivery Cost</p>
                            <p>€4.95</p>
                        </div>
                        <div class="cart-total">
                            <p>Total</p>
                            <div class="cart-total-flex">
                                <p>incl. btw</p>
                                <p class="price">€<?php echo number_format($total + 4.95, 2); ?></p>
                            </div>
                        </div>
              
                        <div class="checkout">
                            <a href="checkout.php">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            <div class="goback">
                <a class="continue" href="index.php">
                    <img src="./images/backtotop.png" alt="">
                    Continue shopping
                </a>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <?php include 'inc.footer.php'; ?>
    
    <script src="./js/cart.js"></script>


</body>
</html>
