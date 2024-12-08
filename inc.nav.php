<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

include_once './classes/Db.php';
include_once './classes/user.php';

// Maak databaseverbinding
$db = new Database();
$conn = $db->connect();

// Haal de huidige gebruiker op
$email = $_SESSION['email'];
$user = new User($conn, $email);
$userData = $user->getUserData();


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>nav</title>
    <link rel="stylesheet" href="./css/normalize.css">
    <link rel="stylesheet" href="./css/inc.nav.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php" class="logo">
            <span class="logoborder"></span>
            <img src="./images/logo.png" alt="Logo" />
        </a>

        <div class="searchBox">
                <input class="searchInput"type="text" name="" placeholder="What are you looking for?">
                <button class="searchButton" type="submit">
                    <img src="./images/search.svg" alt="searchicon">
                </button>
        </div>
        <div class="tinynav">
            <a class="hov user-icon" id="hidden" href="account.php"><img src="./images/user.svg" alt=""></a>
            <a class="hov cart-icon" id="hidden" href="cart.php"><img src="./images/shopping-cart.svg" alt=""></a>
            <a href="logout.php" id="hidden" class="navbar__logout" >Hi <?php echo ucfirst($userData['name']); ?>,<br> logout?</a>
        </div>
    </div>

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

    <script>
        document.getElementById('check').addEventListener('change', function() {
            var menuIcon = document.querySelector('.checkbtn img');
            if (this.checked) {
                menuIcon.src = './images/hovermenu.svg';
            } else {
                menuIcon.src = './images/menu.svg';
            }
        });
    </script>
</body>
</html>