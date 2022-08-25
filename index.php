<?php
    function get_web_page( $url ){

        $options = array(
        
            "CURLOPT_RETURNTRANSFER" => true, // return web page
            
            "CURLOPT_HEADER" => false, // don’t return headers
            
            "CURLOPT_FOLLOWLOCATION" => true, // follow redirects
            
            "CURLOPT_ENCODING" => "", // handle all encodings
            
            "CURLOPT_USERAGENT" => "spider", // who am i
            
            "CURLOPT_AUTOREFERER" => true, // set referer on redirect
            
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

    ob_start();
    get_web_page("https://uslugi.gospmr.org/?option=com_uslugi&view=gosuslugi&task=getUslugi");
    $flsData = ob_get_clean();
    $glbData = json_decode($flsData,true);

    //var_dump(gettype($flsData),$glbData["ulist"]);die();

    $tmpData = array();

    foreach($glbData["ulist"] as $v){
        if($v["has_electronic_view"] == 1){
            ob_start();
            get_web_page("https://uslugi.gospmr.org/?option=com_uslugi&view=usluga&task=getUsluga&uslugaId={$v["id"]}");
            $tempData = json_decode(ob_get_clean(),true);
            //var_dump($tempData,"<hr />");
            $tmpData[] = array(
                "id"=> $v["id"],
                "name" => $v["name"],
                "organization"=>$tempData["organization"],
                "payment"=>$tempData["payment"],
                "state_duty_payment"=>$tempData["state_duty_payment"],
                "has_electronic_view"=>$v["has_electronic_view"],
                
            );
            return;
        }
    }

    var_dump($tmpData[0]);die();
?>