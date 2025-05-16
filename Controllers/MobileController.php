<?php

class MobileController extends BaseController
{
    public function auth()
    {
        header('Content-Type: application/json');

        // Tenta obter o corpo JSON cru (caso esteja vindo como application/json)
        $input = file_get_contents('php://input');
        $data  = json_decode($input, true);

        // Fallback: se não for JSON, tenta usar $_POST
        $email    = isset($data['email']) ? $data['email'] : (isset($_POST['email']) ? $_POST['email'] : null);
        $password = isset($data['password']) ? $data['password'] : (isset($_POST['password']) ? $_POST['password'] : null);

        if ($email && $password) {
            $passwordHash = md5($password); // Ainda fraco, mas necessário no PHP 5.3

            // Verifica credenciais
            $authenticated = $this->users->checkAuth($email, $passwordHash);

            if ($authenticated) {
                // Gera token simples
                $token = md5($authenticated['id'] . time());

                // Salva token
                $this->users->storeToken($authenticated['id'], $token);

                echo json_encode([
                    'success' => true,
                    'token'   => $token,
                    'user'    => [
                        'id'       => $authenticated['id'],
                        'username' => $authenticated['username'],
                        'email'    => $authenticated['email'],
                    ],
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Credenciais inválidas',
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Email e senha são obrigatórios',
            ]);
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

        // Pega headers HTTP (compatível PHP 5.3)
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $header           = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }

        if (! isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token não fornecido']);
            exit;
        }

        $authHeader = $headers['Authorization'];
        $parts      = explode(' ', $authHeader);
        if (count($parts) != 2 || $parts[0] !== 'Bearer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token inválido']);
            exit;
        }
        $token = $parts[1];

        $userId = $this->validateToken($token);
        if (! $userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token inválido']);
            exit;
        }

        // Continua com o resto do processamento normalmente...
        if (! isset($_POST['title']) || ! isset($_POST['content'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Título e conteúdo são obrigatórios']);
            exit;
        }

        $title   = $_POST['title'];
        $content = $_POST['content'];
        $tags    = isset($_POST['tags']) ? $_POST['tags'] : '';

        $this->news->addNews($title, $content, $tags, $userId);

        echo json_encode(['success' => true, 'message' => 'Conteúdo salvo com sucesso']);
        exit;
    }

    public function getAllContents()
    {
        $contents = $this->news->getNews();
        echo json_encode(['success' => true, 'contents' => $contents]);
        exit;
    }

    public function getContents()
    {
        header('Content-Type: application/json');

        // Função para pegar headers HTTP (compatível PHP 5.3)
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                // Converte HTTP_HEADER_NAME para Header-Name
                $header           = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }

        if (! isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token não fornecido']);
            exit;
        }

        $authHeader = $headers['Authorization'];
        $parts      = explode(' ', $authHeader);
        if (count($parts) != 2 || $parts[0] !== 'Bearer') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token inválido']);
            exit;
        }
        $token = $parts[1];
        error_log('Token recebido: ' . $token);

        $userId = $this->validateToken($token);
        error_log('User found' . $userId);
        if (! $userId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token inválido']);
            exit;
        }

        $contents = $this->news->getNewsByUser($userId);

        echo json_encode(['success' => true, 'contents' => $contents]);
        exit;
    }

    public function getPostTags()
    {
        // Pega o ID do conteúdo via GET
        if (! isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID do conteúdo não fornecido']);
            exit;
        }

        $postId = $_GET['id'];
        $tags   = $this->news->getPostTags($postId);
        error_log('tag are: ' . $tags);

        echo json_encode(['success' => true, 'tags' => $tags]);
        exit;
    }

    public function getLeagues()
    {
        $result = $this->league->getMobileLeagues();
        $leagues = array();
        foreach ($result as $league)
        {
            array_push($leagues, $league);
        }
        echo json_encode(['success' => true, 'leagues' => $leagues]);
        exit;
    }


    public function getRounds()
    {
        $results = $this->league->getMobileRounds();
        $rounds = array();
        foreach ($results as $league)
        {
            array_push($rounds, $league);
        }
        echo json_encode(['success' => true, 'rounds' => $rounds]);
        exit;
    }

}
