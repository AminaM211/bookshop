<?php
class Order {
    private $conn;
    private $userId;

    public function __construct($conn, $userId) {
        $this->conn = $conn;
        $this->userId = $userId;
    }

    public function fetchLastOrder() {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function fetchOrderItems($orderId) {
        $sql = "
            SELECT 
                books.id, 
                books.title, 
                books.price, 
                books.image_URL, 
                books.subgenre, 
                books.Type, 
                order_items.quantity, 
                authors.first_name, 
                authors.last_name 
            FROM order_items
            INNER JOIN books ON order_items.book_id = books.id
            LEFT JOIN authors ON books.author_id = authors.id
            WHERE order_items.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function insertOrder($grandTotal) {
        $sql = "INSERT INTO orders (user_id, total, created_at) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('id', $this->userId, $grandTotal);
        $stmt->execute();
        return $this->conn->insert_id;
    }

    public function insertOrderItems($orderId, $orderItems) {
        foreach ($orderItems as $item) {
            $sql = "INSERT INTO order_items (order_id, book_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('iii', $orderId, $item['id'], $item['quantity']);
            $stmt->execute();
        }
    }

    public function clearCart() {
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $this->userId);
        $stmt->execute();
    }

    public function getOrdersByUserId($userId) {
        $stmt = $this->conn->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); // Haal alle resultaten op als associatieve array
    }

    
}
?>
