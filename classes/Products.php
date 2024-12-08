<?php

include_once("Db.php");

class Product {
    // private $conn;
    private $title;
    private $author_id;
    private $price;
    private $isbn;
    private $image_url;
    private $description;
    private $published_date;
    private $stock;
    private $category_id;
    private $Type;
    private $detailed_description;
    private $subgenre;
    

    // public function __construct($conn) {
    //     $this->conn = $conn;
    // }

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
        if (empty($title)) {
            throw new Exception("Title cannot be empty");
        }
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of author_id
     */ 
    public function getAuthor_id()
    {
        return $this->author_id;
    }

    /**
     * Set the value of author_id
     *
     * @return  self
     */ 
    public function setAuthor_id($author_id)
    {
        if (!is_numeric($author_id) || $author_id <= 0) {
            throw new Exception("Author ID must be a positive number");
        }
        $this->author_id = $author_id;

        return $this;
    }

    /**
     * Get the value of price
     */ 
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */ 
    public function setPrice($price)
    {
        if (!is_numeric($price) || $price < 0) {
            throw new Exception("Price cannot be negative");
        }
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of isbn
     */ 
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Set the value of isbn
     *
     * @return  self
     */ 
    public function setIsbn($isbn)
    {
        if (empty($isbn)) {
            throw new Exception("ISBN cannot be empty");
        }
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get the value of image_url
     */ 
    public function getImage_url()
    {
        return $this->image_url;
    }

    /**
     * Set the value of image_url
     *
     * @return  self
     */ 
    public function setImage_url($image_url)
    {
        if (empty($image_url)) {
            throw new Exception("Image URL cannot be empty");
        }
        $this->image_url = $image_url;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        if (empty($description)) {
            throw new Exception("Description cannot be empty");
        }
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of published_date
     */ 
    public function getPublished_date()
    {
        return $this->published_date;
    }

    /**
     * Set the value of published_date
     *
     * @return  self
     */ 
    public function setPublished_date($published_date)
    {
        if (empty($published_date)) {
            throw new Exception("Published date cannot be empty");
        }
        $this->published_date = $published_date;

        return $this;
    }

    /**
     * Get the value of stock
     */ 
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set the value of stock
     *
     * @return  self
     */ 
    public function setStock($stock)
    {
        if (!is_numeric($stock) || $stock < 0) {
            throw new Exception("Stock cannot be negative");
        }
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get the value of category_id
     */ 
    public function getCategory_id()
    {
        return $this->category_id;
    }

    /**
     * Set the value of category_id
     *
     * @return  self
     */ 
    public function setCategory_id($category_id)
    {
        if (!is_numeric($category_id) || $category_id <= 0) {
            throw new Exception("Category ID must be a positive number");
        }
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * Get the value of Type
     */ 
    public function getType()
    {
        return $this->Type;
    }

    /**
     * Set the value of Type
     *
     * @return  self
     */ 
    public function setType($Type)
    {
        if (empty($Type)) {
            throw new Exception("Type cannot be empty");
        }
        $this->Type = $Type;

        return $this;
    }

    /**
     * Get the value of detailed_description
     */ 
    public function getDetailed_description()
    {
        return $this->detailed_description;
    }

    /**
     * Set the value of detailed_description
     *
     * @return  self
     */ 
    public function setDetailed_description($detailed_description)
    {
        if (empty($detailed_description)) {
            throw new Exception("Detailed description cannot be empty");
        }
        $this->detailed_description = $detailed_description;

        return $this;
    }

       /**
     * Get the value of subgenre
     */ 
    public function getSubgenre()
    {
        return $this->subgenre;
    }

    /**
     * Set the value of subgenre
     *
     * @return  self
     */ 
    public function setSubgenre($subgenre)
    {
        $this->subgenre = $subgenre;

        return $this;
    }

    public function save($author_id, $db) {
        $conn = $db->connect();
        $sql = "INSERT INTO books (
                    title, 
                    author_id, 
                    stock, 
                    type, 
                    subgenre, 
                    price, 
                    isbn, 
                    image_url, 
                    description, 
                    detailed_description,
                    category_id,
                    published_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt = $conn->prepare($sql);
    
        if (!$stmt) {
            throw new Exception("Error preparing the query: " . $conn->error);
        }
    
        $stmt->bind_param(
            'sisssdssssss',
            $this->title,
            $author_id,
            $this->stock,
            $this->Type,
            $this->subgenre,
            $this->price,
            $this->isbn,
            $this->image_url,
            $this->description,
            $this->detailed_description,
            $this->category_id,
            $this->published_date
        );
    
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return true; // Successfully saved
        } else {
            throw new Exception("Error executing the query: " . $stmt->error);
        }
    }
    

}

?>