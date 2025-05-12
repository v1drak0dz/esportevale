<?php

class Database {
    private static $instance = null;
    private $pdo;
    private static $db_file = 'database.sqlite'; // Caminho para o arquivo SQLite

    private function __construct() {
        $dsn = 'sqlite:' . self::$db_file;

        try {
            $this->pdo = new PDO($dsn, null, null);
            $this->createTables();
        } catch (PDOException $e) {
            die('Erro ao conectar ao banco de dados SQLite: ' . $e->getMessage());
        }
    }

    public function __clone() {}
    public function __wakeup() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }

    private function createTables() {
        $sql = "CREATE TABLE IF NOT EXISTS leagues (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            url TEXT NOT NULL,
            tabela_html TEXT,
            rodada_html TEXT,
            atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP
        );";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            content TEXT,
            author INTEGER,
            modified_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (author) REFERENCES users(id) ON DELETE SET NULL
        );";
        $this->pdo->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            active INTEGER DEFAULT 1,
            role TEXT NOT NULL DEFAULT 'user',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );";
        $this->pdo->exec($sql);
    }
}
