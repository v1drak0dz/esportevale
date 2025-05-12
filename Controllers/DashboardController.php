<?php

class DashboardController {
    private $news;
    private $league;

    public function __construct() {
        $this->news = new News();
        $this->league = new League();
    }

    public function indexNews() {
        $newslist = $this->news->getNewsByUser(Session::getInstance()->get('user_id'));
        if (!$newslist) {
            $newslist = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhuma notícia encontrada.'));
        }
        include_once('components/header.php');
        include_once('templates/dashboard_news.php');
        include_once('components/footer.php');
    }

    public function indexLeagues() {
        $leagueslist = $this->league->getLeagues();
        if (!$leagueslist) {
            $leagueslist = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhuma liga encontrada.'));
        }
        include_once('components/header.php');
        include_once('templates/dashboard_leagues.php');
        include_once('components/footer.php');
    }

    public function addNews() {
        $news = null;
        if (isset($_GET['id'])) {
            $news = $this->news->getNewsById($_GET['id']);
            if (!$news) {
                header('Location: /404');
                exit;
            }
        }
        include_once('components/header.php');
        include_once('templates/newsform.php');
        include_once('components/footer.php');
    }

    public function addLeague() {
        $league = null;
        if (isset($_GET['id'])) {
            $league = $this->league->getLeagueById($_GET['id']);
            if (!$league) {
                header('Location: /404');
                exit;
            }
        }
        include_once('components/header.php');
        include_once('templates/leagueform.php');
        include_once('components/footer.php');
    }

    public function executeAddNews() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /unauthorized');
            exit;
        }

        $action = isset($_POST['action']) ? $_POST['action'] : null;

        if ($action === 'save') {
            $title = isset($_POST['title']) ? $_POST['title'] : null;
            $content = isset($_POST['content']) ? $_POST['content'] : null;
            $this->news->addNews($title, $content, Session::getInstance()->get('user_id'));
            header('Location: /dashboard/news');
            exit;
        } else {
            header('Location: /dashboard/news');
            exit;
        }
    }

    public function executeAddLeague() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /unauthorized');
            exit;
        }

        $action = isset($_POST['action']) ? $_POST['action'] : null;

        if ($action === 'save') {
            $title = isset($_POST['title']) ? $_POST['title'] : null;
            $table_content = isset($_POST['table_content']) ? $_POST['table_content'] : null;
            $round_content = isset($_POST['round_content']) ? $_POST['round_content'] : null;
            $this->league->atualizarLiga($title, $table_content, $round_content, Session::getInstance()->get('user_id'));
            header('Location: /dashboard/leagues');
            exit;
        } else {
            header('Location: /dashboard/leagues');
            exit;
        }
    }

    public function executeDeleteNews() {
        if (isset($_GET['id'])) {
            $this->news->deleteNews($_GET['id']);
        }
        header('Location: /dashboard/news');
        exit;
    }
}
