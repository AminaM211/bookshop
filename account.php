<?php
session_start();
if($_SESSION['loggedin'] !== true){
    header('Location: login.php');
    exit();
}

include 'inc.tinynav.php';

$conn = new mysqli('junction.proxy.rlwy.net', 'root', 'JoTRKOPYmfOIxHylrywjlCkBrYGpOWvB', 'railway', 11795);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];


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

$userId = $_SESSION['user_id'];


// Fetch the last order placed by the user
$orderSql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($orderSql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();

// Fetch the order items
$orderItemsSql = "
    SELECT 
        books.id, 
        books.title, 
        books.price, 
        books.image_URL, 
        books.subgenre, 
        books.Type, 
        order_items.quantity, 
        authors.first_name, 
        authors.last_name 
    FROM order_items
    INNER JOIN books ON order_items.book_id = books.id
    LEFT JOIN authors ON books.author_id = authors.id
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


$incorrect = false;
$changed = false;
$doesntMatch = false;

if	($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password']))
    {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $email = $_SESSION['email'];
        $userStatement = $conn->prepare('SELECT * FROM users WHERE email = ?');
        $userStatement->bind_param('s', $email);
        $userStatement->execute();
        $userResult = $userStatement->get_result();
        $user = $userResult->fetch_assoc(); // Verkrijg de gebruiker
        if(password_verify($current_password, $user['password'])){
            if($new_password === $confirm_password){
                $new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $updateStatement = $conn->prepare('UPDATE users SET password = ? WHERE email = ?');
                $updateStatement->bind_param('ss', $new_password, $email);
                $updateStatement->execute();
                $updateStatement->close();
                // echo 'Password changed!';
                $changed = true;
            } else {
                // echo 'New password and confirm password do not match!';
                $doesntMatch = true;
            }
        } else {
            // echo 'Current password is incorrect!';
            $incorrect = true;
        }
    }

    $orderStatement = $conn->prepare('SELECT * FROM orders WHERE user_id = ?');
    $orderStatement->bind_param('s', $email);
    $orderStatement->execute();
    $orderResult = $orderStatement->get_result();



$email = $_SESSION['email'];
$userStatement = $conn->prepare('SELECT * FROM users WHERE email = ?');
$userStatement->bind_param('s', $email);
$userStatement->execute();
$userResult = $userStatement->get_result();
$user = $userResult->fetch_assoc(); 

$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Page</title>
    <link rel="stylesheet" href="./css/account.css">
    <link rel="stylesheet" href="./css/inc.footer.css">
</head>
<body>
    <div class="container">
    <div class="account-info">
        <h2>Account Information</h2>
        <div class="account-details">
            <p><strong>Name: </strong><?php echo ucfirst($user['name']); ?></p>
            <p><strong>Email: </strong> <?php echo ucfirst($user['email']); ?></p>
            <div class="coins">
                <p><strong>BookBucks: </strong><?php echo ucfirst($user['book_bucks']); ?></p>
                <img src="./images/bookbuck.svg" alt="">
            </div>
            <P class='usersince'>User since: <?php echo $user['created_at']; ?></P>
        </div>
    </div>
    
    <?php if($isAdmin): ?>
        <div class="admin-panel">
            <a href="addproduct.php">+ Add Product</a>
        </div>
    <?php endif; ?>

        <div class="change-password">
            <h2>Change Password</h2>
            <?php if($changed): ?>
                <p class="message success">Your password was changed successfully!</p>
            <?php endif; ?>
            <?php if($doesntMatch): ?>
                <p class="message error">New password and confirmed password do not match!</p>
            <?php endif; ?>
            <?php if($incorrect): ?>
                <p class="message error">Current password is incorrect!</p>
            <?php endif; ?>
            <form action="account.php" method="post">
                <label for="current-password">Current Password:</label>
                <input type="password" id="current-password" name="current_password" required>
                <br>
                <label for="new-password">New Password:</label>
                <input type="password" id="new-password" name="new_password" required>
                <br>
                <label for="confirm-password">Confirm New Password:</label>
                <input type="password" id="confirm-password" name="confirm_password" required>
                <br>
                <button type="submit">Change Password</button>
            </form>
        </div>

        <div class="order-history">
            <h2>Order History</h2>
            <div class="products">
        <?php if (!empty($order_items)): ?>
            <?php foreach($order_items as $book): ?>
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
</body>
</html>