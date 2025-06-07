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
        $stmt = $this->pdo->query("SELECT c.*, tc.brasao_url as brasao FROM classificacao c, times_campeonato tc WHERE (c.time_nome = tc.time_nome and c.campeonato  = tc.campeonato)");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMobileRounds()
    {
        $stmt = $this->pdo->query("
        SELECT p.*,
               casa.brasao_url AS brasao_casa,
               fora.brasao_url AS brasao_fora
          FROM partidas p
          LEFT JOIN times_campeonato casa 
                 ON p.time_casa = casa.time_nome AND p.campeonato = casa.campeonato
          LEFT JOIN times_campeonato fora 
                 ON p.time_fora = fora.time_nome AND p.campeonato = fora.campeonato
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarTimes($nome, $brasao, $liga)
    {
        error_log("INSERT INTO times_campeonato (time_nome, campeonato, brasao_url) VALUES ('$nome', '$liga', '$brasao')");
        $stmt = $this->pdo->prepare("INSERT INTO times_campeonato (time_nome, campeonato, brasao_url) VALUES (:nome, :liga, :brasao)");
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':brasao', $brasao, PDO::PARAM_STR);
        $stmt->bindParam(':liga', $liga, PDO::PARAM_STR);
        $stmt->execute();
        return true;
    }

    public function saveMatch(
        $league, $group, $match,
        $date, $outerTeam, $homeTeam,
        $goalsHome, $goalsAway,
        $ended
    ) {
        try {
        $stmt = $this->pdo->prepare("
            INSERT INTO partidas (
                campeonato, grupo, rodada,
                data_partida, time_casa, time_fora,
                gols_casa, gols_fora, finalizada
            )
            VALUES (
                :league, :group, :match,
                :date, :homeTeam, :outerTeam,
                :goalsHome, :goalsAway, :ended
            )
            ON CONFLICT (campeonato, rodada, time_casa, time_fora)
            DO UPDATE SET
                gols_casa = excluded.gols_casa,
                gols_fora = excluded.gols_fora,
                finalizada = excluded.finalizada;");
        error_log('league' . $league);

        $stmt->bindParam(':league', $league, PDO::PARAM_STR);
        $stmt->bindParam(':group', $group, PDO::PARAM_STR);
        $stmt->bindParam(':match', $match, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':outerTeam', $outerTeam, PDO::PARAM_STR);
        $stmt->bindParam(':homeTeam', $homeTeam, PDO::PARAM_STR);
        $stmt->bindParam(':goalsHome', $goalsHome, PDO::PARAM_INT);
        $stmt->bindParam(':goalsAway', $goalsAway, PDO::PARAM_INT);
        $stmt->bindParam(':ended', $ended, PDO::PARAM_BOOL);
        $stmt->execute();
        return true;
        }
        catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }


    public function getClassification($id)
    {
        $stmt = $this->pdo->prepare("SELECT c.*, tc.brasao_url as brasao FROM classificacao c, times_campeonato tc WHERE (c.time_nome = tc.time_nome and c.campeonato  = tc.campeonato) and c.campeonato = :league");
        $stmt->bindParam(":league", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMatches($id)
    {
        $stmt = $this->pdo->prepare("
        SELECT p.*,
               casa.brasao_url AS brasao_casa,
               fora.brasao_url AS brasao_fora
          FROM partidas p
          LEFT JOIN times_campeonato casa 
                 ON p.time_casa = casa.time_nome AND p.campeonato = casa.campeonato
          LEFT JOIN times_campeonato fora 
                 ON p.time_fora = fora.time_nome AND p.campeonato = fora.campeonato
         WHERE p.campeonato = :league");
        $stmt->bindParam(":league", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($values)
    {
        $stmt = $this->pdo->prepare("
            UPDATE partidas
            SET gols_casa = :gols_casa, gols_fora = :gols_fora, finalizada = :ended
            WHERE id = :id");
        $stmt->bindParam(":gols_casa", $values['gols_casa']);
        $stmt->bindParam(":gols_fora", $values['gols_fora']);
        $stmt->bindParam(":ended", $values['finalizada']);
        $stmt->bindParam(":id", $values['id']);
        $stmt->execute();
    }
}
