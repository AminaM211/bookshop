<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

include 'inc.tinynav.php';
include_once './classes/db.php';
include './classes/user.php';
include './classes/Order.php';

// Maak databaseverbinding
$db = new Database();
$conn = $db->connect();

// Haal de huidige gebruiker op
$userId = $_SESSION['user_id'];
$email = $_SESSION['email'];
$user = new User($conn, $email);
$userData = $user->getUserData();

// Maak een Order-object aan
$neworder = new Order($conn, $userId);

// Haal de laatste bestelling op
$order = $neworder->fetchLastOrder();

if ($order) {
    $orderItems = $neworder->fetchOrderItems($order['id']);
    $deliveryCost = 4.95;
} else {
    $total = 0;
    foreach ($orderItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    $deliveryCost = 4.95;
    $grandTotal = $total + $deliveryCost;

    $orderId = $neworder->insertOrder($grandTotal);
    $neworder->insertOrderItems($orderId, $orderItems);
}

$neworder->clearCart();

$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <link rel="stylesheet" href="./css/confirmation.css">
</head>
<body>
    <div class="confirmation-container">
        <h1>Order Confirmation</h1>

        <h2>Thank you for your order, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Customer'; ?>!</h2>
        <p class="success">Your order has been successfully placed.</p>

        <div class="flex-container">
            <div class="shippingdetails">
                <h3>Shipping Details</h3>
                <div class="shippingdetails-info">
                    <p><strong>Name:</strong> <?php echo $order['name']; ?></p>
                    <p><strong>Address:</strong> <?php echo $order['address']; ?></p>
                    <p><strong>City:</strong> <?php echo $order['city']; ?></p>
                    <p><strong>Postal Code:</strong> <?php echo $order['postal_code']; ?></p>
                    <p><strong>Phone Number:</strong> <?php echo $order['phone']; ?></p>
                </div>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php if (!empty($orderItems)): ?>
                    <?php
                        $total = 0;
                        foreach ($orderItems as $book):
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

                                <div class="singleprice">
                                    <p class="price"><?php echo $book['quantity']?> x €<?php echo $book['price']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-cart">
                        error
                    </div>
                <?php endif; ?>

                <div class="checkout-summary">
                    <p>Subtotal: €<?php echo number_format($total, 2); ?></p>
                    <p>Delivery: €<?php echo number_format($deliveryCost, 2); ?></p>
                    <h3>Total: €<?php echo number_format($total + $deliveryCost, 2); ?></h3>
                    <h3 class="bkcbks"><img src="./images/bookbuck.svg" alt="" class="bookbucks"><?php echo number_format($total + 4.95); ?></h3>
                </div>
            </div>
        </div>

        <div class="goback">
            <a class="continue" href="index.php">
                <img src="./images/backtotop.png" alt="">
                Continue shopping
            </a>
        </div>
    </div>
</body>
</html>
