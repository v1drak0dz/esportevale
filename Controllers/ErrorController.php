<?php

class ErrorController {
    public function error404() {
        include_once('components/header.php');
        include_once('templates/404.php');
        include_once('components/footer.php');
    }

    public function unauthorized() {
        include_once('components/header.php');
        include_once('templates/unauthorized.php');
        include_once('components/footer.php');
    }
}