<?php

class HomeController {
    private $news;
    private $league;

    public function __construct() {
        $this->news = new News();
        $this->league = new League();
    }

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
        

        include_once('components/header.php');
        include_once('templates/home.php');
        include_once('components/footer.php');
    }

    public function news_index()
    {
        $newslist = $this->news->getNews();
        if (!$newslist) {
            $newslist = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhuma notícia encontrada.'));
        }
        include_once('components/header.php');
        include_once('templates/news_index.php');
        include_once('components/footer.php');
    }

    public function news() {
        $news_id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($news_id === null) {
            header('Location: /404');
            exit;
        }

        $news = $this->news->getNewsById($news_id);
        if (!$news) {
            header('Location: /404');
            exit;
        }

        $news_commentary = $this->news->getNewsCommentary($news_id);

        $relatedNews = $this->news->getNewsLimited(3);

        $isLiked = $this->news->userLikedPost(Session::getInstance()->getUserId(), $news_id);

        $likeCount = $this->news->postLikeCount($news_id);

        $tags = $this->news->getPostTags($news_id);

        include_once('components/header.php');
        include_once('templates/news.php');
        include_once('components/footer.php');
    }

    private function renderComments($comments, $parent_id = null, $level = 0) {
        $marginLeft = $level * 20; // cada nível adiciona 20px de margem
    
        foreach ($comments as $comment) {
            if ($comment['parent_id'] == $parent_id) {
                echo "<div class='card mb-2' style='margin-left: {$marginLeft}px;'>";
                echo "  <div class='card-body p-2'>";
                echo "    <p class='mb-1'>{$comment['commentary']}</p>";
                echo "    <small class='text-muted'>Comentário #{$comment['id']} | Autor ID: {$comment['user_name']} | " . date('d/m/Y H:i', strtotime($comment['created_at'])) . "</small>";
                echo "  </div>";
                echo "</div>";
    
                // Recursivamente renderiza os filhos
                $this->renderComments($comments, $comment['id'], $level + 1);
            }
        }
    }

    public function sendCommentary() {
        $news_id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($news_id === null) {
            header('Location: /404');
            exit;
        }
        $this->news->saveCommentary($news_id, $_POST['commentary'], Session::getInstance()->getUserId());
        header('Location: /news?id=' . $news_id);
    }

    public function like() {
        $news_id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($news_id === null) {
            header('Location: /404');
            exit;
        }
        $this->news->toggleLike(Session::getInstance()->getUserId(), $news_id);
        $liked = $this->news->userLikedPost(Session::getInstance()->getUserId(), $news_id);
        $qtt = $this->news->postLikeCount($news_id);
        echo json_encode(array("msg" => $liked, "qtt" => $qtt));
    }
}
