<?php

class ErrorController extends BaseController {
    public function error404() {
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/errors/404.php');
        include_once('components/footer.php');
    }

    public function unauthorized() {
        $leagues = $this->league->getLeagues();
        include_once('components/header.php');
        include_once('templates/errors/unauthorized.php');
        include_once('components/footer.php');
    }
}