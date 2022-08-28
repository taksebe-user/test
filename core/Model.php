<?php

namespace application\core;

use application\lib\Db;

abstract class Model {
    protected $db;
    
    public function __construct($type="disp"){
        $this->db = new DB($type);
    }
}

?>