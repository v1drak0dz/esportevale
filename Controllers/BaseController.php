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
}