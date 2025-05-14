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
        $leagues = $this->league->getLeagues();
        $league = null;
        if (isset($_GET['id'])) {
            $league = $this->league->getLeagueById($_GET['id']);
            if (!$league) {
                header('Location: /error/404');
                exit;
            }
        }
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
}
