<?php

namespace application\controllers;

use application\core\Controller;
use application\core\View;
use application\lib\Curl as cUrl;

class MainController extends Controller {
    
    public function indexAction() {
        if($this->isAjax()){
            //todo
            View::errorCode(403,'');
        }
        else{
            //todo curl get data
            $url = "https://uslugi.gospmr.org/?option=com_uslugi&view=gosuslugi&task=getUslugi";
            $opts = ["CURLOPT_SSL_VERIFYHOST"=>false];
            $curl = new cUrl($url);
            $curl->prepare($url,[],$opts);
            $curl->exec_post();
            var_dump (count_chars($curl->get_response())); die();
        }

    }


}

/**
 * 
    //var_dump(file_get_contents("https://uslugi.gospmr.org/?option=com_uslugi&view=gosuslugi&task=getUslugi",false));

    $time = microtime();
    if(! file_exists("uslugiAll.json") ){
    ob_start();
    get_web_page("https://uslugi.gospmr.org/?option=com_uslugi&view=gosuslugi&task=getUslugi");
    $flsData = ob_get_clean();
    $glbData = json_decode($flsData,true);

    file_put_contents("uslugiAll.json",$flsData);
    } else {
    $glbData = json_decode(file_get_contents("uslugiAll.json"),true);
    }
    //var_dump(gettype($flsData),$glbData["ulist"]);die();

    $tmpData = array();

    foreach($glbData["ulist"] as $v){
    if($v["has_electronic_view"] == 1){
        //var_dump($tempData["organization"],$tempData["description"]["payment"],$tempData["description"]["state_duty_payment"],"<hr />"); die();
        $tmpData[] = array(
            "id"=> $v["id"],
            "name" => $v["name"],
            "has_electronic_view"=>$v["has_electronic_view"],
            
        );
        //return;
    }
    }

    for ($i = 0; $i< 13; $i++){//$tmpData as $key => $value) {
    $id = $tmpData[$i]["id"];
    ob_start();

    get_web_page("https://uslugi.gospmr.org/?option=com_uslugi&view=usluga&task=getUsluga&uslugaId={$id}");

    $tempData = json_decode(ob_get_clean(),true);

    $tmpData[$i]["organization"]=$tempData["organization"];
    $tmpData[$i]["payment"]=json_encode($tempData["description"]["payment"]);
    $tmpData[$i]["state_duty_payment"]=json_encode($tempData["description"]["state_duty_payment"]);
    //var_dump($tmpData[$i],$tempData,"<hr />");
    }
    //var_dump(json_encode($tmpData));//die();
    var_dump("<hr />",[(microtime()-$time)]);

    //die();
 */

?>