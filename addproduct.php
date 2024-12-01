<?php
session_start();
if($_SESSION['loggedin'] !== true ){
    header('Location: login.php');
    exit();
}

include 'inc.nav.php';
include_once './classes/Db.php';
include './classes/user.php';
include './classes/Admin.php';

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

// Ophalen van auteurs
$authorsStatement = $conn->prepare('SELECT id, CONCAT(first_name, " ", last_name) AS full_name FROM authors');
$authorsStatement->execute();
$authorsResult = $authorsStatement->get_result();
$authors = $authorsResult->fetch_all(MYSQLI_ASSOC);


$email = $_SESSION['email'];
$userStatement = $conn->prepare('SELECT * FROM users WHERE email = ?');
$userStatement->bind_param('s', $email);
$userStatement->execute();
$userResult = $userStatement->get_result();
$user = $userResult->fetch_assoc(); // Verkrijg de gebruiker

// Genre selectie
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : 'all';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : 'all';


// Query boeken op basis van genre
if ($genreFilter === 'all' && $typeFilter === 'all') {
    $sql = "SELECT books.*, authors.first_name, authors.last_name 
            FROM books 
            LEFT JOIN authors ON books.author_id = authors.id
            ORDER BY RAND()";
} else {
    $sql = "SELECT books.*, authors.first_name, authors.last_name 
            FROM books 
            LEFT JOIN authors ON books.author_id = authors.id 
            WHERE 1=1";
        
    if ($genreFilter !== 'all') {
        $sql .= " AND category_id = (SELECT id FROM categories WHERE name = ?)";
    }
    if ($typeFilter !== 'all') {
        $sql .= " AND books.Type = ?";
    }
}

$stmt = $conn->prepare($sql);

if ($genreFilter !== 'all' && $typeFilter !== 'all') {
    $stmt->bind_param('ss', $genreFilter, $typeFilter);  // Bind het geselecteerde genre en type
} elseif ($genreFilter !== 'all') {
    $stmt->bind_param('s', $genreFilter);
} elseif ($typeFilter !== 'all') {
    $stmt->bind_param('s', $typeFilter);
}

$stmt->execute();
$result = $stmt->get_result();
$books = $result->fetch_all(MYSQLI_ASSOC);
// $authors = $result->fetch_all(MYSQLI_ASSOC);

// Initialize the error variable
$error = false;
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Valideer de invoer
        $title = htmlspecialchars($_POST['title']);
        $category_id = (int)$_POST['category_id'];
        $published_date = htmlspecialchars($_POST['published_date']);
        $ISBN = htmlspecialchars($_POST['ISBN']);
        $price = (float)str_replace(',', '.', $_POST['price']);
        $stock = (int)$_POST['stock'];
        $subgenre = htmlspecialchars($_POST['subgenre']);
        $type = ($_POST['type']); // from drop down menu
        $image_URL = htmlspecialchars($_POST['image_URL']);
        $description = htmlspecialchars($_POST['description']);
        $detailed_description = htmlspecialchars($_POST['detailed_description']);
        // $author_id = $_POST['author_id'];
        $first_name = htmlspecialchars($_POST['first_name']);
        $last_name = htmlspecialchars($_POST['last_name']);

        // Voeg de auteur toe aan de 'authors'-tabel
        $authorStmt = $conn->prepare("INSERT INTO authors (first_name, last_name) VALUES (?, ?)");
        $authorStmt->bind_param('ss', $first_name, $last_name);
        $authorStmt->execute();

        // Haal het ID van de nieuw toegevoegde auteur op
        $author_id = $conn->insert_id;

        // Opslaan in de database
        $product = new Product();
        $product->setTitle($title);
        $product->setCategory_id($category_id);
        $product->setPrice($price);
        $product->setStock($stock);
        $product->setISBN($ISBN);
        $product->setImage_URL($image_URL);
        $product->setSubgenre($subgenre);
        $product->setDescription($description);
        $product->setDetailed_description($detailed_description);
        $product->setPublished_date($published_date);
        $product->setType($type);

        $product->save($author_id);


        // $product->save();

        $success = true;
    } catch (Exception $e) {
        echo "Fout: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}
// Sluit de databaseverbinding
$conn->close();

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="./css/addproduct.css">
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

    <?php if($isAdmin === false): ?>
    <div class="falseadmin">
        <img src="./images/connectionlost.svg" alt="">
        <h1>OOPS!</h1>
        <h2>We couldn't find this page...</h2>
        <p>Need help? Please call us any 
             from 078 11 00 43</p>
    </div>
    <?php else: ?>
    <div class="trueAdmin">
    <h2>Add New Product</h2>

    <?php if($error): ?>
        <p class="error">Please fill in all fields.</p>
    <?php endif; ?>
    <?php if($success): ?>
        <p class="success">Product succesfully added!</p>
    <?php endif; ?>

    <form action="addproduct.php" method="POST">
        <div class="form-container">
            <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            </div>

            <div class="author-container">
            <p>Author</p>
            <div class="author">
                <div class="form-group">
                    <label for="first_name"></label>
                    <input placeholder="First name" type="text" id="first_name" name="first_name">
                </div>

                <div class="form-group">
                    <label for="last_name"></label>
                    <input placeholder="Last name" type="text" id="last_name" name="last_name">
                </div>
            </div>
            </div>

            <div class="form-group">
            <label for="category_id">Category ID:</label>
            <input type="number" id="category_id" name="category_id" required>
            </div>

            <div class="form-group">
            <label for="published_date">Published Date:</label>
            <input type="date" id="published_date" name="published_date" required>
            </div>

            <div class="form-group">
            <label for="ISBN">ISBN:</label>
            <input type="text" id="ISBN" name="ISBN" required>
            </div>

            <div class="form-group">
            <label for="price">Price:</label>
            <input type="text" id="price" name="price" step="0.01" required>
            </div>

            <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" required>
            </div>

            <div class="form-group">
            <label for="subgenre">Subgenre:</label>
            <input type="text" id="subgenre" name="subgenre" required>
            </div>

            <!-- drop down menu for type: paperback, hardcover or boxset -->
             <div class="form-group">
                <label for="type">Type:</label>
                <select name="type" id="type">
                    <option value="paperback">Paperback</option>
                    <option value="hardcover">Hardcover</option>
                    <option value="boxset">Boxset</option>
                </select>
             </div>

            <div class="form-group">
            <label for="image_URL">Image URL:</label>
            <input type="text" id="image_URL" name="image_URL" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea type="text" id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="detailed_description">Detailed Description:</label>
                <textarea type="text" id="detailed_description" name="detailed_description" required></textarea>
            </div>

            <button class="add" type="submit">Add Product</button>
        </div>
    </form>
    </div>
    <?php endif; ?>
</body>
</html>