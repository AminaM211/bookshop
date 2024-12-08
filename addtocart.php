<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$book_id = $data['book_id'] ?? null;

if (!$book_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid book ID.']);
    exit;
}

$conn = new mysqli('junction.proxy.rlwy.net', 'root', 'JoTRKOPYmfOIxHylrywjlCkBrYGpOWvB', 'railway', 11795);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit;
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT quantity FROM cart WHERE user_id = ? AND book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Boek zit al in de cart, update de quantity
    $row = $result->fetch_assoc();
    $newQuantity = $row['quantity'] + 1;
    $updateSql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND book_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("iii", $newQuantity, $user_id, $book_id);

    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Quantity updated.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update quantity.']);
    }
    $updateStmt->close();
} else {
    $insertSql = "INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, 1)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ii", $user_id, $book_id);

    if ($insertStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Book added to cart.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add to cart.']);
    }
    $insertStmt->close();
}


$stmt->close();
$conn->close();

?>
