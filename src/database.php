<?php

class Database {
    private static $instance = null;
    private $mysqli;

    // Configurações de conexão com MySQL
    private static $host = 'localhost';
    private static $user = 'seu_usuario';
    private static $pass = 'sua_senha';
    private static $dbname = 'nome_do_banco';

    private function __construct() {
        $this->mysqli = new mysqli(self::$host, self::$user, self::$pass, self::$dbname);

        if ($this->mysqli->connect_error) {
            die('Erro de conexão com MySQL: ' . $this->mysqli->connect_error);
        }

        $this->createTables();
    }

    public function __clone() {}
    public function __wakeup() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->mysqli;
    }

    private function createTables() {
        $this->mysqli->query("
            CREATE TABLE IF NOT EXISTS leagues (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(255) NOT NULL,
                url VARCHAR(255) NOT NULL,
                tabela_html TEXT,
                rodada_html TEXT,
                atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->mysqli->query("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                active TINYINT(1) DEFAULT 1,
                role VARCHAR(50) NOT NULL DEFAULT 'user',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->mysqli->query("
            CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                content TEXT,
                author INT,
                modified_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (author) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }
}
