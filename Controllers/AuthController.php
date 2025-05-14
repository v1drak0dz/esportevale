<?php

class AuthController extends BaseController {

    public function loginPage() {
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/auth/login.php');
        include_once('components/footer.php');
    }

    public function loginAction() {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = md5($_POST['password']); // Ainda funciona, mas é fraco para segurança moderna

            $authenticated = $this->users->checkAuth($email, $password);
            if ($authenticated !== null) {
                $session = Session::getInstance();
                $session->set('user', $authenticated['username']);
                $session->set('user_id', $authenticated['id']);
                header('Location: /');
                exit;
            } else {
                Session::getInstance()->set('error', 'Usuário ou senha incorretos.');
                header('Location: /auth/login');
                exit;
            }
        } else {
            Session::getInstance()->set('error', 'Preencha todos os campos.');
            header('Location: /auth/login');
            exit;
        }
    }

    public function logout() {
        Session::getInstance()->destroy();
        header('Location: /');
        exit;
    }

    public function registerPage() {
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/auth/register.php');
        include_once('components/footer.php');
    }


    public function registerAction() {
        if (isset($_POST['name']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
            $name = $_POST['name'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = md5($_POST['password']); // Ainda funciona, mas é fraco para segurança moderna

            $user_id = $this->users->createUser($name, $username, $email, $password);
            if (!$user_id) {
                Session::getInstance()->set('error', 'Usuário ou email já cadastrados.');
                header('Location: /auth/register');
                exit;
            }
            $session = Session::getInstance();
            $session->set('user', $username);
            $session->set('user_id', $user_id);
            header('Location: /');
            exit;
        } else {
            Session::getInstance()->set('error', 'Preencha todos os campos.');
            header('Location: /auth/register');
            exit;
        }
    }
}
