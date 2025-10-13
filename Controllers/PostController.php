<?php

class PostController extends BaseController
{
  public function postar()
  {
    $name = $_POST['title'];
    $link = $_POST['link'];

    $this->mysqli->query("INSERT INTO videos (titulo, link) VALUES ('$name', '$link')");

    header('Location: /videos/lista');
    exit;
  }
}