<?php
session_start();
if ($_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

include_once 'inc.nav.php';
include_once './classes/Db.php';
include_once './classes/user.php';
include './classes/Cartbooks.php';

// Create a database connection
$db = new Database();
$conn = $db->connect();

// Get the current user
$email = $_SESSION['email'];
$user = new User($conn, $email);
$userData = $user->getUserData();

$userId = $_SESSION['user_id']; 
$cart = new Cartbooks($conn, $userId); 

// Haal book_bucks op voor de ingelogde gebruiker
$bookBucks = $user->getBookBucks();

// Handle POST requests for quantity updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'];
    $action = $_POST['action'];
    $cart->updateQuantity($bookId, $action); // Use the Cart class method
    echo "<meta http-equiv='refresh' content='0'>"; // Refresh to update the cart
    exit();
}

// Get the user's cart items
$cartBooks = $cart->getCartBooks();
$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <link rel="stylesheet" href="./css/cart.css">
    <link rel="stylesheet" href="./css/inc.footer.css">
</head>
<body>
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
                                            <button class="decrease" type="submit" name="action" value="decrease">-</button>
                                        </form>
                                        <input type="text" value="<?php echo $book['quantity']; ?>" readonly class="quantity-input">
                                        <form method="POST" action="cart.php">
                                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                            <button class="increase" type="submit" name="action" value="increase">+</button>
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
                            <div class="cart-total-price-options">
                                <?php if ($bookBucks > ($total + 4.95)): ?>
                                    <p class="price" id="line">incl. btw &nbsp;&nbsp; €<?php echo number_format($total + 4.95, 2); ?></p>
                                    <p class="bkcbks">
                                        <img src="./images/bookbuck.svg" alt="" class="bookbucks">
                                        <?php echo number_format($total + 4.95); ?>
                                    </p>
                                <?php else: ?>
                                    <p class="totalwithoutbb">incl. btw &nbsp;&nbsp; €<?php echo number_format($total + 4.95, 2); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
              
                        <div class="checkout">
                            <a href="checkout.php">Proceed to Checkout</a>
                        </div>
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
