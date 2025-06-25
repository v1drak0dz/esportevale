<?php

class NewsController extends BaseController
{
    public function index()
    {
        if (isset($_GET['query'])) {
            $newslist = $this->news->getNewsBySearch($_GET['query']);
        } else {
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

    public function all()
    {
        $news = $this->news->getNews();
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        echo json_encode($news);
    }

    private function renderComments($comments, $parent_id = null, $level = 0)
    {
        $marginLeft = $level * 20;
        foreach ($comments as $comment) {
            if ($comment['parent_id'] == $parent_id) {
                echo "<div class='card mb-2 comentario' data-comment-id='{$comment['id']}' style='margin-left: {$marginLeft}px;'>";
                echo "<div class='card-body p-2'>";
                echo "<p class='mb-1'>{$comment['commentary']}</p>";
                echo "<small class='text-muted'>Comentário #{$comment['id']} | Autor ID: {$comment['user_name']} | " . date('d/m/Y H:i', strtotime($comment['created_at'])) . "</small>";
                echo "</div></div>";

                $this->renderComments($comments, $comment['id'], $level + 1);
            }
        }
    }

    public function videoForm()
    {
        if (isset($_GET['id'])) {
            $video = $this->news->getVideoById($_GET['id']);
        }

        include_once('components/header.php');
        include_once('templates/news/videoForm.php');
        include_once('components/footer.php');
    }

    public function deleteVideo()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $this->news->deleteVideos($id);
        header('Location: /news/dashboardVideos');
        exit;
    }

    public function videoUpload()
    {
        $action = isset($_POST['action']) ? $_POST['action'] : null;

        if ($action == 'save') {
            $title = isset($_POST['title']) ? $_POST['title'] : 'Sem titulo';
            $url = isset($_POST['url']) ? $_POST['url'] : 'Sem url';
            $author = isset($_POST['author']) ? $_POST['author'] : 1;
            $category = isset($_POST['category']) ? $_POST['category'] : 'Sem categoria';
            $id = isset($_POST['id']) ? $_POST['id'] : null;

            $uploadDir = dirname(__DIR__) . '/content/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $capaPath = null;

            if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['capa']['tmp_name'];
                $originalName = basename($_FILES['capa']['name']);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $newName = uniqid('capa_', true) . '.' . $extension;
                $destination = $uploadDir . $newName;

                if (move_uploaded_file($tmpName, $destination)) {
                    $capaPath = '/content/' . $newName;
                }
            }

            $this->news->saveVideo($id, $title, $url, $author, $category, $capaPath);
            header("Location: /");
            exit;
        } else {
            header("Location: /");
            exit;
        }
    }

    public function videoDashboard()
    {
        $videoslist = $this->news->getVideos();
        include_once('components/header.php');
        include_once('templates/news/videosDashboard.php');
        include_once('components/footer.php');
    }

    public function videoIndex()
    {
        if (isset($_GET['query'])) {
            $videoslist = $this->news->getVideosByQuery($_GET['query']);
        } else {
            $videoslist = $this->news->getVideos();
        }

        if (!$videoslist) {
            $videoslist = array();
            Session::getInstance()->setAlert(array('type' => 'info', 'text' => 'Nenhuma notícia encontrada.'));
        }

        include_once('components/header.php');
        include_once('templates/news/videos.php');
        include_once('components/footer.php');
    }

    public function comment()
    {
        $news_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;
        if ($news_id === null) {
            header('Location: /404');
            exit;
        }

        $this->news->saveCommentary($news_id, $_POST['reply'], Session::getInstance()->getUserId());
        header('Location: /news/show?id=' . $_GET['id']);
    }

    public function like()
    {
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

    public function form()
    {
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

    public function uploadImage()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Erro no upload da imagem.';
            exit;
        }

        $uploadDir = dirname(__FILE__) . '/../content/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        if (!in_array($ext, $allowed)) {
            header('HTTP/1.1 415 Unsupported Media Type');
            echo 'Extensão de imagem não permitida.';
            exit;
        }

        $filename = uniqid('img_', true) . '.' . $ext;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
            $publicPath = '/content/' . $filename;
            echo $publicPath;
            exit;
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo 'Falha ao salvar a imagem.';
            exit;
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /unauthorized');
            exit;
        }

        $action = isset($_POST['action']) ? $_POST['action'] : null;

        if ($action === 'save') {
            $title = isset($_POST['title']) ? $_POST['title'] : null;
            $content = isset($_POST['content']) ? $_POST['content'] : null;
            $tags = isset($_POST['tags']) ? $_POST['tags'] : null;
            $capa = null;

            if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(__FILE__) . '/../content/';
                $publicPath = '/content/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileTmpPath = $_FILES['capa']['tmp_name'];
                $fileName = basename($_FILES['capa']['name']);
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $safeName = uniqid('news_', true) . '.' . $fileExtension;
                $destination = $uploadDir . $safeName;

                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $capa = $publicPath . $safeName;
                }
            }

            $userId = Session::getInstance()->getUserId();
            if (!$userId) {
                $userId = 1;
            }

            $this->news->addNews($title, $content, $tags, $userId, $capa);

            if (Session::getInstance()->getUserId()) {
                header('Location: /news/dashboard');
            } else {
                header('Location: /');
            }
            exit;
        } else {
            header('Location: /news/dashboard');
            exit;
        }
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $this->news->deleteNews($_GET['id']);
        }
        header('Location: /news/dashboard');
        exit;
    }
}
