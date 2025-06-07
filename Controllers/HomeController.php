<?php

class HomeController extends BaseController {

    public function index() {
        $newslist = $this->news->getNewsLimited(5);
        if (!$newslist) {
            $newslist = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhuma notícia encontrada.'));
        }

        $leaguetable = $this->league->getLeagueById(1);
        if (!$leaguetable) {
            $leaguetable = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhuma liga encontrada.'));
        }

        $leagues = $this->league->getLeagues();
        
        $cur_league = $this->league->getClassification('Brasileirão Série A');
        $rows_count = count($cur_league);

        include_once('components/header.php');
        include_once('templates/home.php');
        include_once('components/footer.php');
    }
}
