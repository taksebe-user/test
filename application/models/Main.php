<?php
    
namespace application\models;

use application\core\Model;
use DateTime;
use DateInterval;

class Main extends Model {

    public function getSteamShop(){
        $res = $this->db->row("
                SELECT   `id_kotelnaya`,`name`,`isMain`
                        ,`SortOrder`,`title`
                        ,`isNeedHeader`,`headerName`
                        ,`group_name`, `ContextHeaderTitleName`
                        , `isNeedCHTDiscrete`, `CHTDiscrete`
                        , `isNeedCHTAnalog`, `CHTAnalog` 
                FROM `name_of_kotelnaya_new` nkn 
                    left join `ContextHeaderTitle` cht 
                        on (nkn.nameHeaderID = cht.idContextHeaderTitle
                            and cht.`isDeleted`=0)
                WHERE nkn.`isDeleted`=0 
                ORDER BY `SortOrder` ASC");
        return $res;
    }

    public function getCurrentState(){
        $json = $this->getCurrentPingSteam();
        $crashesNames = $this->getNameCrashes();
        $dicsrete = $this->getCurrentDiscrete($crashesNames);
        $analog = $this->getCurrentAnalog();
        $crashes = $this->getCurrentCrashes();
        //debug($dicsrete);
        foreach ($json as $key => $value) {
            $json[$key]['AAAF'] =(array_key_exists('id_'.$value['AAAA'], $dicsrete))? $dicsrete['id_'.$value['AAAA']]:array();
            $json[$key]['AAAG'] =(array_key_exists('id_'.$value['AAAA'], $analog))?$analog['id_'.$value['AAAA']]:array();
        }
        foreach ($crashes as $key => $value) {
            if(!isset($_SESSION["user"]["admin"])) 
                $crashes[$key]["AAAC"] = null;
            $crashes[$key]['AAAA']=$json['id_'.$crashes[$key]['AAAA']]['AAAH'];
            $crashes[$key]['AAAB']=$crashesNames['id_'.$crashes[$key]['AAAB']]['AAAB'];
        }
        $json[] = array('AAAA'=>$crashes);
        
        return json_encode($json,JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }

    public function getArchiveCrashes($data){
        extract($data);
        $unix_time = time();
        $unix_time_year_ago = $unix_time-'31556926';
        $unix_time_range = '300'; //Количество секунд, которое данные считаются актуальными
        $unix_time_range_kishinev = '3600'; //Количество секунд, которое данные считаются актуальными по кишинёвской системе
        //$cnt = $numPage;
        $cnt = (isset($cnt))?$cnt:100;
        
        $cntCrash = $this->db->column("
                select count(*) as cnt 
                from `crash_new` 
                WHERE `u_time_create` > :timeYearAgo
            "
            ,array("timeYearAgo"=>[$unix_time_year_ago,"i"])
        );
        //debug( [$cntCrash,$unix_time_year_ago]);
        $cntPages = ceil($cntCrash / $cnt);
        //debug([$cntPages,$cntCrash]);
        $page=$numPage;
        $page=($page<1 or $page>$cntPages)?1:$page;
        $offset = ($page-1)*$cnt;
        
        $params = array(
            "time_create"=>[$unix_time_year_ago,"i"],
            "offset"=>[$offset,"i"],
            "cnt"=>[$cnt,"i"],
        );

        $sql = "SELECT 
                    cn.`id_kotelnaya` `AAAA` 
                    ,cn.`id_name_crash` `AAAB` 
                    ,concat(`Id_and_adress`,`u_time_create`,cn.`id_name_crash`) `AAAC` 
                    ,concat(nkn.`name`,' - ',ncn.`name`) `AAAD`
                    ,`h_time_create` `AAAE` 
                    ,`confirm` `AAAF` 
                    ,ABS(nullif(`u_time_confirm`,0)-`u_time_create`) `AAAG` 
                    ,`liquidation` `AAAH` 
                    ,`h_time_liquidation` `AAAI`
            FROM
                    `crash_new` cn left join `name_of_kotelnaya_new` nkn on (cn.`id`=nkn.`id_kotelnaya`)
                            left join `name_of_crash_new` ncn on (cn.`id_name_crash`=ncn.`id_name_crash`)
            WHERE
                    cn.`u_time_create` > :time_create
            ORDER BY cn.`u_time_create` DESC
            LIMIT :cnt OFFSET :offset;
            ";
        
        $res = $this->db->row($sql, $params);

        $res['cnt']=$cntPages;
        $res['crnt']=$page;
        
        return json_encode($res);
    }

    public function getStatisticCrashes($data){
        extract($data);
        //debug($data);
        if ($rangeStart=='') {
			$data_rangeStart=date_format(date_sub( new DateTime("NOW"),new DateInterval('P1M')),'Y-m-d');
		} else {
            $time = DateTime::createFromFormat("d-m-Y", $rangeStart);
			$data_rangeStart=$time->format("Y-m-d");
		}
		if ($data["rangeEnd"]=='') {
			$data_rangeEnd=date_format( new DateTime("NOW"),'Y-m-d');
		} else {
            $time = DateTime::createFromFormat("d-m-Y", $rangeEnd);
			$data_rangeEnd=$tine->format("Y-m-d");
		}
	
		$json = array();
		$json['dateMin']=$data_rangeStart;
		$json['dateMax']=$data_rangeEnd;
		
		if($data_rangeEnd<$data_rangeStart){
			$temp = $data_rangeEnd;
			$data_rangeEnd=$data_rangeStart;
			$data_rangeStart=$temp;
			unset($temp);
		}
		

		$sql = "select id as `AAAA`, id_name_crash as `AAAB`,h_time_create as `AAAC` from `crash_new` 
		where h_time_create between :start and :end";

        $params = ["start"=>["$data_rangeStart 00:00:00","s"],"end"=>["$data_rangeEnd 23:59:59","s"]];
		$res = $this->db->row($sql,$params);
		//var_dump($res);
		$arrDate=array();
		//echo '<pre>';
		foreach($res as $value){
			//aaaa = id, aaab = id_crash, aaac = time_create
			//var_dump($key,$value);
			//echo $value['AAAA'].$value['AAAB'].PHP_EOL;
			if (isset($json[$value['AAAB']][$value['AAAA']])) {
				++$json[$value['AAAB']][$value['AAAA']];
			} else {
				$json[$value['AAAB']][$value['AAAA']]=1;
			}
			
			$arrDate[]=$value['AAAC'];
		}
		//echo "</pre>";
		$json['minDate']=date_format(new Datetime(min($arrDate)),'d-m-Y');
		$json['maxDate']=date_format(new Datetime(max($arrDate)),'d-m-Y');
		//mysqli_free_result($res);
		
		$sql="select id_kotelnaya as 'AAAA',name as 'AAAB' 
            from `name_of_kotelnaya_new` 
            where isDeleted =0 and isActive=1 order by SortOrder";
		
		$json['head']=$this->db->row($sql);
		//mysqli_free_result($res);
		
		$sql="select id_name_crash as 'AAAA',name as 'AAAB' 
            from name_of_crash_new 
            where isRealAlarm=1
            order by 1";
	
		//$json['sql']=$sqlSelect;
		$json['rows']=$this->db->row($sql);
		//var_dump($res);//retu//rn 1;
        $json["newDate"] = date('d-m-y H:i:s');

        return json_encode($json);
    }

    public function getMapState(){
        $ping = $this->db->row("SELECT id_kotelnaya as ID
                    ,ping as isConnected
                    ,u_time as lastPing
                    FROM ip_ping_new");
        
        $id_kot=array();
        
        foreach ($ping as $data) {
            $id_kot[$data['ID']]['isCon'] = $data['isConnected'];
		    $id_kot[$data['ID']]['ping']  = $data['lastPing'];
        }
        
        $crash = $this->db->row("SELECT Id_and_adress, id, id_name_crash
                            , confirm, liquidation, id_user 
                    FROM crash_new
                    where liquidation=0");

        foreach($crash as $data){
            $id_kot[$data['id']][$data['Id_and_adress'].'_'.$data['id_name_crash']] = 
            ['confirm' => $data['confirm']
            ,'liquidation' => $data['liquidation']
            ,'id_user' => $data['id_user']
            ,'id_name_crash' => $data['id_name_crash']];
        }
        
        //debug(json_encode($id_kot));
        return json_encode($id_kot);
    }

    public function setupAlarm($id){
        $cnt = $this->db->row("select concat(`Id_and_adress`,`u_time_create`,`id_name_crash`) as id,`confirm`,`liquidation` 
                from crash_new 
                where concat(`Id_and_adress`,`u_time_create`,`id_name_crash`) = :alarm"
            ,["alarm"=>[$id,"i"]])[0];
        //debug($cnt);
        if($cnt["confirm"]==1 or $cnt["confirm"]==1 and $cnt["liquidation"]==1){
            return false;
        } else {
            $cnt=$this->db->query("
                    UPDATE `crash_new` 
                    SET `confirm`='1',`h_time_confirm`=now(),`u_time_confirm`= :timeCreate, `id_user` = :userId
                    WHERE CONCAT (`Id_and_adress`,`u_time_create`,`id_name_crash`) = :id"
                ,["id"=>[$id,"i"]
                ,"timeCreate"=>[time(),"i"]
                ,"userId"=>[$_SESSION["user"]["id"],"i"]]
            );
        }
        return true;
    }
    
    private function getCurrentPingSteam(){
        $res = $this->db->row("
            select   ipp.id_kotelnaya as 'AAAA'
                    ,ip 'AAAB'
                    ,ping 'AAAC'
                    ,u_time 'AAAD'
                    ,h_time  'AAAE'
                    ,nm.`name` as 'AAAH'
                    ,CONCAT('{',
                        concat_WS(',',
                            '\"AAAA\":\"'||urlTitleMain||'\"'
                            ,CONCAT(
                                '\"AAAB\":{' , 
                                    CONCAT_WS(',',
                                        '\"AAAA\":\"'||urlDiscrete1||'\"'
                                        ,'\"AAAB\":\"'||urlDiscrete2||'\"'
                                        ,'\"AAAC\":\"'||urlDiscrete3||'\"'
                                        ,'\"AAAD\":\"'||urlDiscrete4||'\"'
                                        ,'\"AAAE\":\"'||urlDiscrete5||'\"'
                                        ,'\"AAAF\":\"'||urlDiscrete6||'\"'
                                        ,'\"AAAG\":\"'||urlDiscrete7||'\"'
                                        ,'\"AAAH\":\"'||urlDiscrete8||'\"'
                                        )
                                    ,'}'
                                )
                            ,CONCAT(
                            '\"AAAC\":{' , 
                                CONCAT_WS(',',
                                    '\"AAAA\":\"'||urlAnalog1||'\"'
                                    ,'\"AAAB\":\"'||urlAnalog2||'\"'
                                    ,'\"AAAC\":\"'||urlAnalog3||'\"'
                                    ,'\"AAAD\":\"'||urlAnalog4||'\"'
                                    ,'\"AAAE\":\"'||urlAnalog5||'\"'
                                    ,'\"AAAF\":\"'||urlAnalog6||'\"'
                                    ,'\"AAAG\":\"'||urlAnalog7||'\"'
                                    ,'\"AAAH\":\"'||urlAnalog8||'\"'
                                    )
                                ,'}'
                            )
                        )
                    ,'}'
                    ) as 'AABA'
            from ip_ping_new as ipp 
                    inner join name_of_kotelnaya_new nm on (ipp.id_kotelnaya=nm.id_kotelnaya and nm.IsDeleted = 0 AND nm.isActive=1)
                        left join SetupURLTitle sut on (nm.id_kotelnaya=sut.id_kot and sut.IsDeleted=0)
        ");

        foreach ($res as $key => $value) {
            $res['id_'.$value['AAAA']] = $value;
            unset($res[$key]);
        }
        return $res;
    }
    
    private function getNameCrashes(){
        $res = $this->db->row("
            select `id_name_crash` AAAA,`name` AAAB,`isRealAlarm` AAAC 
            from name_of_crash_new
        ");
        foreach ($res as $key => $value) {
            $res['id_'.$value['AAAA']] = $value;
            unset($res[$key]);
        }
        return $res;
    }
    
    private function getCurrentDiscrete($crashesNames){
        $res = $this->db->row("
            select dis.`id_and_adress` AAAA ,dis.`id` AAAB,dis.`adress` AAAC,dis.u AAAD,
                 dis.1 AABA, dis.2 AABB, dis.3 AABC, dis.4 AABD, dis.5 AABE
                , dis.6 AABF, dis.7 AABG, dis.8 AACA, dis.9 AACB, dis.10 AACC
                , dis.11 AACD, dis.12 AACE, dis.13 AACF, dis.14 AACG, dis.15 AACH
                , dis.16 AACI, discr.1 AADA, discr.2 AADB, discr.3 AADC, discr.4 AADD
                , discr.5 AADE, discr.6 AADF, discr.7 AADG, discr.8 AAEA, discr.9 AAEB
                , discr.10 AAEC, discr.11 AAED, discr.12 AAEE, discr.13 AAEF, discr.14 AAEG
                , discr.15 AAEH, discr.16 AAEI
            from owen_MV110_16D_now_online dis 
                inner join owen_MV110_16D_now_online_crash_id discr 
                    on (dis.id_and_adress=discr.id_and_adress);");

        $arrayKeys = array('AADA','AADB','AADC','AADD','AADE','AADF','AADG','AAEA','AAEB','AAEC','AAED','AAEE','AAEF','AAEG','AAEH','AAEI');
        foreach($res as $key=>$mvalue){
            $res['id_'.$mvalue['AAAB']]=$mvalue;
            unset($res[$key]);
            foreach($arrayKeys as $value){
                $res['id_'.$mvalue['AAAB']][$value] = $crashesNames['id_'.$mvalue[$value]];
            }
        }
        return $res;
    }

    private function getCurrentAnalog(){
        $res =  $this->db->row("
            select`id_kot` as `AAAA`,
                if(`titleCol1` < 500 AND `titleCol1` > -500,`titleCol1`,if(isnull(`titleCol1`),`titleCol1`,'####')) as `AAAB`, `unixTitleCol1` as `AAAJ`, `descButtonCol1` as `AABA`,
                if(`titleCol2` < 500 AND `titleCol2` > -500,`titleCol2`,if(isnull(`titleCol2`),`titleCol2`,'####')) as `AAAC`, `unixTitleCol2` as `AAAK`, `descButtonCol2` as `AABB`,
                if(`titleCol3` < 500 AND `titleCol3` > -500,`titleCol3`,if(isnull(`titleCol3`),`titleCol3`,'####')) as `AAAD`, `unixTitleCol3` as `AAAL`, `descButtonCol3` as `AABC`,
                if(`titleCol4` < 500 AND `titleCol4` > -500,`titleCol4`,if(isnull(`titleCol4`),`titleCol4`,'####')) as `AAAE`, `unixTitleCol4` as `AAAM`, `descButtonCol4` as `AABD`,
                if(`titleCol5` < 500 AND `titleCol5` > -500,`titleCol5`,if(isnull(`titleCol5`),`titleCol5`,'####')) as `AAAF`, `unixTitleCol5` as `AAAN`, `descButtonCol5` as `AABE`,
                if(`titleCol6` < 500 AND `titleCol6` > -500,`titleCol6`,if(isnull(`titleCol6`),`titleCol6`,'####')) as `AAAG`, `unixTitleCol6` as `AAAO`, `descButtonCol6` as `AABF`,
                if(`titleCol7` < 500 AND `titleCol7` > -500,`titleCol7`,if(isnull(`titleCol7`),`titleCol7`,'####')) as `AAAH`, `unixTitleCol7` as `AAAP`, `descButtonCol7` as `AABG`,
                if(`titleCol8` < 500 AND `titleCol8` > -500,`titleCol8`,if(isnull(`titleCol8`),`titleCol8`,'####')) as `AAAI`, `unixTitleCol8` as `AAAQ`, `descButtonCol8` as `AABH`
            from `title_Analog_now`;
        ");

        foreach($res as $key=>$mvalue){
            $res['id_'.$mvalue['AAAA']]=$mvalue;
            unset($res[$key]);
        }
        return $res;
    }

    private function getCurrentCrashes(){
        $res = $this->db->row("
            select `id_kotelnaya` `AAAA` ,`id_name_crash` `AAAB` 
                ,concat(`Id_and_adress`,`u_time_create`,`id_name_crash`) `AAAC` 
                ,`h_time_create` `AAAD` ,`confirm` `AAAE` 
                ,nullif(`u_time_confirm`,0)-`u_time_create` `AAAF` 
                ,`liquidation` `AAAG` 
                ,`h_time_liquidation` `AAAH` 
            from dispatcher.crash_new 
            where `confirm`= 0 OR `liquidation` = 0 ORDER BY `u_time_create` DESC;
        ");

        return $res;
    }
}

?>