<?php
session_start();
if($_SESSION['loggedin'] !== true ){
    header('Location: login.php');
    exit();
}

include_once 'classes/Products.php';
include 'inc.nav.php';

$conn = new mysqli('localhost', 'root', '', 'bookstore');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
} else {
    $isAdmin = false;
}

// Ophalen van auteurs
$authorsStatement = $conn->prepare('SELECT id, CONCAT(first_name, " ", last_name) AS full_name FROM authors');
$authorsStatement->execute();
$authorsResult = $authorsStatement->get_result();
$authors = $authorsResult->fetch_all(MYSQLI_ASSOC);

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

// Initialize de error en success variabelen
$error = false;
$success = false;

$product_id = $_GET['id'] ?? null; // Haal de ID uit de URL
$product = [
    'id' => '',
    'title' => '',
    'author_id' => '',
    'category_id' => '',
    'published_date' => '',
    'ISBN' => '',
    'price' => '',
    'stock' => '',
    'subgenre' => '',
    'Type' => '',
    'image_URL' => '',
    'description' => '',
    'detailed_description' => '',
    'first_name' => '',
    'last_name' => '',
];

if ($product_id) {
    // Haal productdata op uit de database
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc() ?: $product;
}

if (isset($_POST['update_product'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $author_id = $_POST['author_id']; 
    $category_id = $_POST['category_id'];
    $published_date = $_POST['published_date'];
    $ISBN = $_POST['ISBN'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $subgenre = $_POST['subgenre'];
    $Type = $_POST['Type'];
    $image_URL = $_POST['image_URL'];
    $description = $_POST['description'];
    $detailed_description = $_POST['detailed_description'];
    
    $stmt = $conn->prepare("UPDATE books SET 
    title = ?, author_id = ?, category_id = ?, published_date = ?, ISBN = ?, 
    price = ?, stock = ?, subgenre = ?, Type = ?, image_URL = ?, description = ?, 
    detailed_description = ? WHERE id = ?");
    $stmt->bind_param("siiissdsssssi", $title, $author_id, $category_id, $published_date, $ISBN, 
    $price, $stock, $subgenre, $Type, $image_URL, $description, $detailed_description, $id);


    if ($stmt->execute()) {
        // Redirect naar product detail pagina na update
        header("Location: details.php?id=$id");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();

// Sluit de databaseverbinding
// $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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
    <h1>OOPS!</h1>
    <h2>We couldn't find this page...</h2>
    <p>Need help? Please call us at 078 11 00 43</p>
</div>
<?php else: ?>
<div class="trueAdmin">
    <h2>Edit Product</h2>

    <?php if($error): ?>
    <p class="error">Please fill in all fields.</p>
    <?php endif; ?>
    <?php if($success): ?>
    <p class="success">Product successfully updated!</p>
    <?php endif; ?>

    <form action="editproduct.php" method="POST">
        <div class="form-container">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
            </div>

            <div class="author-container">
                <p>Author</p>
                <div class="author">
                    <div class="form-group">
                        <label for="first_name"></label>
                        <input placeholder="First name" type="text" id="first_name" name="first_name" value="<?php echo isset($product['first_name']) ? htmlspecialchars($product['first_name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="last_name"></label>
                        <input placeholder="Last name" type="text" id="last_name" name="last_name" value="<?php echo isset($product['last_name']) ? htmlspecialchars($product['last_name']) : ''; ?>">
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" required>
                    <option value="1" <?php echo $product['category_id'] == 1 ? 'selected' : ''; ?>>Fiction</option>
                    <option value="2" <?php echo $product['category_id'] == 2 ? 'selected' : ''; ?>>Non-Fiction</option>
                    <option value="3" <?php echo $product['category_id'] == 3 ? 'selected' : ''; ?>>Romance</option>
                    <option value="4" <?php echo $product['category_id'] == 4 ? 'selected' : ''; ?>>Thriller</option>
                </select>
            </div>

            <div class="form-group">
                <label for="published_date">Published Date</label>
                <input type="date" id="published_date" name="published_date" value="<?php echo htmlspecialchars($product['published_date']); ?>" required>
            </div>

            <div class="form-group">
                <label for="ISBN">ISBN</label>
                <input type="text" id="ISBN" name="ISBN" value="<?php echo htmlspecialchars($product['ISBN']); ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>

            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
            </div>

            <div class="form-group">
                <label for="subgenre">Subgenre</label>
                <input type="text" id="subgenre" name="subgenre" value="<?php echo htmlspecialchars($product['subgenre']); ?>">
            </div>

            <div class="form-group">
                <label for="Type">Type</label>
                <select name="Type" id="Type">
                    <option value="book" <?php echo $product['Type'] == 'book' ? 'selected' : ''; ?>>Book</option>
                    <option value="ebook" <?php echo $product['Type'] == 'ebook' ? 'selected' : ''; ?>>Ebook</option>
                </select>
            </div>

            <div class="form-group">
                <label for="image_URL">Image URL</label>
                <input type="text" id="image_URL" name="image_URL" value="<?php echo htmlspecialchars($product['image_URL']); ?>">
            </div>

            <div class="form-group">
                <label for="description">Short Description</label>
                <textarea name="description" id="description" cols="30" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="detailed_description">Detailed Description</label>
                <textarea name="detailed_description" id="detailed_description" cols="30" rows="6"><?php echo htmlspecialchars($product['detailed_description']); ?></textarea>
            </div>

            <button type="submit" class="add" name="update_product">Update Product</button>
        </div>
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <!-- <button type="submit" class="delete" name="delete">Delete Product</button> -->
    </form>


</div>
<?php endif; ?>

</body>
</html>
