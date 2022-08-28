<?php

namespace application\core;

class Router{

    protected $routes = [];
    protected $params = [];

    public function __construct() {
        $arr = require_once "application/config/routes.php";
        //debug($arr);
        foreach($arr as $k => $v){
            $this->add($k,$v);
        }
        //debug($this->routes);
    } 

    public function add($route, $params) {
        $route = preg_replace('/{([a-z]+):([^\}]+)}/', '(?P<\1>\2)', $route);
        $route = '#^'.$route.'$#';
        $this->routes[$route] = $params;
    }

    public function match(){
        $url = $_SERVER["REQUEST_URI"];

        //debug([$this->routes,$url]);
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        if (is_numeric($match)) {
                            $match = (int) $match;
                        }
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function run(){

        if($this->match()){
            $path = sprintf("application\controllers\%sController"
                                ,ucfirst($this->params["controller"]));
            if(class_exists($path)){
                $action = $this->params["action"]."Action";
                if(method_exists($path,$action)){
                    $controller = new $path($this->params);
                    $controller->$action();
                }
                else { View::errorCode(404,"не найден метод {$this->params["action"]} в {$this->params['controller']}"); }
            }
            else { View::errorCode(404,"не найден класс {$this->params['controller']}"); }
        }
        else { View::errorCode(404,"не найден путь"); }
    }

}

?>