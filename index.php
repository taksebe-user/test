<?php

require_once "{$_SERVER["DOCUMENT_ROOT"]}/application/lib/Dev.php";

use application\core\Router;

spl_autoload_register(function($class) {
    
    $path = str_replace("\\","/","{$class}.php");
    
    if(file_exists($path)){ include $path; }
});


session_start();

$router = new Router;

$router->run();

?>
