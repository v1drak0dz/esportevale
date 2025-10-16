<?php

class BaseController
{
  protected $mysqli;

  public function __construct()
  {
    $this->mysqli = Database::getInstance();
  }

  public function index()
  {
    $ligas = $this->mysqli->query("SELECT DISTINCT nome FROM ligas ORDER BY nome")->fetch_all(MYSQLI_ASSOC);

    include_once('components/header.php');
    include_once('views/index.php');
    include_once('components/footer.php');
  }
}