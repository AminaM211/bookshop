<?php
class Review {
    private $conn;
    private $book_id;
    private $user_id;
    private $comment;
    private $score;
    private $title;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Get the value of book_id
     */ 
    public function getBook_id()
    {
        return $this->book_id;
    }

    /**
     * Set the value of book_id
     *
     * @return  self
     */ 
    public function setBook_id($book_id)
    {
        $this->book_id = $book_id;

        return $this;
    }

    /**
     * Get the value of user_id
     */ 
    public function getUser_id()
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     *
     * @return  self
     */ 
    public function setUser_id($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of comment
     */ 
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the value of comment
     *
     * @return  self
     */ 
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the value of score
     */ 
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set the value of score
     *
     * @return  self
     */ 
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function saveReview($user_id, $book_id, $comment, $score, $title) {
        $stmt = $this->conn->prepare("INSERT INTO reviews (user_id, book_id, comment, score, title) VALUES (?, ?, ?, ?, ?)");

        $comment = $this->getComment();
        $score = $this->getScore();
        $title = $this->getTitle();
        $book_id = $this->getBook_id();
        $user_id = $this->getUser_id();


        $stmt->bind_param("iisss", $user_id, $book_id, $comment, $score, $title);
        
        $result = $stmt->execute();
        return $result;
    }

    public function getReviews($book_id) {
        $stmt = $this->conn->prepare("SELECT r.comment, r.title, r.score, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.book_id = ?");
        $stmt->bind_param("i", $book_id);
        $result = $stmt->execute();        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

?>
