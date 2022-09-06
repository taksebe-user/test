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
        $params = array(
            "id" => [$data["id"], "i"],
            "name" => [$data["name"], "s"],
            "org" => [$data["organization"], "s"],
            "payment" => [$this->getPaymentID($data["payment"]), "i"],
            "sdp" => [$this->getSDP_ID($data["state_duty_payment"]), "i"],
            "has_electronic_view" => [$data["has_electronic_view"], "i"],
        );
        //debug($params);
        try {
            $ID = $this->db->column("
                    SELECT `id_record` 
                    FROM `usluga` 
                    WHERE `id` = :id",
                array("id" => $params["id"])
            );

            if ($ID === false) {
                $this->db->query("
                    INSERT INTO `usluga` (`id`, `name`, `organization`, 
                        `paymant_id`, `state_duty_payment_id`, `has_electronic_view`
                    ) 
                    VALUES (
                        :id, :name, :org, :payment, :sdp,
                        :has_electronic_view
                    ) ",
                    $params
                );
            } else {
                $params["id_r"] = array($ID,"i");

                $this->db->query("
                    UPDATE `usluga` 
                    SET `id`=:id, 
                        `name`=:name, 
                        `organization`=:org, 
                        `paymant_id`=:payment, 
                        `state_duty_payment_id`=:sdp, 
                        `has_electronic_view`=:has_electronic_view
                    WHERE `id_record` = :id_r
                         ",
                    $params
                );
            }
        } catch (\Throwable $e) {
            debug($data);
        }
    }

    function getAllFromDB()
    {
        return $this->db->row("
            select `id`, `name`, `organization`, `has_electronic_view` 
            from `usluga`
            order by `organization`,`name`
        ");
    }


    private function getTaxID($data)
    {
        $params = array(
            "tir" => [$data["tax_code_tir"], "i"],
            "ben" => [$data["tax_code_ben"], "i"],
            "ryb" => [$data["tax_code_ryb"], "i"],
            "kam" => [$data["tax_code_kam"], "i"],
            "dub" => [$data["tax_code_dub"], "i"],
            "gri" => [$data["tax_code_gri"], "i"],
            "slo" => [$data["tax_code_slo"], "i"],
            "dne" => [$data["tax_code_dne"], "i"],
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
        ",
            $params
        );

        if ($ID === false) {
            $ID = $this->db->query(
                " insert into `tax_codes` (
                    `tax_code_tir`, `tax_code_ben`, `tax_code_ryb`, 
                    `tax_code_kam`, `tax_code_dub`, `tax_code_gri`, 
                    `tax_code_slo`, `tax_code_dne`) 
                values (:tir, :ben, :ryb, :kam, :dub, :gri, :slo, :dne) ",
                $params
            );
        }

        return $ID;
    }

    private function getAccountNumID($data)
    {
        $params = array(
            "tir" => [$data["account_number"], "i"],
            "ben" => [$data["account_number_ben"], "i"],
            "ryb" => [$data["account_number_ryb"], "i"],
            "kam" => [$data["account_number_kam"], "i"],
            "dub" => [$data["account_number_dub"], "i"],
            "gri" => [$data["account_number_gri"], "i"],
            "slo" => [$data["account_number_slo"], "i"],
            "dne" => [$data["account_number_dne"], "i"],
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
        ",
            $params
        );

        if ($ID === false) {
            $ID = $this->db->query(
                "insert into `account_numbers` (
                `number`, `number_ben`, `number_ryb`, `number_kam`, `number_dub`, `number_gri`, `number_slo`, `number_dne`
                ) values ( :tir, :ben, :ryb, :kam, :dub, :gri, :slo, :dne )",
                $params
            );
        }

        return $ID;
    }

    private function getSDP_ID($data)
    {
        $tax_id = $this->getTaxID($data["tax_codes"]);
        $acc_num_id = $this->getAccountNumID($data["account_numbers"]);
        $params = array(
            "num" => [$data["account_number"], "i"],
            "tax" => [$tax_id, "i"],
            "num_ids" => [$acc_num_id, "i"],
            "article" => [$data["article"], "i"],
            "incomes" => [$data["incomes_classification"], "s"],
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
        ",
            $params
        );

        if ($ID === false) {
            $ID = $this->db->query(
                "insert into SDP (
                    `account_number`,
                    `tax_code_id`,
                    `account_numbers_id`,
                    `article`,
                    `incomes_classification`
                ) values ( :num, :tax, :num_ids, :article, :incomes )",
                $params
            );
        }

        return $ID;
    }

    private function getPaymentID($data)
    {
        $params = array(
            "type" => [$data["payment_type"], "s"],
            "amount" => [$data["payment_amount"], "s"],
            "a_urgent" => [$data["payment_amount_urgent"], "s"],
            "a_coins" => [$data["payment_amount_coins"], "i"],
            "a_urgent_coins" => [$data["payment_amount_urgent_coins"], "i"],
            "a_ul" => [$data["payment_amount_ul"], "s"],
            "a_urgent_ul" => [$data["payment_amount_urgent_ul"], "s"],
            "a_coins_ul" => [$data["payment_amount_coins_ul"], "i"],
            "a_urgent_coins_ul" => [$data["payment_amount_urgent_coins_ul"], "i"],
            "method" => [$data["payment_method"], "s"],
        );

        $ID = $this->db->column(
            "
            select `ID` from `payment`
            where 
                    `type` = :type
                and `amount` = :amount
                and `amount_urgent` = :a_urgent
                and `amount_coins` = :a_coins
                and `amount_urgent_coins` = :a_urgent_coins
                and `amount_ul` = :a_ul
                and `amount_urgent_ul` = :a_urgent_ul
                and `amount_coins_ul` = :a_coins_ul
                and `amount_urgent_coins_ul` = :a_urgent_coins_ul
                and `method` = :method
        ",
            $params
        );

        if ($ID === false) {
            $ID = $this->db->query(
                " insert into `payment` (
                    `type`, `amount`, `amount_urgent`, `amount_coins`
                    , `amount_urgent_coins`, `amount_ul`, `amount_urgent_ul`
                    , `amount_coins_ul`, `amount_urgent_coins_ul`, `method`
                ) 
                values (:type, :amount, :a_urgent, :a_coins, 
                    :a_urgent_coins, :a_ul, :a_urgent_ul, :a_coins_ul, 
                    :a_urgent_coins_ul, :method
                ) ",
                $params
            );
        }

        return $ID;
    }
}
