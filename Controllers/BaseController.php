<?php

class BaseController
{
    protected $news;
    protected $league;
    protected $users;

    public function __construct()
    {
        $this->news = new News();
        $this->league = new League();
        $this->users = new Users();
    }

    public function receivePostKey($key)
    {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }
}