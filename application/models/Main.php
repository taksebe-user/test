<?php

namespace application\models;

use application\core\Model;
use application\lib\Curl as cUrl;
use DateTime;
use DateInterval;

class Main extends Model
{

    function getAllData()
    {   //if possible add logic for check difference of online and offline datas.
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
            file_put_contents(
                "files/{$id}.json",
                json_encode($tmpDoc, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT)
            );
            $curl = null;
        } else {
            $res = json_decode(file_get_contents("files/{$id}.json"), true);
        }
        return $res;
    }

    function setCurrent($data)
    {
        debug($this->getTaxID(array(0, 0, 0, 0, 0, 0, 5, 0)));
    }
    private function getTaxID($data)
    {
        $params = array(
            "tir" => [$data[0], "i"],
            "ben" => [$data[1], "i"],
            "ryb" => [$data[2], "i"],
            "kam" => [$data[3], "i"],
            "dub" => [$data[4], "i"],
            "gri" => [$data[5], "i"],
            "slo" => [$data[6], "i"],
            "dne" => [$data[7], "i"],
        );

        $ID = $this->db->column(
            "
            select `ID` from `tax_codes`
            where 
                `tax_code_tir` = :tir
                and `tax_code_ben` = :ben
                and `tax_code_ryb` = :ryb
                and `tax_code_kam` = :kam
                and `tax_code_dub` = :dub
                and `tax_code_gri` = :gri
                and `tax_code_slo` = :slo
                and `tax_code_dne` = :dne
        ", $params );

        if($ID === false){
            $ID = $this->db->query(" insert into `tax_codes` (
                    `tax_code_tir`, `tax_code_ben`, `tax_code_ryb`, 
                    `tax_code_kam`, `tax_code_dub`, `tax_code_gri`, 
                    `tax_code_slo`, `tax_code_dne`) 
                values (:tir, :ben, :ryb, :kam, :dub, :gri, :slo, :dne) ", $params
            );
        }

        return $ID;
    }

    private function getAccountNumID($data)
    {
        $params = array(
            "tir" => [$data[0], "i"],
            "ben" => [$data[1], "i"],
            "ryb" => [$data[2], "i"],
            "kam" => [$data[3], "i"],
            "dub" => [$data[4], "i"],
            "gri" => [$data[5], "i"],
            "slo" => [$data[6], "i"],
            "dne" => [$data[7], "i"],
        );
    

        $ID = $this->db->column(
            "
            select `ID` from `account_numbers`
            where 
                `number` = :tir
                and `number_ben` = :ben
                and `number_ryb` = :ryb
                and `number_kam` = :kam
                and `number_dub` = :dub
                and `number_gri` = :gri
                and `number_slo` = :slo
                and `number_dne` = :dne
        ", $params );

        if($ID === false){
            $ID = $this->db->query(
                "insert into `account_numbers` (
                `number`, `number_ben`, `number_ryb`, `number_kam`, `number_dub`, `number_gri`, `number_slo`, `number_dne`
                ) values ( :tir, :ben, :ryb, :kam, :dub, :gri, :slo, :dne )", $params
            );
        }

        return $ID;
    }

    private function getSDP_ID($data)
    {
        $params = array(
            "num" => [$data[0], "i"],
            "tax" => [$data[1], "i"],
            "num_ids" => [$data[2], "i"],
            "article" => [$data[3], "i"],
            "incomes" => [$data[4], "s"],
        );

        $ID = $this->db->column(
            "
            select `ID` from `SDP`
            where 
                    `account_number` = :num
                and `tax_code_id` = :tax
                and `account_numbers_id` = :num_ids
                and `article` = :article
                and `incomes_classification` = :incomes
        ", $params);

        if($ID === false){
            $ID = $this->db->query(
                "insert into SDP (
                    `account_number`,
                    `tax_code_id`,
                    `account_numbers_id`,
                    `article`,
                    `incomes_classification`
                ) values ( :num, :tax, :num_ids, :article, :incomes )", $params
            );
        }

        return $ID;
    }
}
