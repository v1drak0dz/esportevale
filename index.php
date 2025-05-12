<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('src/autoloader.php');
require_once('src/router.php');
require_once('src/database.php');
require_once('src/session.php');
require_once('src/utils.php');

$session = new Session();

add_route('/', 'HomeController@index');

add_route('/login', 'AuthController@login');
add_route('/execute_login', 'AuthController@executeLogin');
add_route('/logout', 'AuthController@logout');
add_route('/register', 'AuthController@register');
add_route('/execute_register', 'AuthController@executeRegister');

add_route('/dashboard/news', 'DashboardController@indexNews');
add_route('/dashboard/leagues', 'DashboardController@indexLeagues');

add_route('/add_news', 'DashboardController@addNews');
add_route('/save_news', 'DashboardController@executeAddNews');
add_route('/delete_news', 'DashboardController@executeDeleteNews');

add_route('/add_league', 'DashboardController@addLeague');
add_route('/save_league', 'DashboardController@executeAddLeague');

add_route('/league', 'LeagueController@atualizar');

add_route('/news', 'HomeController@news');
add_route('/send_commentary', 'HomeController@commentary');
add_route('/like', 'HomeController@like');

add_route('/404', 'ErrorController@error404');
add_route('/unauthorized', 'ErrorController@unauthorized');

route();
