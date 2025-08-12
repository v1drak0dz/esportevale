<?php

class HomeController extends BaseController {
    public function test()
    {
        echo phpinfo();
    }

    public function index() {
        $newslist = $this->news->getNewsLimited(5);
        if (!$newslist) {
            $newslist = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhuma notícia encontrada.'));
        }

        $videoslist = $this->news->getVideosLimited(5);
        if (!$videoslist) {
            $videoslist = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhum vídeo encontrado.'));
        }

        $atividadesMistas = array();

        // Adiciona posts
        foreach ($newslist as $n) {
            $atividadesMistas[] = array(
                'tipo' => 'post',
                'id' => $n->post_id,
                'titulo' => $n->post_title,
                'conteudo' => $n->post_content,
                'data' => $n->post_created,
                'capa' => $n->post_capa,
                'autor' => $n->post_author_name
            );
        }

        // Adiciona vídeos
        foreach ($videoslist as $v) {
            $atividadesMistas[] = array(
                'tipo' => 'video',
                'url' => $v->video_url,
                'titulo' => $v->video_title,
                'capa' => $v->video_capa,
                'categoria' => $v->video_category,
                'data' => $v->video_created_at,
                'autor' => $v->video_author
            );
        }

        // Ordena por data (mais recentes primeiro)
        usort($atividadesMistas, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

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
