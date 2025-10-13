<?php

namespace App\Model;

use App\Database\Database;

class Base
{
    protected static $table = '';
    protected static $pdo;
    protected static $queryParts = array();
    protected static $bindings = array();

    public $id;

    public function __construct($dados = array())
    {
        self::$pdo = Database::getInstance()->getConnection();
        foreach ($dados as $chave => $valor) {
            $this->$chave = $valor;
        }
    }

    // Utilitário para bind_param com tipos 's' para todos os valores (simplificação)
    protected static function bindParams($stmt, $params)
    {
        if (empty($params)) return;

        // Todos os parâmetros são tratados como string 's', ajuste aqui se quiser tipos dinâmicos
        $types = str_repeat('s', count($params));
        // bind_param exige parâmetros passados por referência
        $refs = array();
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }
        array_unshift($refs, $types);
        call_user_func_array(array($stmt, 'bind_param'), $refs);
    }

    public function save()
    {
        $props = get_object_vars($this);

        if ($this->id) {
            $propsCopy = $props;
            unset($propsCopy['id']);
            unset($propsCopy['created_at']);

            $sets = [];
            $values = [];

            foreach ($propsCopy as $chave => $valor) {
                $sets[] = "`$chave` = ?";
                $values[] = $valor;
            }

            $values[] = $this->id;
            $sql = "UPDATE `" . static::$table . "` SET " . implode(', ', $sets) . " WHERE `id` = ?";
            $stmt = self::$pdo->prepare($sql);
            self::bindParams($stmt, $values);
            error_log($sql);
            return $stmt->execute();
        } else {
            if (!isset($this->id) || $this->id == NULL) {
                unset($props['id']);
            }
            unset($props['created_at']); // Se for autogerenciado no banco

            $campos = array_keys($props);
            $values = array_values($props);

            $placeholders = array_fill(0, count($values), '?');
            $formatedCampos = array_map(fn($campo) => "`$campo`", $campos);
            $sql = "INSERT INTO `" . static::$table . "` (" . implode(', ', $formatedCampos) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = self::$pdo->prepare($sql);
            self::bindParams($stmt, $values);
            $success = $stmt->execute();

            return $success ? self::$pdo->insert_id : false;
        }
    }

    public function delete()
    {
        if (!$this->id) return false;
        $stmt = self::$pdo->prepare("DELETE FROM " . static::$table . " WHERE id = ?");
        self::bindParams($stmt, array($this->id));
        return $stmt->execute();
    }

    public static function find($id)
    {
        self::$pdo = Database::getInstance()->getConnection();
        $stmt = self::$pdo->prepare("SELECT * FROM " . static::$table . " WHERE id = ?");
        self::bindParams($stmt, array($id));
        $stmt->execute();
        $result = $stmt->get_result();
        $dados = $result->fetch_assoc();
        if ($dados) {
            return new static($dados);
        }
        return null;
    }

    public static function findBySlug($slug)
    {
        self::$pdo = Database::getInstance()->getConnection();
        $stmt = self::$pdo->prepare("SELECT * FROM " . static::$table . " WHERE slug = ?");
        self::bindParams($stmt, array($slug));
        $stmt->execute();
        $result = $stmt->get_result();
        $dados = $result->fetch_assoc();
        if ($dados) {
            return new static($dados);
        }
        return null;
    }

    public static function all()
    {
        self::$pdo = Database::getInstance()->getConnection();
        $stmt = self::$pdo->prepare("SELECT * FROM " . static::$table);
        $stmt->execute();
        $result = $stmt->get_result();

        $resultados = array();
        while ($row = $result->fetch_assoc()) {
            $resultados[] = new static($row);
        }
        return $resultados;
    }

    public static function first()
    {
        self::$pdo = Database::getInstance()->getConnection();
        $stmt = self::$pdo->prepare("SELECT * FROM " . static::$table . " LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $dados = $result->fetch_assoc();
        if ($dados) {
            return new static($dados);
        }
        return null;
    }

    public static function firstN($quantidade)
    {
        self::$pdo = Database::getInstance()->getConnection();
        $stmt = self::$pdo->prepare("SELECT * FROM " . static::$table . " LIMIT ?");
        self::bindParams($stmt, array($quantidade));
        $stmt->execute();
        $result = $stmt->get_result();

        $resultados = array();
        while ($row = $result->fetch_assoc()) {
            $resultados[] = new static($row);
        }
        return $resultados;
    }

    // --- Builder ---

    public static function where($campo, $valor)
    {
        static::$queryParts['where'][] = "`$campo` = ?";
        static::$bindings[] = $valor;
        return new static;
    }

    public static function lessThen($campo, $valor)
    {
        static::$queryParts['where'][] = "`$campo` < ?";
        static::$bindings[] = $valor;
        return new static;
    }

    public static function greaterThen($campo, $valor)
    {
        static::$queryParts['where'][] = "`$campo` > ?";
        static::$bindings[] = $valor;
        return new static;
    }

    public static function whereIn($campo, $array)
    {
        if (empty($array)) return new static;

        $placeholders = array_fill(0, count($array), '?');
        static::$queryParts['where'][] = "`$campo` IN (" . implode(', ', $placeholders) . ")";
        static::$bindings = array_merge(static::$bindings, $array);
        return new static;
    }

    public static function like($campo, $valor)
    {
        static::$queryParts['where'][] = "`$campo` LIKE ?";
        static::$bindings[] = $valor;
        return new static;
    }

    public function orderBy($campo, $direction = 'ASC')
    {
        static::$queryParts['order'] = "ORDER BY `$campo` " . strtoupper($direction);
        return $this;
    }

    public function limit($quantidade)
    {
        static::$queryParts['limit'] = "LIMIT " . intval($quantidade);
        return $this;
    }

    public function offset($quantidade)
    {
        static::$queryParts['offset'] = "OFFSET " . intval($quantidade);
        return $this;
    }

    public function get()
    {
        $sql = "SELECT * FROM " . static::$table;

        if (!empty(static::$queryParts['where'])) {
            $sql .= " WHERE " . implode(' AND ', static::$queryParts['where']);
        }

        if (!empty(static::$queryParts['order'])) {
            $sql .= " " . static::$queryParts['order'];
        }

        if (!empty(static::$queryParts['limit'])) {
            $sql .= " " . static::$queryParts['limit'];
        }

        if (!empty(static::$queryParts['offset'])) {
            $sql .= " " . static::$queryParts['offset'];
        }

        $stmt = self::$pdo->prepare($sql);
        self::bindParams($stmt, static::$bindings);
        error_log($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        static::$queryParts = array();
        static::$bindings = array();

        $resultados = array();
        while ($row = $result->fetch_assoc()) {
            $resultados[] = new static($row);
        }

        return $resultados;
    }

    public static function count($campo = null, $valor = null)
    {
        self::$pdo = Database::getInstance()->getConnection();
        $tabela = static::$table;

        if ($campo && $valor !== null) {
            $sql = "SELECT COUNT(*) AS total FROM `$tabela` WHERE `$campo` = ?";
            $stmt = self::$pdo->prepare($sql);
            self::bindParams($stmt, array($valor));
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return isset($row['total']) ? (int) $row['total'] : 0;
        } else {
            $sql = "SELECT COUNT(*) AS total FROM `$tabela`";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return isset($row['total']) ? (int) $row['total'] : 0;
        }
    }

    // --- Relacionamentos básicos ---
    public function hasMany($relatedClass, $foreignKey)
    {
        $parts = explode("\\", $relatedClass);
        $parts[0] = strtolower($parts[0]);
        require_once implode("/", $parts) . '.php';
        return call_user_func(array($relatedClass, 'where'), $foreignKey, $this->id)->get();
    }

    public function belongsTo($relatedClass, $foreignKey)
    {
        $parts = explode("\\", $relatedClass);
        $parts[0] = strtolower($parts[0]);
        require_once implode("/", $parts) . '.php';
        return call_user_func(array($relatedClass, 'find'), $this->$foreignKey);
    }

    public function gerarUUIDv4() {
        $dados = '';
        for ($i = 0; $i < 16; $i++) {
            $dados .= chr(mt_rand(0, 255));
        }

        // Ajusta os bits conforme a versão e variante
        $dados[6] = chr(ord($dados[6]) & 0x0f | 0x40); // versão 4
        $dados[8] = chr(ord($dados[8]) & 0x3f | 0x80); // variante RFC 4122

        $hex = bin2hex($dados);

        $uuid = sprintf('%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );

        return $uuid;
    }
}
