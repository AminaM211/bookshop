<?php
session_start();

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit;
}

include_once './classes/db.php';

// Create a database connection
$db = new Database();
$conn = $db->connect();


// Haal het product-ID en de gebruikers-ID op
$product_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$user_id = $_SESSION['user_id']; // Zorg ervoor dat je user_id opslaat bij login


if ($product_id && $user_id && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $review_text = $_POST['review'] ?? '';

    if ($review_text) {
        $stmt = $conn->prepare("INSERT INTO review (text, products_id, users_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $review_text, $product_id, $user_id);

        if ($stmt->execute()) {
            echo ('good');
        } else {
            // Foutmelding bij het uitvoeren van de query
            echo "Er is een fout opgetreden bij het toevoegen van de review: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Je moet een review schrijven.";
    }
} else {
    var_dump($product_id, $user_id);
    echo "Er is een probleem met de opgegeven gegevens.";
}

$conn->close();
?>
