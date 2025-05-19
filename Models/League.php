<?php
// models/League.php

class League
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getLeagueById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM leagues WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function salvarLiga($nome, $url, $tabelaHtml, $rodadaHtml)
    {
        $stmt = $this->pdo->prepare("INSERT INTO leagues (nome, url, tabela_html, rodada_html) VALUES (:nome, :url, :tabela, :rodada)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':tabela', $tabelaHtml);
        $stmt->bindParam(':rodada', $rodadaHtml);
        $stmt->execute();
    }

    public function atualizarLiga($nome, $tabelaHtml, $rodadaHtml)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $stmt = $this->pdo->prepare("UPDATE leagues SET tabela_html = :tabela, rodada_html = :rodada, atualizado_em = :now WHERE nome = :nome");
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':tabela', $tabelaHtml, PDO::PARAM_STR);
            $stmt->bindParam(':rodada', $rodadaHtml, PDO::PARAM_STR);
            $stmt->bindParam(':now', $now, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
        
    }


    public function salvar($tipo, $conteudo)
    {
        $stmt = $this->pdo->prepare("INSERT INTO league_data (tipo, conteudo) VALUES (:tipo, :conteudo)");
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':conteudo', $conteudo);
        $stmt->execute();
    }

    public function obter($tipo)
    {
        $stmt = $this->pdo->prepare("SELECT conteudo FROM league_data WHERE tipo = :tipo ORDER BY atualizado_em DESC LIMIT 1");
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['conteudo'] : '';
    }

    public function getLeagues()
    {
        $stmt = $this->pdo->query("SELECT * FROM leagues ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getMobileLeagues()
    {
        $stmt = $this->pdo->query("SELECT id, nome, tabela_html FROM leagues ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMobileRounds()
    {
        $stmt = $this->pdo->query("SELECT id, nome, rodada_html FROM leagues ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
