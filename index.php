<?php

require_once 'src/autoloader.php';
require_once 'src/router.php';
require_once 'src/database.php';
require_once 'src/session.php';
require_once 'src/utils.php';


date_default_timezone_set('America/Sao_Paulo');
$session = new Session();

add_route('GET', '/', 'HomeController@index');

add_route('GET', '/auth', function () {
    add_route('GET', '/login', 'AuthController@loginPage');
    add_route('POST', '/login', 'AuthController@loginAction');

    add_route('GET', '/register', 'AuthController@registerPage');
    add_route('POST', '/register', 'AuthController@registerAction');

    add_route('GET', '/logout', 'AuthController@logout');

    add_route('GET', '/forgot', 'AuthController@forgotPage');
    add_route('POST', '/forgot', 'AuthController@forgotAction');
});

add_route('GET', '/leagues', function () {
    add_route('GET', '/dashboard', 'LeagueController@dashboard');
    add_route('GET', '/add', 'LeagueController@add');
    add_route('POST', '/save', 'LeagueController@save');
    add_route('DELETE', '/delete', 'LeagueController@delete');
    add_route('GET', '/show/tabela', 'LeagueController@getClassification');
    add_route('GET', '/show/jogos', 'LeagueController@getMatches');
    add_route('POST', '/update', 'LeagueController@update');
});

// News Routes
add_route('GET', '/news', function () {
    add_route('GET', '/all', 'NewsController@all');
    add_route('GET', '/index', 'NewsController@index');
    add_route('GET', '/form', 'NewsController@form');
    add_route('POST', '/add', 'NewsController@create');
    add_route('GET', '/delete', 'NewsController@delete');
    add_route('GET', '/dashboard', 'NewsController@dashboard');
    add_route('POST', '/like', 'NewsController@like');
    add_route('POST', '/comment', 'NewsController@comment');
    add_route('GET', '/show', 'NewsController@show');
    add_route('POST', '/upload', 'NewsController@uploadImage');
    add_route('GET', '/video', 'NewsController@videoForm');
    add_route('POST', '/video', 'NewsController@videoUpload');
    add_route('GET', '/videos', 'NewsController@videoIndex');
    add_route('GET', '/dashboardVideos', 'NewsController@videoDashboard');
    add_route('GET', '/videoDelete', 'NewsController@deleteVideo');
});

// Automation Purposes
add_route('GET', '/bot', function () {
    add_route('POST', '/saveMatch', 'BotController@saveLeague');
    add_route('POST', '/saveTeam', 'BotController@saveTeam');
    add_route('POST', '/teams', 'BotController@saveTeams');
    add_route('POST', '/matches', 'BotController@saveMatch');
});

// Mobile
add_route('GET', '/mobile', function () {
    add_route('POST', '/auth', function () {
        add_route('POST', '/login', 'MobileController@login');
        add_route('POST', '/register', 'MobileController@register');
    });

    add_route('POST', '/save', 'MobileController@saveContent');

    add_route('GET', '/news', function () {
        add_route('GET', '/get', 'MobileController@getContents');
        add_route('GET', '/getAll', 'MobileController@getAllContents');
        add_route('GET', '/getTags', 'MobileController@getPostTags');
        add_route('GET', '/getComments', 'MobileController@getComments');
    });

    add_route('GET', '/videos', function () {
        add_route('GET', '/getAll', 'MobileController@getVideos');
    });

    add_route('GET', '/leagues', function () {
        add_route('GET', '/getLeagues', 'MobileController@getLeagues');
        add_route('GET', '/getRounds', 'MobileController@getRounds');
    });
});

add_route('GET', '/error', function () {
    add_route('GET', '/404', 'ErrorController@error404');
    add_route('GET', '/unauthorized', 'ErrorController@unauthorized');
});

route();
