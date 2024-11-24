<?php
session_start();

// Zorg ervoor dat je de juiste databaseverbinding hebt
$conn = new mysqli('localhost', 'root', '', 'bookstore');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = intval($_POST['id']);
    
    // Controleer of het boek al in de winkelwagen staat
    $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE book_id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        // Voeg het boek toe aan de winkelwagen
        $stmt = $conn->prepare("INSERT INTO cart (book_id) VALUES (?)");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $stmt->close();
        echo "Success";
    } else {
        echo "Already in cart";
    }
}
?>
