<?php
class Database {
    private $host = 'junction.proxy.rlwy.net';
    private $user = 'root';
    private $pass = 'JoTRKOPYmfOIxHylrywjlCkBrYGpOWvB';
    private $dbname = 'railway';
    private $port = 11795;
    public $conn;

    public function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname, $this->port);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }
}
?>
