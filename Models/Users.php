<?php
// Models/Users.php

class Users {
    private $db; 

    public function __construct() {
        // Supondo que Database::getInstance() retorne uma conexão mysqli
        $this->db = Database::getInstance();
    }

    public function checkAuth($user, $password) {
        $sql = "SELECT id, username, email FROM users WHERE email = ? AND password = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta: " . $this->db->error);
        }

        $stmt->bind_param("ss", $user, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        $userData = $result->fetch_assoc();
        $stmt->close();

        return $userData ? $userData : null;
    }

    public function createUser($name, $username, $email, $password) {
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta: " . $this->db->error);
        }

        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            return false;
        }

        $sql = "INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta: " . $this->db->error);
        }

        $stmt->bind_param("ssss", $name, $username, $email, $password);
        $stmt->execute();

        $insert_id = $stmt->insert_id;
        $stmt->close();

        return $insert_id;
    }

    public function storeToken($userId, $token) {
        // MySQL não tem INSERT OR REPLACE, usamos INSERT ... ON DUPLICATE KEY UPDATE
        // Para isso, user_id deve ser chave única em user_tokens

        $sql = "INSERT INTO user_tokens (user_id, token, created_at, expires_at) VALUES (?, ?, NOW(), NULL)
                ON DUPLICATE KEY UPDATE token = VALUES(token), created_at = VALUES(created_at), expires_at = VALUES(expires_at)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta: " . $this->db->error);
        }

        $stmt->bind_param("is", $userId, $token);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function validateToken($token) {
        $sql = "SELECT user_id FROM user_tokens WHERE token = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro na preparação da consulta: " . $this->db->error);
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        $user_id = $result->fetch_assoc();
        $stmt->close();

        return $user_id ? $user_id['user_id'] : false;
    }
}
