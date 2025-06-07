<?php

class MobileController extends BaseController
{
    public function login()
    {
        header('Content-Type: application/json');

        $input = file_get_contents('php://input');
        $data  = json_decode($input, true);

        $email    = isset($data['email']) ? $data['email'] : (isset($_POST['email']) ? $_POST['email'] : null);
        $password = isset($data['password']) ? $data['password'] : (isset($_POST['password']) ? $_POST['password'] : null);

        if ($email && $password) {
            $passwordHash  = md5($password);
            $authenticated = $this->users->checkAuth($email, $passwordHash);

            if ($authenticated) {
                $token = md5($authenticated['id'] . time());
                $this->users->storeToken($authenticated['id'], $token);

                echo json_encode(array(
                    'success' => true,
                    'token'   => $token,
                    'user'    => array(
                        'id'       => $authenticated['id'],
                        'username' => $authenticated['username'],
                        'email'    => $authenticated['email'],
                    ),
                ));
            } else {
                header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Credenciais inválidas',
                ));
            }
        } else {
            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
            echo json_encode(array(
                'success' => false,
                'message' => 'Email e senha são obrigatórios',
            ));
        }

        exit;
    }

    public function register()
    {
        header('Content-Type: application/json');

        $input = file_get_contents('php://input');
        $data  = json_decode($input, true);

        $name     = isset($data['name']) ? $data['name'] : (isset($_POST['name']) ? $_POST['name'] : null);
        $username = isset($data['username']) ? $data['username'] : (isset($_POST['username']) ? $_POST['username'] : null);
        $email    = isset($data['email']) ? $data['email'] : (isset($_POST['email']) ? $_POST['email'] : null);
        $password = isset($data['password']) ? $data['password'] : (isset($_POST['password']) ? $_POST['password'] : null);

        if (! $name || ! $username || ! $email || ! $password) {
            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
            echo json_encode(array(
                'success' => false,
                'message' => 'Nome, usuário, email e senha são obrigatórios',
            ));
            exit;
        }

        $passwordHash = md5($password);
        $userId       = $this->users->createUser($name, $username, $email, $passwordHash);

        if ($userId) {
            echo json_encode(array(
                'success' => true,
                'message' => 'Usuário cadastrado com sucesso',
                'user'    => array(
                    'id'       => $userId,
                    'name'     => $name,
                    'username' => $username,
                    'email'    => $email,
                ),
            ));
        } else {
            header($_SERVER["SERVER_PROTOCOL"] . " 409 Conflict");
            echo json_encode(array(
                'success' => false,
                'message' => 'Nome de usuário ou email já estão em uso',
            ));
        }

        exit;
    }

    public function validateToken($token)
    {
        return $this->users->validateToken($token);
    }

    public function saveContent()
    {
        header('Content-Type: application/json');

        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $header           = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }

        if (! isset($headers['Authorization'])) {
            header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
            echo json_encode(array('success' => false, 'message' => 'Token não fornecido'));
            exit;
        }

        $authHeader = $headers['Authorization'];
        $parts      = explode(' ', $authHeader);
        if (count($parts) != 2 || $parts[0] !== 'Bearer') {
            header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
            echo json_encode(array('success' => false, 'message' => 'Token inválido'));
            exit;
        }

        $token  = $parts[1];
        $userId = $this->validateToken($token);
        if (! $userId) {
            header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
            echo json_encode(array('success' => false, 'message' => 'Token inválido'));
            exit;
        }

        if (! isset($_POST['title']) || ! isset($_POST['content'])) {
            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
            echo json_encode(array('success' => false, 'message' => 'Título e conteúdo são obrigatórios'));
            exit;
        }

        $title   = $_POST['title'];
        $content = $_POST['content'];
        $tags    = isset($_POST['tags']) ? $_POST['tags'] : '';

        $this->news->addNews($title, $content, $tags, $userId);

        echo json_encode(array('success' => true, 'message' => 'Conteúdo salvo com sucesso'));
        exit;
    }

    public function getAllContents()
    {
        $contents = $this->news->getNews();
        echo json_encode(array('success' => true, 'contents' => $contents));
        exit;
    }

    public function getContents()
    {
        header('Content-Type: application/json');

        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $header           = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }

        error_log("Headers: " . json_encode($headers));

        if (! isset($headers['Authorization'])) {
            header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
            echo json_encode(array('success' => false, 'message' => 'Token não fornecido'));
            exit;
        }

        $authHeader = $headers['Authorization'];
        $parts      = explode(' ', $authHeader);
        if (count($parts) != 2 || $parts[0] !== 'Bearer') {
            header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
            echo json_encode(array('success' => false, 'message' => 'Token inválido: ' . $authHeader));
            exit;
        }

        $token = $parts[1];
        error_log('Token recebido: ' . $token);

        $userId = $this->validateToken($token);
        error_log('User found' . $userId);
        if (! $userId) {
            header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
            echo json_encode(array('success' => false, 'message' => 'Token inválido: ' . $token));
            exit;
        }

        $contents = $this->news->getNewsByUser($userId);

        echo json_encode(array('success' => true, 'contents' => $contents));
        exit;
    }

    public function getPostTags()
    {
        if (! isset($_GET['id'])) {
            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
            echo json_encode(array('success' => false, 'message' => 'ID do conteúdo não fornecido'));
            exit;
        }

        $postId = $_GET['id'];
        $tags   = $this->news->getPostTags($postId);
        $result = array();
        foreach ($tags as $tag) {
            array_push($result, trim($tag['nome']));
        }

        echo json_encode(array('success' => true, 'tags' => $result));
        exit;
    }

    public function getComments()
    {
        $id       = $_GET['id'];
        $comments = $this->news->getNewsCommentary($id);
        echo json_encode(array('success' => true, 'comments' => $comments));
        exit;
    }

    public function getLeagues()
    {
        $result  = $this->league->getMobileLeagues();
        $leagues = array();
        foreach ($result as $league) {
            array_push($leagues, $league);
        }
        echo json_encode(array('success' => true, 'leagues' => $leagues));
        exit;
    }

    public function getRounds()
    {
        $results = $this->league->getMobileRounds();
        $rounds  = array();
        foreach ($results as $league) {
            array_push($rounds, $league);
        }
        echo json_encode(array('success' => true, 'rounds' => $rounds));
        exit;
    }
}
