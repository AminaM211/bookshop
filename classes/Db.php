<?php
class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $port;
    public $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST');
        $this->user = getenv('DB_USER');
        $this->pass = getenv('DB_PASS');
        $this->dbname = getenv('DB_NAME');
        $this->port = getenv('DB_PORT');
    }

    public function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname, $this->port);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }
}
?>