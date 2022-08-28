<?php

use application\controllers\AccountController;

return [
    //MainController.php
    "/"=>["controller"=>"main","action"=>"index"],
    "/alarm/{alarm:\d+}"=>["controller"=>"main","action"=>"alarm"],
    "/archive"=>["controller"=>"main","action"=>"archive"],
    "/statistic"=>["controller"=>"main","action"=>"statistic"],
    "/map"=>["controller"=>"main","action"=>"map"],
    "/contact"=>["controller"=>"main","action"=>"contact"],
    //AccountController.php
    "/account/login"=>["controller"=>"account","action"=>"login"],
    "/account/reg"=>["controller"=>"account","action"=>"reg"],
    "/account/logout"=>["controller"=>"account","action"=>"logout"],
    "/account/genpasswd"=>["controller"=>"account","action"=>"genpasswd"],
    //PagesController.php
    "/schema/kot_{geo:\w+}_{num:\d+}"=>["controller"=>"pages","action"=>"schema"],

    //"/news/show"=>["controller"=>"news","action"=>"show"],
];

?>