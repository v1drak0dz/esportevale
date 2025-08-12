<?php
// LeagueController.php

class LeagueController  extends BaseController
{
    public function dashboard() {
        $leagueslist = $this->league->getLeagues();
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/leagues/dashboard.php');
        include_once('components/footer.php');
    }

    public function create() {
        include_once('components/header.php');
        include_once('templates/leagues/createLeague.php');
        include_once('components/footer.php');
    }

    public function add() {
        $league_id = $_GET['campeonato'];
        $currLeague = $this->league->getMatches($league_id);
        $teams = $this->league->getTeams($league_id);

        $rodada_atual_header = 0;
        for ($i = count($currLeague); $i > 0; $i--) {
            if ($currLeague[$i-1]['finalizada'] == 1) {
                $rodada_atual_header = $currLeague[$i-1]['rodada'];
                break;
            }
        }
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/leagues/form.php');
        include_once('components/footer.php');
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /error/unauthorized');
            exit;
        }

        $action = isset($_POST['action']) ? $_POST['action'] : null;

        if ($action === 'save') {
            $title = isset($_POST['title']) ? $_POST['title'] : null;
            $table_content = isset($_POST['table_content']) ? $_POST['table_content'] : null;
            $round_content = isset($_POST['round_content']) ? $_POST['round_content'] : null;
            $this->league->atualizarLiga($title, $table_content, $round_content, Session::getInstance()->get('user_id'));
            header('Location: /leagues/dashboard');
            exit;
        } else {
            header('Location: /leagues/dashboard');
            exit;
        }
    }

    public function show()
    {
        $league_id = $_GET['id'];
        $currLeague = $this->league->getLeagueById($league_id);
        if (!$currLeague) {
            header('Location: /error/404');
            exit;
        }

        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/leagues/index.php');
        include_once('components/footer.php');
    }

    public function getClassification()
    {
        $league_id = $_GET['campeonato'];
        $currLeague = $this->league->getClassification($league_id);
        if (!$currLeague) {
            header('Location: /error/404');
            exit;
        }

        $groupedLeague = array();
        if ($currLeague[0]['grupo'] != null) {
            foreach ($currLeague as $value) {
                $grupo = $value['grupo'];

                if (!isset($groupedLeague[$grupo])) {
                    $groupedLeague[$grupo] = array();
                }

                $groupedLeague[$grupo][] = $value;
            }
        }

        if (!empty($groupedLeague)) {
            uksort($groupedLeague, function ($a, $b) {
                preg_match('/(\d+)/', $a, $matchA);
                preg_match('/(\d+)/', $b, $matchB);

                $numA = isset($matchA[0]) ? (int)$matchA[0] : 0;
                $numB = isset($matchB[0]) ? (int)$matchB[0] : 0;

                if ($numA == $numB) return 0;

                return ($numA < $numB) ? -1: 1;
            });
        }

        $matches = $this->league->getMatches($league_id);
        $rodada_atual_header = 0;
        for ($i = count($matches); $i > 0; $i--) {
            if ($matches[$i-1]['finalizada'] == 1) {
                $rodada_atual_header = $matches[$i-1]['rodada'];
                break;
            }
        }
        $rows_count = count($currLeague);
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/leagues/index.php');
        include_once('components/footer.php');
    }

    public function saveMatches() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $currLeague = $this->league->getMatches($_GET['campeonato']);
            usort($currLeague, function($a, $b) {
                $dataA = DateTime::createFromFormat('d/m/Y - H\hi', $a['data_partida']);
                $dataB = DateTime::createFromFormat('d/m/Y - H\hi', $b['data_partida']);
                return $dataA <=> $dataB;
            });
            include_once('components/header.php');
            include_once('templates/leagues/list.php');
            include_once('components/footer.php');
            exit;
        }

        if (!isset($_POST['match']) || !is_array($_POST['match'])) {
            header('Location: /leagues/dashboard');
            exit;
        }

        foreach ($_POST['match'] as $id => $dados) {
            // Prepara o array com os valores esperados
            $values = [
                'id' => (int)$dados['id'],
                'gols_casa' => isset($dados['gols_casa']) ? (int)$dados['gols_casa'] : 0,
                'gols_fora' => isset($dados['gols_fora']) ? (int)$dados['gols_fora'] : 0,
                'finalizada' => isset($dados['finalizada']) ? (int)$dados['finalizada'] : 0
            ];

            // Chama o update da model
            $this->league->update($values);
        }

        Session::getInstance()->setAlert([
            'type' => 'success',
            'text' => 'Partidas salvas com sucesso.'
        ]);

        header('Location: /leagues/dashboard');
        exit;
    }


    public function getMatches()
    {
        $league_id = $_GET['campeonato'];
        $currLeague = $this->league->getMatches($league_id);
        $rodada_atual = $_GET['rodada'];
        if (!$currLeague) {
            header('Location: /error/404');
            exit;
        }

        $rodada_atual_header = 0;
        for ($i = count($currLeague); $i > 0; $i--) {
            if ($currLeague[$i-1]['finalizada'] == 1) {
                $rodada_atual_header = $currLeague[$i-1]['rodada'];
                break;
            }
        }

        $rodadas_filtradas = array();
        foreach($currLeague as $rodada) {
            if ($rodada['rodada'] == $rodada_atual) {
                array_push($rodadas_filtradas, $rodada);
            }
        }

        usort($rodadas_filtradas, function($a, $b) {
            $dataA = DateTime::createFromFormat('d/m/Y - H\hi', $a['data_partida']);
            $dataB = DateTime::createFromFormat('d/m/Y - H\hi', $b['data_partida']);
            return $dataA <=> $dataB;
        });

        $rodadasRaw = $this->league->getRodadasNumber($league_id);

        $rodadasRaw = array_map('intval', $rodadasRaw);

        $rodadas = array();

        if (!empty($rodadasRaw) && isset($rodada_atual)) {
            // Gera um array com -2 até +2 da rodada atual
            for ($i = -2; $i <= 2; $i++) {
                $rodada = $rodada_atual + $i;
                if (in_array($rodada, $rodadasRaw)) {
                    array_push($rodadas,$rodada);
                }
            }
        }
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/leagues/index.php');
        include_once('components/footer.php');
    }

    public function update()
    {
        $this->league->update($_POST);
        header('Location: /leagues/dashboard');
        exit;
    }
}
