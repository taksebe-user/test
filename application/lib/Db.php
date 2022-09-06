<?php

namespace application\lib;

use PDO;
use \Exception;

class Db /*  extends PDO */ {

    protected $db;

    public function __construct($type){
        $conf = require "application/config/db.php";
        $conf = $conf[$type];
        $connString = "mysql:host={$conf["host"]};dbname={$conf["database"]};";
        try{
            $this->db = new PDO($connString
                            , $conf["user"]
                            , $conf["password"]
                            , array(
                                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            ));
        } catch (Exception $e){
            //if(isset($_SESSION["user"]["dev"]) or isset($_COOK1IE["DEV"]) and $_COOKIE["DEV"]=="DEV") 
            debug($e);
            die("Произошла ошибка подключения к базам данных");
        }
    }

    public function query($sql,$params=[]){
        $stmt = $this->db->prepare($sql);
        if(!empty($params)){
            foreach ($params as $key => $val) {
                //$val[0] -> value
                //$val[1] -> char of type
                $type = null;
                switch ($val[1]) {
                    case 'i':
                        $type = PDO::PARAM_INT; break;
                    case 's':
                        $type = PDO::PARAM_STR; break;
                    case 'b':
                        $type = PDO::PARAM_BOOL; break;
                    case 'n':
                        $type = PDO::PARAM_NULL; break;
                }
                
                $stmt->bindValue(":{$key}", $val[0], $type);
            }
        }
        $stmt->execute();
        $res = (stripos($sql, "insert into")===false)?$stmt:$this->db->lastInsertId();
        return $res;
    }

    public function row($sql,$params=[]){
        $result = $this->query($sql, $params);
        if(! $result === false ){
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function column($sql,$params=[]){
        $result = $this->query($sql, $params);
        if(! $result === false ){
            return $result->fetchColumn();
        }
    }

}

?>