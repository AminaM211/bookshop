<?php
session_start();
if ($_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

include_once './classes/Db.php';
include './classes/user.php';
include './classes/Book.php';

// Create a database connection
$db = new Database();
$conn = $db->connect();

$userId = $_SESSION['user_id']; // Ensure user ID is in session

$book = new Book($conn);
$cartBooks = $book->cartBooks($userId);


// Calculate total price
$total = 0;
foreach ($cartBooks as $item) {
    $total += $item['price'] * $item['quantity'];
}
$deliveryCost = 4.95;
$grandTotal = $total + $deliveryCost;

// Checkout form submission logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $address = htmlspecialchars(trim($_POST['address']));
    $city = htmlspecialchars(trim($_POST['city']));
    $postalCode = htmlspecialchars(floatval($_POST['postal_code']));
    $phone = htmlspecialchars(trim($_POST['phone']));

    // Validate postal code (minimum 4 numbers)
    if ($postalCode < 4) {
        die("Invalid postal code.");
    }

    // Validate phone number (must be a number)
    if (!is_numeric($phone)) {
        die("Invalid phone number.");
    }

    // Save the order details in the database
    $orderSql = "INSERT INTO orders (user_id, name, address, city, postal_code, phone, total) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $orderStmt = $conn->prepare($orderSql);
    $orderStmt->bind_param('isssssd', $userId, $name, $address, $city, $postalCode, $phone, $grandTotal);
    $orderStmt->execute();

    $orderId = $conn->insert_id; // Get the last inserted order ID
    $orderItemsSql = "INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)";
    $orderItemsStmt = $conn->prepare($orderItemsSql);
    foreach ($cartBooks as $item) {
        $bookId = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $orderItemsStmt->bind_param('iiid', $orderId, $bookId, $quantity, $price);
        $orderItemsStmt->execute();
    }

    header('Location: confirmation.php');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <link rel="stylesheet" href="./css/checkout.css">
</head>
<body>
    <?php include 'inc.tinynav.php'; ?>
    <div class="container">
    <h1>Checkout</h1>
    <div class="checkout-container">
        <div class="checkout-container-flex-one">
        <form method="POST" class="checkout-form">
            <h2>Shipping Details</h2>
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>

            <div class="checkout-flex">
                <div class="flex-one">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" required>
                </div>
                <div class="flex-two">
                    <label for="postal_code">Postal Code:</label>
                    <input type="text" id="postal_code" name="postal_code" required>
                </div>
            </div>

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" required>
            </div>
            <div class="checkout-container-flex-two">
            <div class="book-products">
                <h2>My orders</h2>
                    <?php
                        $total = 0;
                        foreach ($cartBooks as $book):
                            $bookTotal = $book['price'] * $book['quantity'];
                            $total += $bookTotal;
                        ?>
                        <div class="column-book">
                            <div class="column-book-cover">
                                    <img src="<?php echo $book['image_URL']; ?>" alt="Book cover">
                            </div>
                            <div class="column-book-details">
                                <div class="title-flex">
                                    <h5><?php echo $book['title'];?></h5>
                                </div>

                                <p class="auteur"><?php echo $book['first_name'] . " " . $book['last_name']; ?></p>
                                <div class="type"><?php echo $book['Type']; ?> | <?php echo $book['subgenre']; ?></div>
                                <div class="basket-amount">

                                <div class="singleprice">
                                    <p class="price"><?php echo $book['quantity']?> x €<?php echo $book['price']; ?></p>
                                </div>
                                <div class="bookprice-total">
                                    €<?php echo number_format($book['price'] * $book['quantity'], 2); ?>
                                </div>
                                
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>

        <div class="checkout-summary">
            <p>Subtotal: €<?php echo number_format($total, 2); ?></p>
            <p>Delivery: €<?php echo number_format($deliveryCost, 2); ?></p>
            <h3>Total: €<?php echo number_format($total + $deliveryCost, 2); ?></h3>
            <h3 class="bkcbks"><img src="./images/bookbuck.svg" alt="" class="bookbucks"><?php echo number_format($total + 4.95); ?></h3>
        </div>

        <button type="submit" class="btn-checkout">Place Order</button>    
        <a href="cart.php" class="btn-back">Go Back to Cart</a>
        </form>
            </div>
    </div>
    </div>
</body>
</html>