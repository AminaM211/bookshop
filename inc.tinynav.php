<?php
if($_SESSION['loggedin'] !== true){
    header('Location: login.php');
    exit();
}

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
?>
<style>
    
#myBtn {
    display: none;
    position: fixed; /* Fixesd/sticky position */
    bottom: 200px; /* Place the button at the bottom of the page */
    right: 30px; /* Place the button 30px from the right */
    z-index: 99; /* Make sure it does not overlap */
    cursor: pointer; /* Add a mouse pointer on hover */
    border: none;
    background-color: none;
    text-decoration: none;
  }

#myBtn img {
    width: 80%;
}


/* _____________________Navigation_____________________ */

.navbar {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    background-color: #333;
    color: #F5E3D7;
}

.tinynav {
    text-align: left;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    color: #F5E3D7;
}

.tinynav img {
    height: 2.4em;
    display: block;
    margin: 0 auto 5px;
}

.tinynav a {
    padding: 10px 12px;
    text-align: center;
    font-size: 0.9em;
}

.hov:hover {
    background-color: #444;
    border-radius: 100%;
}

.hov1:hover {
    background-color: #444;
    border-radius: 100%;
}

.navbar__logout {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    border-radius: 10px;

}

.navbar__logout:hover{
    cursor: pointer;
    text-decoration: underline;

}

.navbar a {
    color: #F5E3D7;
    text-decoration: none;
    margin: 0 10px;
    transition: color 0.3s;
}

.navbar .logo img {
    height: 50px;
}

/* _____________________Search Bar_____________________ */

.searchBox {
    position: relative;
    display: flex;
    align-items: center;
    padding: 5px 15px;
    border-radius: 40px;
    transition: width 0.4s;
    width: 50vw;
    max-width: 500px;
}

.searchInput {
    flex: 1;
    border: none;
    outline: none;
    padding: 16px;
    padding-left: 23px;
    font-size: 1em;
    color: #333;
    border-radius: 40px;
    background-color: #fcf6f2;
    transition: width 0.4s;
}

.searchInput::placeholder {
    color: #b3b3b3;
}

.searchButton {
    position: absolute;
    right: 18px;
    background-color: #ff5722;
    border: none;
    border-radius: 50%;
    color: white;
    padding: 12px 12px;
    cursor: pointer;
}

.searchButton:hover {
    background-color: #ff8a50;
}

/* _______________________CATEGORIES______________________ */

#categories {
    display: flex;
    justify-content: center;
    gap: 20em;
    padding: 10px 10px;
    background-color: #FCF6F2;
    /* flex-wrap: nowrap; */
}

#categories a {
    color: #1d1d1d;
    text-decoration: none;
    font-size: 1em;
    font-weight: bold;
    padding: 5px 20px;
    border-radius: 5px;
    transition: background-color 0.1s, color 0.2s;
    text-wrap: nowrap;
}

#categories a:hover {
    color: #ff5722;
}

@media (max-width: 600px) {
    .navbar .logo img {
        margin-left: 20px;
        height: 50px;
        width: 40px;
        object-fit: cover;
        object-position: left;
    }

    .navbar .logo {
        position: relative;
    }

    .logoborder{
        border-width: 2px;
        border-style: solid;
        border-color: #FF5722;
        z-index: 999;
        position: absolute;
        width: 60px;
        height: 60px;
        left: 9px;
        top: -4px;
        border-radius: 100px;
    }
}

@media (max-width: 460px) {
    .logo {
        padding-right: 16px;
    }
}
</style>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <span class="logoborder"></span>
            <img src="./images/logo.png" alt="Logo" />
        </a>

        <div class="tinynav">
            <a class="hov user-icon" id="hidden" href="account.php"><img src="./images/user.svg" alt=""></a>
            <a class="hov cart-icon" id="hidden" href="cart.php"><img src="./images/shopping-cart.svg" alt=""></a>
            <a href="logout.php" id="hidden" class="navbar__logout" >Hi <?php echo ucfirst($user['name']); ?>,<br> logout?</a>
        </div>
    </nav>