<?php

class AuthController extends BaseController
{
  public function login()
  {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $this->mysqli->query("SELECT * FROM users WHERE username = '$email'")->fetch_object();
    if ($user) {
      if (password_verify($password, $user->password)) {
        Session::getInstance()->set('user', $user->username);
        Session::getInstance()->set('user_id', $user->id);
        header('Location: /ligas/lista');
        exit;
      } else {
        echo '<script>alert("Usuário ou senha incorretos.");</script>';
      }
    }
  }

  public function logout()
  {
    Session::getInstance()->destroy();
    header('Location: /');
    exit;
  }
}
