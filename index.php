<?php

require_once('src/autoloader.php');
require_once('src/router.php');
require_once('src/database.php');
require_once('src/session.php');
require_once('src/utils.php');

date_default_timezone_set('America/Sao_Paulo');
$session = new Session();

add_route('GET', '/', 'HomeController@index');

add_route('GET', '/auth', function() {
    add_route('GET', '/login', 'AuthController@loginPage');
    add_route('POST', '/login', 'AuthController@loginAction');
    
    add_route('GET', '/register', 'AuthController@registerPage');
    add_route('POST', '/register', 'AuthController@registerAction');
    
    add_route('GET', '/logout', 'AuthController@logout');
    
    add_route('GET', '/forgot', 'AuthController@forgotPage');
    add_route('POST', '/forgot', 'AuthController@forgotAction');
});

add_route('GET', '/leagues', function() {
    add_route('GET', '/dashboard', 'LeagueController@dashboard', true);
    add_route('GET', '/add', 'LeagueController@add', true);
    add_route('POST', '/save', 'LeagueController@save', true);
    add_route('DELETE', '/delete', 'LeagueController@delete', true);
    add_route('GET', '/show/tabela', 'LeagueController@show');
    add_route('GET', '/show/jogos', 'LeagueController@show');
});

// News Routes
add_route('GET', '/news', function() {
    add_route('GET', '/index', 'NewsController@index');
    add_route('GET', '/form', 'NewsController@form', true);
    add_route('POST', '/add', 'NewsController@create', true);
    add_route('DELETE', '/delete', 'NewsController@delete', true);
    add_route('GET', '/dashboard', 'NewsController@dashboard', true);
    add_route('POST', '/like', 'NewsController@like');
    add_route('POST', '/comment', 'NewsController@comment');
    add_route('GET', '/show', 'NewsController@show');
});

// Automation Purposes
add_route('GET', '/bot', function() {
    add_route('POST', '/save', 'BotController@saveLeague');
});

// Mobile
add_route('GET', '/mobile', function() {
    add_route('POST', '/auth', function() {
        add_route('POST', '/login', 'MobileController@login');
        add_route('POST', '/register', 'MobileController@register');
    });

    add_route('POST', '/save', 'MobileController@saveContent');
    
    add_route('GET', '/news', function() {
        add_route('GET', '/get', 'MobileController@getContents');
        add_route('GET', '/getAll', 'MobileController@getAllContents');
        add_route('GET', '/getTags', 'MobileController@getPostTags');
    });

    add_route('GET', '/leagues', function() {
        add_route('GET', '/getLeagues', 'MobileController@getLeagues');
        add_route('GET', '/getRounds', 'MobileController@getRounds');
    });
});

add_route('GET', '/error', function() {
    add_route('GET', '/404', 'ErrorController@error404');
    add_route('GET', '/unauthorized', 'ErrorController@unauthorized');
});


route();
