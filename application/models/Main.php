<?php

namespace application\models;

use application\core\Model;
use application\lib\Curl as cUrl;
use DateTime;
use DateInterval;

class Main extends Model
{

    function getAllData()
    {
        if (!file_exists("uslugiAll.json")) {
            $url = "https://uslugi.gospmr.org/?option=com_uslugi&view=gosuslugi&task=getUslugi";
            $opts = ["CURLOPT_SSL_VERIFYHOST" => false];
            $curl = new cUrl($url);
            $curl->prepare($url, [], $opts);
            $curl->exec_post();
            $res = $curl->get_response();
            file_put_contents("uslugiAll.json", $res);
            $res = json_decode($res, true);
        } else {
            $res = json_decode(file_get_contents("uslugiAll.json"), true);
        }

        return $res["ulist"];
    }

    function getCurrent($id)
    {

        $url = "https://uslugi.gospmr.org/?option=com_uslugi&view=usluga&task=getUsluga&uslugaId={$id}";
        $opts = ["CURLOPT_SSL_VERIFYHOST" => false];
        $curl = new cUrl($url);
        $curl->prepare($url, [], $opts);
        $curl->exec_post();
        $res = $curl->get_response();
        $res = json_decode($res, true);

        return $res;
    }

    function setCurrent($data){
        debug($data);
    }
}

?>