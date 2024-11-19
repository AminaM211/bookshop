<?php
    class Review {
        private $text;
        private $postid;
        private $userid;

        /**
         * Get the value of text
         */ 
        public function getText()
        {
                return $this->text;
        }

        /**
         * Set the value of text
         *
         * @return  self
         */ 
        public function setText($text)
        {
                $this->text = $text;

                return $this;
        }

        /**
         * Get the value of postid
         */ 
        public function getPostid()
        {
                return $this->postid;
        }

        /**
         * Set the value of postid
         *
         * @return  self
         */ 
        public function setPostid($postid)
        {
                $this->postid = $postid;

                return $this;
        }

        /**
         * Get the value of userid
         */ 
        public function getUserid()
        {
                return $this->userid;
        }

        /**
         * Set the value of userid
         *
         * @return  self
         */ 
        public function setUserid($userid)
        {
                $this->userid = $userid;

                return $this;
        }

        public function save(){
            $conn = new PDO("mysql:host=localhost;dbname=bookstore", 'root', '');
            $statement = $conn->prepare('INSERT INTO reviews (text, postid, userid) VALUES (:text, :postid, :userid)');
            $text = $this->getText();
            $postid = $this->getPostid();
            $userid = $this->getUserid();
            
            $statement->bindValue(":text", $text);
            $statement->bindValue(":postid", $postid);
            $statement->bindValue(":userid", $userid);

            $result = $statement->execute();
            return $result;
        }

        public static function getAll($postid){
            $conn = new PDO("mysql:host=localhost;dbname=bookstore", 'root', '');
            $statement = $conn->prepare('SELECT * FROM reviews WHERE postid = :postid');
            $statement->bindValue(":postid", $postid);
            $statement->execute();
            $reviews = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $reviews;
        }
    }