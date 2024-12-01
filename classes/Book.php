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
}

?>
