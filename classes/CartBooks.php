<?php
class Cartbooks {
    private $conn;
    private $userId;

    public function __construct($conn, $userId) {
        $this->conn = $conn;
        $this->userId = $userId;
    }

    public function getCartBooks() {
        $sql = "SELECT books.*, cart.quantity, authors.first_name, authors.last_name 
                FROM books
                INNER JOIN cart ON books.id = cart.book_id
                INNER JOIN authors ON books.author_id = authors.id
                WHERE cart.user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateQuantity($bookId, $action) {
        if ($action === 'increase') {
            $stmt = $this->conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND book_id = ?");
        } elseif ($action === 'decrease') {
            $stmt = $this->conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND book_id = ?");
        }
        $stmt->bind_param('ii', $this->userId, $bookId);
        $stmt->execute();

        // Remove item if quantity is 0
        if ($action === 'decrease') {
            $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ? AND book_id = ? AND quantity = 0");
            $stmt->bind_param('ii', $this->userId, $bookId);
            $stmt->execute();
        }
    }
}
?>
