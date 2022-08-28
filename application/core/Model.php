<?php

namespace application\core;

use application\lib\Db;

abstract class Model {
    protected $db;
    
    public function __construct($type="db"){
        $this->db = new DB($type);
    }
}

?>