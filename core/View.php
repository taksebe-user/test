<?php

namespace application\core;

class View
{

    public $path;
    public $route;
    public $layout = "default";

    public function __construct($route)
    {
        $this->route = $route;
        $this->path = "{$route['controller']}/{$route['action']}";
    }

    public function render($title, $vars = [])
    {
        extract($vars);
        //debug([$this->path,$vars]);
        if (file_exists("application/views/{$this->path}.php")) {
            ob_start();
            require "application/views/{$this->path}.php";
            $content = ob_get_clean();
            require "application/views/layouts/{$this->layout}.php";
        } else {
            echo "View not found";
        }
    }

    public static function errorCode($code, $text)
    {
        http_response_code($code);
        $path = (file_exists("application/views/errors/{$code}.php"))
            ? "application/views/errors/{$code}.php" : "";
        if (strlen($path) > 0) {
            ob_start();
            require $path;
            $content = ob_get_clean();
            $title = sprintf("%d: %s", $code, $text);
            require "application/views/layouts/errors/default.php";
            //debug([$code, $path, strlen($path), $content, $title]);
        }
        //exit;
    }

    public static function getStaticFileHistoryDate($file)
    {
        $file_local = "{$_SERVER["DOCUMENT_ROOT"]}$file";
        $extention = pathinfo($file, PATHINFO_EXTENSION);
        if (file_exists($file_local)) {
            return sprintf("%s?_=%s", $file, filemtime($file_local));
        } else {
            return "/public/$extention/not_found.$extention";
        }
    }

    public function redirect($url)
    {
        header(sprintf("Location: %s", $url));
        exit;
    }

    public function getConnectedLibs($arrFiles)
    {
        //debug([array_merge_recursive($arrFiles,$this->getFilesToConnect($this->route))]);
        $outputFile = "";
        #region connect files by default layout
        foreach (array_merge_recursive($arrFiles, $this->getFilesToConnect($this->route)) as $ext => $files) {
            //debug([$ext,$files]);
            foreach ($files as $file) {
                $file = trim($file);
                if (!(substr($file, 0, 4) === "http")) {
                    // connect file
                    $fileParam = $this->getFileHistoryDate($file);
                    switch ($ext) {
                        case 'css':
                            $outputFile .= "<link rel='stylesheet' href='{$fileParam}'>";
                            break;
                        case 'js':
                            $outputFile .= "<script type='text/javascript' defer async src='{$fileParam}'></script>";
                            break;
                        default:
                            break;
                    }
                } else {
                    // connect url
                    $outputFile .= ($ext === "js") ? "<script type='text/javascript' async src='{$file}'></script>" :
                                    (($ext === "css") ? "<link rel='stylesheet' href='{$file}'>" : "");
                }
            }
        }
        #endregion
        //debug($outputFile);
        return $outputFile;
    }

    private function getFileHistoryDate($file)
    {
        $file_local = "{$_SERVER["DOCUMENT_ROOT"]}$file";
        $extention = pathinfo($file, PATHINFO_EXTENSION);
        if (file_exists($file_local)) {
            return sprintf("%s?_=%s", $file, filemtime($file_local));
        } else {
            return "/public/$extention/not_found.$extention";
        }
    }

    private function getFilesToConnect($route)
    {
        $rootDir = $_SERVER["DOCUMENT_ROOT"];
        $files = array();
        $cssDir = "$rootDir/public/css/{$route["controller"]}/{$route["action"]}";
        $jsDir = "$rootDir/public/js/{$route["controller"]}/{$route["action"]}";

        foreach (array($cssDir, $jsDir) as $value) {
            if ($value === $jsDir) {
                $ext = "js";
            } elseif ($value === $cssDir) {
                $ext = "css";
            }
            //debug($ext);
            if (file_exists($value)) {
                foreach (array_diff(scandir($value, SCANDIR_SORT_DESCENDING), array('..', '.', "urls.txt")) as $v) {
                    $files[$ext][] = str_replace($rootDir, "", "{$value}/{$v}");
                }
            }

            if (file_exists("$value/urls.txt")) {
                $fileUrls = file_get_contents("$value/urls.txt");
                $urls = explode(PHP_EOL, $fileUrls);
                foreach ($urls as $val) {
                    $files[$ext][] = $val;
                }
            }
        }

        return $files;
    }
}


