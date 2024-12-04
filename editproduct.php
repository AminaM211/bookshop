<?php
session_start();
if ($_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

include_once './classes/db.php';
include 'inc.nav.php';
include_once './classes/Products.php';
// include './classes/user.php';
include './classes/Admin.php';

$db = new Database();
$conn = $db->connect();

// Haal de huidige gebruiker op
$email = $_SESSION['email'];
// $user = new User($conn, $email);
// $userData = $user->getUserData();

// admin check
$admin = new Admin($conn);
$isAdmin = $admin->isAdmin($email);

// Initialize product data
$product_id = $_GET['id'] ?? null;
$product = [
    'id' => '',
    'title' => '',
    'category_id' => '',
    'ISBN' => '',
    'price' => '',
    'stock' => '',
    'subgenre' => '',
    'image_URL' => '',
    'description' => '',
    'detailed_description' => '',
];

if ($product_id) {
    // Fetch product from database
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc() ?: $product;
}

// Update product
if (isset($_POST['update_product'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $category_id = $_POST['category_id'];
    $ISBN = $_POST['ISBN'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $subgenre = $_POST['subgenre'];
    $image_URL = $_POST['image_URL'];
    $description = $_POST['description'];
    $detailed_description = $_POST['detailed_description'];

    $stmt = $conn->prepare("UPDATE books SET 
        title = ?, category_id = ?, ISBN = ?, price = ?, stock = ?, 
        subgenre = ?, image_URL = ?, description = ?, detailed_description = ? 
        WHERE id = ?");
    $stmt->bind_param(
        "sisisssssi",
        $title, $category_id, $ISBN, $price, $stock,
        $subgenre, $image_URL, $description, $detailed_description, $id
    );

    if ($stmt->execute()) {
        echo "<script>window.location.href='details.php?id=$id';</script>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Delete product
if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='all.php';</script>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
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
<?php if ($isAdmin === false): ?>
    <div class="falseadmin">
        <img src="./images/connectionlost.svg" alt="">
        <h1>OOPS!</h1>
        <h2>We couldn't find this page...</h2>
        <p>Need help? Please call us at 078 11 00 43</p>
    </div>
<?php else: ?>

    <div id="cart-popup" class="popup">
        <div class="popup-content">
            <div class="title">
                <h5>Are you sure you want to delete this product?</h5>
            </div>
            <div class="buttons">
            <button class="go" onclick="confirmDelete()">OK</button>
            <button class="continue" onclick="cancelDelete()">Cancel</button>
            </div>
        </div>
    </div>

    <div class="trueAdmin">
        <h2>Edit Product</h2>
        <form action="editproduct.php?id=<?php echo $product['id']; ?>" method="POST">
            <div class="form-container">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
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
                <button type="submit" class="add" name="update_product">Save</button>
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            </div>
        </form>
       
        <form id="deleteForm" action="editproduct.php?id=<?php echo $product['id']; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="delete" value="1">
            <button type="button" onclick="DeleteProduct()" class="delete">Delete Product</button>
        </form>


    </div>
<?php endif; ?>

    <script>
        // Toon de pop-up
        function DeleteProduct() {
            document.getElementById('cart-popup').style.display = 'block';
        }

        // Indienen van het formulier bij bevestiging
        function confirmDelete() {
            document.getElementById('deleteForm').submit();
        }

        // Sluit de pop-up zonder actie te ondernemen
        function cancelDelete() {
            document.getElementById('cart-popup').style.display = 'none';
        }
    </script>

</body>
</html>
