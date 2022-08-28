<?php

use application\controllers\AccountController;

return [
    //MainController.php
    "/"=>["controller"=>"main","action"=>"index"],

    //dont forget for regex route path
    // "/schema/kot_{geo:\w+}_{num:\d+}"=>["controller"=>"pages","action"=>"schema"],

    //"/news/show"=>["controller"=>"news","action"=>"show"],
];

?>