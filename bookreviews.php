<?php
session_start();
if($_SESSION['loggedin'] !== true){
    header('Location: login.php');
    exit();
}

include 'inc.tinynav.php';
include_once __DIR__ . '/classes/Review.php';
// $allComents = Review::getAll();

$conn = new mysqli('localhost', 'root', '', 'bookstore');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];
$userStatement = $conn->prepare('SELECT * FROM users WHERE email = ?');
$userStatement->bind_param('s', $email);
$userStatement->execute();
$userResult = $userStatement->get_result();
$user = $userResult->fetch_assoc(); // Verkrijg de gebruiker

$conn->close();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/bookreviews.css">
    <link rel="stylesheet" href="css/inc.footer.css">
</head>
<body>
    <div class="container">
    <div class="post_reviews">
        <div class="post_review_form">
            <input type="text" id="reviewText" placeholder="What's on your mind?">
            <a href="#" class="button" id="btnAddReview" data-postid="3">Add Review</a>
        </div>
    </div>

    <ul class="post_reviews_list">
        <!-- <?php foreach ($allComents as $c): ?>
            <li><?php echo $c['text']; ?></li>
        <?php endforeach; ?> -->
    </ul>
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
    <script scr="./review.js"></script>
</body>
</html>