<?php
session_start();
if($_SESSION['loggedin'] !== true){
    header('Location: login.php');
    exit();
}

include 'inc.nav.php';
include_once './classes/Db.php';
include './classes/user.php';
include './classes/Admin.php';
include './classes/Order.php';

// Maak databaseverbinding
$db = new Database();
$conn = $db->connect();

// Haal de huidige gebruiker op
$email = $_SESSION['email'];
$user = new User($conn, $email);
$userData = $user->getUserData();

// admin check
$admin = new Admin($conn);
$isAdmin = $admin->isAdmin($email);

$userId = $_SESSION['user_id'];

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
                $changed = true;
            } else {
                $doesntMatch = true;
            }
        } else {
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

            <?php if($isAdmin): ?>
            <div class="admin-panel">
                <a href="addproduct.php">+ Add Product</a>
            </div>
        <?php endif; ?>

        </div>
        
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
                <h3 class="ordertitle">Order History</h3>
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
                            <div class="book-details-flex">
                                <div class="title-flex">
                                    <h5><?php echo $book['title'];?></h5>
                                </div>

                                <p class="auteur"><?php echo $book['first_name'] . " " . $book['last_name']; ?></p>
                                <div class="type"><?php echo $book['Type']; ?> | <?php echo $book['subgenre']; ?></div>
                            </div>
                            <div class="basket-amount">

                                <div class="singleprice">
                                    <p class="price"><?php echo $book['quantity']?> x €<?php echo $book['price']; ?></p>
                                </div>

                                <h3 class="total">Total: €<?php echo number_format($total + $deliveryCost, 2); ?></h3>
                                <div class="bkcbks">
                                    <img src="./images/bookbuck.svg" alt="" class="bookbucks">
                                    <?php echo number_format($total + 4.95); ?>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-cart">
                        No orders found.
                    </div>
                <?php endif; ?>
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