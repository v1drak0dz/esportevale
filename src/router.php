<?php
// Armazena as rotas por método (GET, POST, etc.)
$GLOBALS['routes'] = array(
    'GET' => [],
    'POST' => [],
    'DELETE' => []
    // Adicione outros métodos se necessário
);
$GLOBALS['route_prefix'] = '';

// Adiciona rota ou grupo de rotas
function add_route($method, $uri, $controllerActionOrClosure, $authRequired = false) {
    $routes = &$GLOBALS['routes'];
    $prefix = rtrim($GLOBALS['route_prefix'], '/');
    $method = strtoupper($method); // Normaliza método

    // Se for função anônima, trata como grupo
    if (is_callable($controllerActionOrClosure)) {
        $previousPrefix = $GLOBALS['route_prefix'];
        $GLOBALS['route_prefix'] = $prefix . $uri;

        call_user_func($controllerActionOrClosure);

        $GLOBALS['route_prefix'] = $previousPrefix;
        return;
    }

    // Separa controller e action
    $parts = explode('@', $controllerActionOrClosure);
    $controller = $parts[0];
    $action = isset($parts[1]) ? $parts[1] : 'index';

    // Concatena prefixo com URI
    $fullUri = $prefix . $uri;
    $fullUri = '/' . ltrim($fullUri, '/'); // Garante barra no início

    // Registra a rota no método certo
    $routes[$method][$fullUri] = array(
        'controller' => $controller,
        'action' => $action,
        'auth' => $authRequired
    );
}

// Função para executar a rota baseada na URL atual
function route() {
    $routes = &$GLOBALS['routes'];

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $method = strtoupper($_SERVER['REQUEST_METHOD']);

    // Remove o prefixo do script, se necessário
    if (strpos($uri, $scriptName) === 0) {
        $uri = substr($uri, strlen($scriptName));
    }

    $uri = '/' . ltrim($uri, '/'); // Normaliza URI

    if (isset($routes[$method][$uri])) {
        $route = $routes[$method][$uri];

        // Verifica se precisa de autenticação
        if (!empty($route['auth'])) {
            $session = Session::getInstance();
            if (!$session->get('user_id')) {
                header('Location: /error/unauthorized');
                exit;
            }
        }

        $controller = $route['controller'];
        $action = $route['action'];

        if (!class_exists($controller)) {
            die("Controlador '$controller' não encontrado.");
        }

        $controllerInstance = new $controller();

        if (method_exists($controllerInstance, $action)) {
            call_user_func(array($controllerInstance, $action));
        } else {
            echo "Ação '$action' não encontrada no controlador '$controller'.";
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        header('Location: /error/404');
        exit;
    }
}
