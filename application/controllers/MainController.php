<?php

namespace application\controllers;

use application\core\Controller;
use application\core\View;
use application\lib\Curl as cUrl;

class MainController extends Controller {
    
    public function indexAction() {
        if($this->isAjax()){
            echo $this->model->getCurrentState();
        }
        else{
            $data["steamShops"] = $this->generateRows($this->model->getSteamShop()); // получение всех котельных
            $data["needTemp"]=1; // вывод кнопки с температурой
            $this->view->render("Главная старница",$data);
        }

    }
    
    public function mapAction() {

        if($this->isAjax()){
            if($_POST["s"] === "tiraspol"){
                echo $this->model->getMapState();
            } else {
                echo $this->getMapDataFromNorthRegions($_POST["s"]);
            }
            
        }
        else{
            echo $this->isAjax();
            $this->view->render("Карта");
        }

    }
    
    public function archiveAction() {
        if($this->isAjax()){
            $data = $_POST;
            echo $this->model->getArchiveCrashes($data);
        }
        else {
            $this->view->render("Архивы");
        }
    }
    
    public function statisticAction() {
        if($this->isAjax()){
            $data = $_POST;
            echo $this->model->getStatisticCrashes($data);
        }
        else {
            $this->view->render("Статистика",["dateUpdate"=>date('d-m-y H:i:s')]);
        }
    }

    public function alarmAction(){
        if($this->isAjax()){
            //debug($this->route["alarm"]);
            if( $this->model->setupAlarm($this->route["alarm"])) {
                echo "{\"status\":\"success\",\"message\":\"Подтверждено\",\"url\":\"/\"}";
            } else {
                echo "{\"status\":\"error\",\"message\":\"Ошибка\",\"url\":\"/\"}";
            }
        } else {
            View::errorCode(403,'');
        }
    }

    private function generateRows($arraySteamShops){ 
        /// продумать логику деления на 3 столбца с сменяющимся коэффициентом деления;
        $group = "";
        $dispHTMLLeft = "";
        $dispHTMLRight = "";
        $tmpHtmlDelimetr = "";
        $htmlDelimetr = "";
        $cntSteamShop = count($arraySteamShops);
        foreach ($arraySteamShops as $key => $val) {
            $isNeedDiscrete = intval($val["isNeedCHTDiscrete"]);
            $isNeedAnalog = intval($val["isNeedCHTAnalog"]);
            $colspan = 3 - ($isNeedDiscrete + $isNeedAnalog);
            $classHideHeader = ($group == $val["group_name"])?" hide-header":"";
            $group = $val["group_name"];
            $htmlDelimetr = ($cntSteamShop > 1.68*$key)?"dispHTMLLeft":"dispHTMLRight";
            //var_dump("<pre>",$tmpHtmlDelimetr,$htmlDelimetr,"</pre>");
            if($tmpHtmlDelimetr=="dispHTMLLeft" and $htmlDelimetr == "dispHTMLRight")
                $classHideHeader = "";
            $tmpHtmlDelimetr = $htmlDelimetr;
            $header = $this->headerRowGenerate($val);
            $footer = $this->footerRowGenerate($val);
            $$htmlDelimetr .= "<div class='info--row header--mode{$colspan}{$classHideHeader}'>\n{$header}{$footer}</div>";
        }
        return "<div class='info--flex'><div>{$dispHTMLLeft}</div><div>{$dispHTMLRight}</div></div>";
    }

