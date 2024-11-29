<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

include 'inc.tinynav.php';

$conn = new mysqli('junction.proxy.rlwy.net', 'root', 'JoTRKOPYmfOIxHylrywjlCkBrYGpOWvB', 'railway', 11795);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];

// Fetch the last order placed by the user
$orderSql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($orderSql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();

// Fetch the order items
$orderItemsSql = "SELECT books.id, books.title, books.price, books.image_URL, order_items.quantity 
                  FROM order_items
                  INNER JOIN books ON order_items.book_id = books.id
                  WHERE order_items.order_id = ?";
$orderItemsStmt = $conn->prepare($orderItemsSql);
$orderItemsStmt->bind_param('i', $order['id']);
$orderItemsStmt->execute();
$orderItemsResult = $orderItemsStmt->get_result();
$orderItems = $orderItemsResult->num_rows > 0 ? $orderItemsResult->fetch_all(MYSQLI_ASSOC) : [];

// Calculate total price
$total = 0;
foreach ($orderItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
$deliveryCost = 4.95;
$grandTotal = $total + $deliveryCost;

// Step 1: Insert order into the orders table (if not already inserted)
if (!$order) {
    $insertOrderSql = "INSERT INTO orders (user_id, total, created_at) VALUES (?, ?, NOW())";
    $insertOrderStmt = $conn->prepare($insertOrderSql);
    $insertOrderStmt->bind_param('id', $userId, $grandTotal);
    $insertOrderStmt->execute();
    $orderId = $conn->insert_id; // Get the last inserted order ID

    // Step 2: Insert order items into the order_items table
    foreach ($orderItems as $item) {
        $insertOrderItemSql = "INSERT INTO order_items (order_id, book_id, quantity) VALUES (?, ?, ?)";
        $insertOrderItemStmt = $conn->prepare($insertOrderItemSql);
        $insertOrderItemStmt->bind_param('iii', $orderId, $item['id'], $item['quantity']);
        $insertOrderItemStmt->execute();
    }
}

// Step 3: Clear the cart after the order is successfully placed
$clearCartSql = "DELETE FROM cart WHERE user_id = ?";
$clearCartStmt = $conn->prepare($clearCartSql);
$clearCartStmt->bind_param('i', $userId);
$clearCartStmt->execute();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="./css/confirmation.css">
</head>
<body>
    <div class="confirmation-container">
        <h1>Order Confirmation</h1>

        <h2>Thank you for your order, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Customer'; ?>!</h2>
        <p class="success">Your order has been successfully placed.</p>

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
                            <p class="price">€<?php echo $book['price']; ?></p>
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

        <h3>Total</h3>
        <p>Subtotal: €<?php echo number_format($total, 2); ?></p>
        <p>Delivery: €<?php echo number_format($deliveryCost, 2); ?></p>
        <h3>Total: €<?php echo number_format($grandTotal, 2); ?></h3>

        <div class="goback">
                <a class="continue" href="index.php">
                    <img src="./images/backtotop.png" alt="">
                    Continue shopping
                </a>
        </div>
    </div>
</body>
</html>
