<?php

class Database {
    private static $instance = null;
    private $mysqli;

    // Configurações de conexão com MySQL
    private static $host = 'localhost';
    private static $user = 'root';
    private static $pass = 'root';
    private static $dbname = 'esportevale';

    private function __construct() {
        $this->mysqli = new mysqli(self::$host, self::$user, self::$pass, self::$dbname);
        if ($this->mysqli->connect_error) {
            die('Erro de conexão com MySQL: ' . $this->mysqli->connect_error);
        }

        $this->mysqli->set_charset('utf8mb4');

        $user = $this->mysqli->query("SELECT * FROM users WHERE username = 'admin'")->fetch_object();
        if (!$user) {
          $this->mysqli->query("INSERT INTO users (username, password, name) VALUES ('admin', '".password_hash('admin', PASSWORD_DEFAULT)."', 'Admin')");
        }
    }

    public function __clone() {}
    public function __wakeup() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->mysqli;
    }
}