    private function headerRowGenerate($data){
        //if($data["SortOrder"]>70) debug($data);
        $isNeedDiscrete = intval($data["isNeedCHTDiscrete"]);
        $isNeedAnalog = intval($data["isNeedCHTAnalog"]);
        $isMain = intval($data["isMain"]);
        $classMain = ($isMain == 1)?" main":"";
        
        $dispHTMLHeader = "<div class='info--elem elem--header ieheader'>\n<span>{$data["headerName"]}</span>\n</div>";
        if($isNeedDiscrete === 1 and !$isMain == 1 or $isMain == 0) {/// if one operator no need  {  } 
            $dispHTMLHeader .= "<div class='info--elem elem--header discrete'>
                <span>&nbsp;</span>
                <span>&nbsp;</span>
                <span>&nbsp;</span>
                <span>&nbsp;</span>
                <span>&nbsp;</span>
                <span>&nbsp;</span>
                <span>&nbsp;</span>
                <span>&nbsp;</span>
            </div>";
        } else {
            $dispHTMLHeader .= "<div class='info--elem elem--header discrete'></div>";
        }
        if($isNeedAnalog === 1 or $isMain == 1) {/// if one operator no need  {  } 
            $json = json_decode($data["CHTAnalog"],true);
            $arrKeys = ($isMain==1)? array("A","B","C","D","E","F","G","H")
                : array("A","B","C","D","E","F");
            $dispHTMLHeader .= "<div class='info--elem elem--header analog{$classMain}'>";
            foreach($arrKeys as $k=>$val){
                // A => Main text @ B => Footer text @ C => Header text @ D => Styles
                $styles = (stripos($json[$val]["D"],"i")!==false)?" italic":"";
                $styles .= (stripos($json[$val]["D"],"s")!==false)?" bold":"";
                $styles .= (stripos($json[$val]["D"],"u")!==false)?" under":"";
                $dispHTMLHeader .= sprintf("<span class='elem--head%s'>%s<sub>%s</sub><sup>%s</sup></span>"
                                    ,$styles,$json[$val]["A"],$json[$val]["B"],$json[$val]["C"]);
            }
            $dispHTMLHeader .= "</div>";
           
        } else {
            $dispHTMLHeader .= "<div class='info--elem elem--header analog'></div>";
        }
        return $dispHTMLHeader;
    }

    private function footerRowGenerate($data){
        $isNeedDiscrete = intval($data["isNeedCHTDiscrete"]);
        $isNeedAnalog = intval($data["isNeedCHTAnalog"]);
        $isMain = intval($data["isMain"]);
        $classMain = ($isMain == 1)?" main":"";
        
        $dispHTMLFooter = "<div class='info--elem elem--footer ieheader'>\n<span class='elem_name black' id='AAA{$data["id_kotelnaya"]}'>{$data["title"]}</span>\n</div>";
        
        if($isNeedDiscrete === 1 and !$isMain == 1 or $isMain == 0) {/// if one operator no need  {  } 
            $dispHTMLFooter .= "<div class='info--elem elem--footer discrete{$classMain}'>
                <span id='ABA{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ABB{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ABC{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ABD{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ABE{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ABF{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ABG{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ABH{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
            </div>";
        } else {
            $dispHTMLFooter .= "<div class='info--elem elem--footer discrete{$classMain}'></div>";
        }
        if($isNeedAnalog === 1 or $isMain == 1) {/// if one operator no need  {  } 
            $dispHTMLFooter .= "<div class='info--elem elem--footer analog{$classMain}'>
                <span id='ACA{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ACB{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ACC{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ACD{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ACE{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ACF{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>\n";
            if($isMain == 1) $dispHTMLFooter .= "
                <span id='ACG{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>
                <span id='ACH{$data["id_kotelnaya"]}' class='elem_name black'>&nbsp;</span>\n";
            $dispHTMLFooter .= "</div>";
        } else {
            $dispHTMLFooter .= "<div class='info--elem elem--footer analog{$classMain}'></div>";
        }
        return $dispHTMLFooter;
    }

    private function getMapDataFromNorthRegions($type){
        $url = "";
        switch($type){
            case 'rybnitsa':
                $url = "http://217.19.212.252:8080/30d0facd-c44d-4d6a-87d0-45df106a5678/script_scan_mva8_rybnitsa.php";
                break;
            case 'kamenka':
                $url = "http://217.19.212.252:8080/30d0facd-c44d-4d6a-87d0-45df106a5678/script_scan_mva8_kamenka.php";
                break;
            case 'dubossary':
                $url = "http://217.19.212.252:8080/30d0facd-c44d-4d6a-87d0-45df106a5678/script_scan_mva8_dubossary.php";
                break;
        }
        if($url!==""){
            $opts = ["CURLOPT_SSL_VERIFYHOST"=>false];
            $curl = new cUrl($url);
            $curl->prepare($url,[],$opts);
            $curl->exec_post();
            return $curl->get_response();
        }
        return false;
    }

}


?>