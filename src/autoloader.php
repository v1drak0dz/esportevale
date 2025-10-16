<?php

function autoload($class) {
    $class = str_replace("\\", DIRECTORY_SEPARATOR, $class); // Compatível com pseudo-namespaces

    $folders = array('Controllers', 'Models');

    foreach ($folders as $folder) {
        $path = $folder . DIRECTORY_SEPARATOR . $class . '.php';

        if (file_exists($path)) {
            require_once $path;
            return; // para após carregar a primeira ocorrência
        }
    }
}

// Registra a função de autoload
spl_autoload_register('autoload');
