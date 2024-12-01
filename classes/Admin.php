<?php 
class Admin {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function isAdmin($email) {
        $stmt = $this->conn->prepare('SELECT is_admin FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user['is_admin'] === 1;
    }
}

?>