<?php
// models/League.php

class League
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance(); // deve retornar um mysqli
    }

    public function getLeagueById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM leagues WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_object();
    }

    public function salvarLiga($nome, $url, $tabelaHtml, $rodadaHtml)
    {
        $stmt = $this->pdo->prepare("INSERT INTO leagues (nome, url, tabela_html, rodada_html) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $url, $tabelaHtml, $rodadaHtml);
        $stmt->execute();
    }

    public function atualizarLiga($nome, $tabelaHtml, $rodadaHtml)
    {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare("UPDATE leagues SET tabela_html = ?, rodada_html = ?, atualizado_em = ? WHERE nome = ?");
        $stmt->bind_param("ssss", $tabelaHtml, $rodadaHtml, $now, $nome);
        return $stmt->execute();
    }

    public function salvar($tipo, $conteudo)
    {
        $stmt = $this->pdo->prepare("INSERT INTO league_data (tipo, conteudo) VALUES (?, ?)");
        $stmt->bind_param("ss", $tipo, $conteudo);
        $stmt->execute();
    }

    public function obter($tipo)
    {
        $stmt = $this->pdo->prepare("SELECT conteudo FROM league_data WHERE tipo = ? ORDER BY atualizado_em DESC LIMIT 1");
        $stmt->bind_param("s", $tipo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['conteudo'] : '';
    }

    public function getLeagues()
    {
        $result = $this->pdo->query("SELECT DISTINCT campeonato FROM partidas p");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMobileLeagues()
    {
        $result = $this->pdo->query("SELECT c.*, tc.brasao_url as brasao FROM classificacao c, times_campeonato tc WHERE (c.time_nome = tc.time_nome and c.campeonato  = tc.campeonato)");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMobileRounds()
    {
        $result = $this->pdo->query("
            SELECT p.*,
                   casa.brasao_url AS brasao_casa,
                   fora.brasao_url AS brasao_fora
              FROM partidas p
              LEFT JOIN times_campeonato casa
                     ON p.time_casa = casa.time_nome AND p.campeonato = casa.campeonato
              LEFT JOIN times_campeonato fora
                     ON p.time_fora = fora.time_nome AND p.campeonato = fora.campeonato
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTeams($league_id)
    {
        $stmt = $this->pdo->prepare("SELECT time_nome, brasao_url FROM times_campeonato WHERE campeonato = ?");
        $stmt->bind_param("s", $league_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function atualizarTimes($nome, $brasao, $liga)
    {
        $stmt = $this->pdo->prepare("INSERT INTO times_campeonato (time_nome, campeonato, brasao_url) SELECT ?, ?, ? WHERE NOT EXISTS (SELECT 1 FROM times_campeonato WHERE time_nome = ? AND campeonato = ?)");
        $stmt->bind_param("sssss", $nome, $liga, $brasao, $nome, $liga);
        return $stmt->execute();
    }

    public function saveMatch($league, $group, $match, $date, $outerTeam, $homeTeam, $goalsHome, $goalsAway, $ended)
    {
        $stmt = $this->pdo->prepare("REPLACE INTO partidas (
                campeonato, grupo, rodada,
                data_partida, time_casa, time_fora,
                gols_casa, gols_fora, finalizada
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssisssiii", $league, $group, $match, $date, $homeTeam, $outerTeam, $goalsHome, $goalsAway, $ended);
        return $stmt->execute();
    }

    public function getClassification($id)
    {
        $stmt = $this->pdo->prepare("SELECT c.*, tc.brasao_url as brasao FROM classificacao c, times_campeonato tc WHERE (c.time_nome = tc.time_nome and c.campeonato  = tc.campeonato) and c.campeonato = ? ORDER BY c.pontos DESC, c.vitorias DESC, c.saldo_gols DESC, c.gols_pro DESC");
        if (!$stmt) {
            die("Erro no prepare(): (" . $this->pdo->errno . ") " . $this->pdo->error);
        }
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
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
         WHERE p.campeonato = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRodadasNumber($id)
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT rodada FROM partidas WHERE campeonato = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rodadas = [];
        while ($row = $result->fetch_row()) {
            $rodadas[] = $row[0];
        }
        return $rodadas;
    }

    public function update($values)
    {
        $stmt = $this->pdo->prepare("UPDATE partidas SET gols_casa = ?, gols_fora = ?, finalizada = ? WHERE id = ?");
        $stmt->bind_param("iiii", $values['gols_casa'], $values['gols_fora'], $values['finalizada'], $values['id']);
        $stmt->execute();
    }

	public function save($gcasa, $gfora, $finalizada, $league, $data, $rodada, $tcasa, $tfora)
	{
		$sql = "UPDATE partidas
				   SET gols_casa = ?,
				       gols_fora = ?,
					   finalizada = ?,
				 WHERE campeonato = ?
				   AND data_partida = ?
				   AND time_casa = ?
				   AND time_fora = ?
				   AND rodada = ?";
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->bind_param('iiissssi', $gcasa, $gfora, $finalizada, $league, $data, $tcasa, $tfora, $rodada);
		$stmt->execute();
	}
}
