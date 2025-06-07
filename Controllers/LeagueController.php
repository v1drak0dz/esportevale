<?php
// LeagueController.php

class LeagueController  extends BaseController
{
    public function dashboard() {
        $leagueslist = $this->league->getLeagues();
        $leagues = $this->league->getLeagues();
        if (!$leagueslist) {
            $leagueslist = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhuma liga encontrada.'));
        }
        include_once('components/header.php');
        include_once('templates/leagues/dashboard.php');
        include_once('components/footer.php');
    }

    public function add() {
        $league_id = $_GET['campeonato'];
        $currLeague = $this->league->getMatches($league_id);

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
        $rodadas = array($rodada_atual-2, $rodada_atual-1, $rodada_atual, $rodada_atual+1, $rodada_atual+2);
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
