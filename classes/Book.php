<?php
class Book {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getBooks($genreFilter = 'all', $typeFilter = 'all') {
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

        $stmt = $this->conn->prepare($sql);

        if ($genreFilter !== 'all' && $typeFilter !== 'all') {
            $stmt->bind_param('ss', $genreFilter, $typeFilter);
        } elseif ($genreFilter !== 'all') {
            $stmt->bind_param('s', $genreFilter);
        } elseif ($typeFilter !== 'all') {
            $stmt->bind_param('s', $typeFilter);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function CartBooks ($userId) {
    $sql = "SELECT books.*, cart.quantity, authors.first_name, authors.last_name FROM books
        INNER JOIN cart ON books.id = cart.book_id
        INNER JOIN authors ON books.author_id = authors.id
        WHERE cart.user_id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getBooksByGenre($genreFilter = 'romance', $limit = 6) {
        // Basisquery
        $sql = "SELECT books.*, authors.first_name, authors.last_name 
                FROM books 
                LEFT JOIN authors ON books.author_id = authors.id 
                WHERE category_id = (
                    SELECT id FROM categories WHERE name = ?
                )
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param('si', $genreFilter, $limit); 
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC); 
        }

        return [];  // Lege array als de query niet werkt
    }
}

?>
