<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/public/images/favicon.ico" type="image/x-icon">
    <?php

        use FFI\Exception;
        use application\core\View;

        try{
            $arrFiles = array(
                "/public/css/Site.css",
                "/public/css/bootstrap.css",
                "/public/css/styleDisp.css",
                "/public/css/styleGrid.css",
                "/public/css/styleIndex.css",
                "/public/css/styleIndexPart.css",
            );
            $outputFile = "";
            foreach ($arrFiles as $file){
                $fileParam = View::getStaticFileHistoryDate($file);
                $extention = explode(".",$file)[1];
                switch ($extention) {
                    case 'css': $outputFile .= "<link rel='stylesheet' href='{$fileParam}'>"; break;
                    case 'js': $outputFile .= "<script src='{$fileParam}'></script>"; break;
                    default: break;
                }
            }
            echo $outputFile;
        } catch( \Exception $e ){}
    ?>
    <title><?= $title; ?></title>
</head>
<body>
    <?php
        $buttons = require "application/config/menu.php";
        $contentButton = "<ul class='topnav'>";
        foreach($buttons as $key => $val){
            $right = (isset($val["right"]))?" class='right'":"";
            $class = (isset($val["class"]))?$val["class"]:"";
            $id = (isset($val["id"]))?" id='{$val['id']}'":"";
            if(isset($val["icon"])){
                $contentButton .= "<li$right>
                    <a style='padding: 8px 16px;' href='/'>
                        <img src='/public/images/{$val["icon"]["name"]}' width='{$val["icon"]["wh"]}' height='{$val["icon"]["ht"]}' alt='{$val["icon"]["alt"]}'>
                    </a>
                </li>"; 
            } else if(isset($val["condition"]) and $val["condition"] === true){
                $contentButton .= "<li$right><a class='active_class slider-container{$class}' href='{$key}' title='{$val['title-alt']}'>
                        <span class='slider'>
                            <span class='glyphicon glyphicon-{$val['ico']}'>
                                <span class='text'{$id}>{$val['title']}</span>
                            </span>
                        </span>
                    </a>
                </li>";
            }
        }
        $contentButton .= "</ul>";
        echo $contentButton;
    ?>
    <?= $content; ?>
</body>
</html>