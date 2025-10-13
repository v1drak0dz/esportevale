<?php

class LigaController extends BaseController
{
  public function lista()
  {
    $ligas = $this->mysqli->query("SELECT * FROM ligas ORDER BY nome")->fetch_all(MYSQLI_ASSOC);
    include_once('components/header.php');
    include_once('views/ligas.php');
    include_once('components/footer.php');
  }

  public function criar()
  {
    $title = $_POST['title'];
    $this->mysqli->query("INSERT INTO ligas (nome) VALUES ('$title')");
    header('Location: /ligas/lista');
  }
}