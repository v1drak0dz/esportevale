<?php
// Armazena as rotas
$GLOBALS['routes'] = array(); // Em vez de variável global, usa $GLOBALS direto para maior compatibilidade

// Função para adicionar rotas
function add_route($uri, $controllerAction) {
    // Usa $GLOBALS para acessar a variável global
    $routes = &$GLOBALS['routes'];

    // Separa o controller e a função
    $parts = explode('@', $controllerAction);
    $controller = $parts[0];
    $action = isset($parts[1]) ? $parts[1] : 'index'; // fallback se a ação não for definida

    // Adiciona a rota
    $routes[$uri] = array(
        'controller' => $controller,
        'action' => $action
    );
}

// Função que executa a rota
function route() {
    $routes = &$GLOBALS['routes'];

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);

    // Remove o caminho do script da URI (se estiver em subpasta)
    if (strpos($uri, $scriptName) === 0) {
        $uri = substr($uri, strlen($scriptName));
    }

    // Garante que sempre tenha uma barra inicial
    $uri = '/' . ltrim($uri, '/');

    if (isset($routes[$uri])) {
        $controller = $routes[$uri]['controller'];
        $action = $routes[$uri]['action'];

        if (!class_exists($controller)) {
            die("Controlador '$controller' não encontrado.");
        }

        $controllerClass = new $controller();

        if (method_exists($controllerClass, $action)) {
            call_user_func(array($controllerClass, $action));
        } else {
            echo "Ação '$action' não encontrada no controlador '$controller'.";
        }
    } else {
        header('Location: /404');
        exit();
    }
}
?>
