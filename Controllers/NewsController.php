<?php

class NewsController extends BaseController
{
    public function index()
    {
        if (isset($_GET['query']))
        {
            $newslist = $this->news->getNewsBySearch($_GET['query']);
        }
        else
        {
            $newslist = $this->news->getNews();
        }
        if (!$newslist) {
            $newslist = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhuma notícia encontrada.'));
        }
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/news/index.php');
        include_once('components/footer.php');
    }

    public function dashboard()
    {
        $newslist = $this->news->getNewsByUser(Session::getInstance()->get('user_id'));
        if (!$newslist) {
            $newslist = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhuma notícia encontrada.'));
        }
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/news/dashboard.php');
        include_once('components/footer.php');
    }

    public function show()
    {
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
        if (!$tags) {
            $tags = array();
        }
        $leagues = $this->league->getLeagues();

        include_once('components/header.php');
        include_once('templates/news/show.php');
        include_once('components/footer.php');
    }

    public function all() {
        $news = $this->news->getNews();
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        echo json_encode($news);
    }

    private function renderComments($comments, $parent_id = null, $level = 0) {
        $marginLeft = $level * 20; // cada nível adiciona 20px de margem
    
        foreach ($comments as $comment) {
            if ($comment['parent_id'] == $parent_id) {
                echo "<div class='card mb-2 comentario' data-comment-id='{$comment['id']}' style='margin-left: {$marginLeft}px;'>";
                echo "  <div class='card-body p-2'>";
                echo "    <p class='mb-1'>{$comment['commentary']}</p>";
                echo "    <small class='text-muted'>Comentário #{$comment['id']} | Autor ID: {$comment['user_name']} | " . date('d/m/Y H:i', strtotime($comment['created_at'])) . "</small>";
                echo "    <button class='btn btn-link p-0 reply-btn' data-comment-id='{$comment['id']}'>Responder</button>";
                echo "  </div>";
                echo "</div>";
    
                // Recursivamente renderiza os filhos
                $this->renderComments($comments, $comment['id'], $level + 1);
            }
        }
    }

    public function comment() {
        $news_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;
        if ($news_id === null) {
            header('Location: /404');
            exit;
        }
        $this->news->saveCommentary($news_id, $_POST['reply'], Session::getInstance()->getUserId());
        header('Location: /news/show?id=' . $_GET['id']);
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

    public function form() {
        $news = null;
        if (isset($_GET['id'])) {
            $news = $this->news->getNewsById($_GET['id']);
            if (!$news) {
                header('Location: /404');
                exit;
            }
        }
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/news/form.php');
        include_once('components/footer.php');
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /unauthorized');
            exit;
        }

        $action = isset($_POST['action']) ? $_POST['action'] : null;

        if ($action === 'save') {
            $title = isset($_POST['title']) ? $_POST['title'] : null;
            $content = isset($_POST['content']) ? $_POST['content'] : null;
            $tags = isset($_POST['tags']) ? $_POST['tags'] : null;
            $this->news->addNews($title, $content, $tags, Session::getInstance()->get('user_id'));
            header('Location: /news/dashboard');
            exit;
        } else {
            header('Location: /news/dashboard');
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->news->deleteNews($_GET['id']);
        }
        header('Location: /news/dashboard');
        exit;
    }
}