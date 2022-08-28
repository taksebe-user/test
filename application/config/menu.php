<?php
//debug([$this,get_defined_vars()]);
return [
    "/"=>[
        "icon"=>[
            "name"=>"TTE_logo.png",
            "wh"=>119,
            "ht"=>32,
            "alt"=>"ТТЭ",
        ],
        "condition"=>true,
    ],
    "/archive"=>[ #<!---магистраль, внутренние и распеределительные--->
        "title"=>"Архив",
        "title-alt"=>"Архив",
        "ico"=>"calendar",
        "condition"=>(isset($_SESSION["user"])),
    ],
    "/statistic"=>[ #<!---магистраль, внутренние и распеределительные--->
        "title"=>"Статистика",
        "title-alt"=>"Статистика",
        "ico"=>"align-center",
        "condition"=>(isset($_SESSION["user"])),
    ],
    "/map"=>[ #<!---магистраль, внутренние и распеределительные--->
        "title"=>"Карта",
        "title-alt"=>"Карта",
        "ico"=>"map-marker",
        "condition"=>(isset($_SESSION["user"])),
    ],
    "/repair"=>[
        "title"=>"Ремонты",
        "title-alt"=>"Ремонты",
        "ico"=>"wrench",
        "condition"=>false,//(isset($_SESSION["user"])),
    ],
    "/directory"=>[
        "title"=>"Справочник",
        "title-alt"=>"Справочник",
        "ico"=>"book",
        "condition"=>false,//(isset($_SESSION["user"])),
    ],
    "/account/logout"=>[
        "title"=>"ВЫЙТИ",
        "title-alt"=>"ВЫЙТИ",
        "ico"=>"export",
        "right"=>1,
        "condition"=>(isset($_SESSION["user"])),
    ],
    "/account/login"=>[
        "title"=>"ВХОД",
        "title-alt"=>"ВХОД",
        "ico"=>"import",
        "right"=>1,
        "condition"=>(!isset($_SESSION["user"])),
    ],
    "#"=>[
        "id"=>"tempOutput", #id as params of temp on steamShop
        "title"=>"##,## °C",
        "title-alt"=>"Температура наружного воздуха",
        "ico"=>"picture",
        "right"=>1,
        "condition"=>(isset($needTemp) and $needTemp==1),
    ],
    "/admin"=>[
        "title"=>"Администрирование",
        "title-alt"=>"Администрирование",
        "ico"=>"cog",
        "right"=>1,
        "class"=>" admin-red",
        "condition"=>(isset($_SESSION["user"]["dev"]) or isset($_SESSION["user"]["admin"])),
    ],
];

?>