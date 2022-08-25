<?php
    function get_web_page( $url ){

        $options = array(
        
            "CURLOPT_RETURNTRANSFER" => 1, // return web page
            
            "CURLOPT_HEADER" => 0, // don’t return headers
            
            "CURLOPT_FOLLOWLOCATION" => 1, // follow redirects
            
            "CURLOPT_ENCODING" => "", // handle all encodings
            
            "CURLOPT_USERAGENT" => "spider", // who am i
            
            "CURLOPT_AUTOREFERER" => 1, // set referer on redirect
            
            "CURLOPT_CONNECTTIMEOUT" => 60, // timeout on connect
            
            "CURLOPT_TIMEOUT" => 60, // timeout on response
            
            "CURLOPT_MAXREDIRS" => 1, // stop after 10 redirects
        
        );
        
        $ch = curl_init( $url );
        
        curl_setopt_array( $ch, $options );
        
        $content = curl_exec( $ch );
        
        curl_close( $ch );
        
        return $content;
}

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
            // ob_start();
            
            // get_web_page("https://uslugi.gospmr.org/?option=com_uslugi&view=usluga&task=getUsluga&uslugaId={$v["id"]}");
            
            // $tempData = json_decode(ob_get_clean(),true);
            //var_dump($tempData["organization"],$tempData["description"]["payment"],$tempData["description"]["state_duty_payment"],"<hr />"); die();
            $tmpData[] = array(
                "id"=> $v["id"],
                "name" => $v["name"],
                //"organization"=>$tempData["organization"],
                //"payment"=>$tempData["description"]["payment"],
                //"state_duty_payment"=>$tempData["description"]["state_duty_payment"],
                "has_electronic_view"=>$v["has_electronic_view"],
                
            );
            //return;
        }
    }
    var_dump($tmpData);//die();
    var_dump("<hr />",microtime()-$time);

    //die();
