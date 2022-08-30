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
            $curl = null;
        } else {
            $res = json_decode(file_get_contents("uslugiAll.json"), true);
        }

        return $res["ulist"];
    }

    function getCurrent($id)
    {
        if (!file_exists("files/{$id}.json")) {
            $url = "https://uslugi.gospmr.org/?option=com_uslugi&view=usluga&task=getUsluga&uslugaId={$id}";
            $opts = ["CURLOPT_SSL_VERIFYHOST" => false];
            $curl = new cUrl($url);
            $curl->prepare($url, [], $opts);
            $curl->exec_post();
            $res = $curl->get_response();
            $res = json_decode($res, true);
            $tmpDoc = [
                "id" => $res["id"],
                "name" => $res["name"],
                "organization" => $res["organization"],
                "payment" => $res["description"]["payment"],
                "state_duty_payment" => $res["description"]["state_duty_payment"],
                "has_electronic_view" => $res["description"]["has_electronic_view"],
            ];
            file_put_contents("files/{$id}.json"
                , json_encode($tmpDoc,JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES|JSON_FORCE_OBJECT)
            );
            $curl = null;
        } else {
            $res = json_decode(file_get_contents("files/{$id}.json"), true);
        }
        return $res;
    }

    function setCurrent($data)
    {
        debug($data);
    }
}
