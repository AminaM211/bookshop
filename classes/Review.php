<?php
require_once 'db.php';
class Review {
    private $conn;
    private $userId;

    public function __construct($conn, $userId) {
        $this->conn = $conn;
        $this->userId = $userId;
    }


    public function addReview($bookId, $userId, $rating, $comment, $title) {
        $sql = "INSERT INTO reviews (book_id, user_id, rating, comment, title) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iisss', $bookId, $userId, $rating, $comment, $title);
        return $stmt->execute();
    }

    public static function getAll($conn, $book_id) {
        $stmt = $conn->prepare('SELECT * FROM reviews WHERE book_id = ?');
        $stmt->bind_param('i', $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        return $reviews;
    }
}
?>
