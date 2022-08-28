<?php

namespace application\core;

use application\core\View;

abstract class Controller
{

    public $route;
    public $view;
    public $modelType = "db";
    public $acl;

    public function __construct($route)
    {
        $this->route = $route;
        $this->view = new View($route);

        if (!$this->checkACL() and isset($_SESSION["user"])) {
            View::errorCode(403, "Запрещено");
            die();
        }


        $this->model = $this->loadModel(
            $route["controller"],
            $this->modelType
        );
        
    }

    public function loadModel($name, $type)
    {
        $model = sprintf("application\models\%s", ucfirst($name));
        if (class_exists($model)) {
            return new $model($type);
        }
    }

    public function setModelType($name, $type)
    {
        $this->model = $this->loadModel($name, $type);
    }

    public function checkACL()
    {
        $this->acl = require "application/acl/{$this->route['controller']}.php";
        //debug($this->acl);
        if ($this->isACL("guest")) {
            // debug([__LINE__,$_SESSION]); 
            return true;
        }
        if ($this->isACL("admin") and isset($_SESSION["user"]['admin'])) {
            // debug([__LINE__,$_SESSION]); 
            return true;
        }
        if ($this->isACL("dev") and isset($_SESSION["user"]['dev'])) {
            // debug([__LINE__,$_SESSION]);
            return true;
        }
        if (
            $this->isACL("user")
            and isset($_SESSION['user'])
            and !(isset($_SESSION["user"]['dev'])
                or isset($_SESSION["user"]['admin']))
        ) {
            //debug([__LINE__,$_SESSION]); 
            return true;
        }

        return false;
    }

    public function isACL($key)
    {
        return in_array($this->route["action"], $this->acl[$key]);
    }

    protected function isAjax()
    {
        //debug([__DIR__,__NAMESPACE__,$_SERVER]);
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function getFileHistoryDate($file)
    {
        $file = "{$_SERVER["DOCUMENT_ROOT"]}$file";
        $extention = pathinfo($file, PATHINFO_EXTENSION);
        if (file_exists($file)) {
            return sprintf("%s?_=%i", $file, filemtime($file));
        } else {
            return "/public/$extention/not_found.$extention";
        }
    }
}
