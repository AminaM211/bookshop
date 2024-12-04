<?php
class User {
    private $conn;
    private $email;

    public function __construct($conn, $email) {
        $this->conn = $conn;
        $this->email = $email;
    }

    public function getUserData() {
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bind_param('s', $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getBookBucks() {
        $query = "SELECT book_bucks FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['book_bucks'] ?? 0;
    }
}
?>
