<?php

namespace application\controllers;

use application\core\Controller;
use application\core\View;
use \SimpleXMLElement;

class MainController extends Controller
{

    public function indexAction()
    {
        if ($this->isAjax()) {
            //todo
            View::errorCode(403, '');
        } else {
            $datetime = date("d-m-Y H:i:s");
            //todo curl get data
            try{
                $this->model->writeLog();
                $filter = $this->filterDocuments($this->model->getAllData());
                //$filter_conv = mb_convert_encoding($filter,"CP1251");
                $files = htmlspecialchars_decode(json_encode($filter, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE));
                file_put_contents( "clearJSON.json", $files );
                //debug($files);
                $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><uslugi></uslugi>');

                // function call to convert array to xml
                $this->array_to_xml($filter, $xml_data);

                //saving generated xml file; 
                $result = $xml_data->asXML("xml/uslugi_{$datetime}.xml");
                //debug($xml_data);
                $this->view->render("Testing Test",["uslugi"=>$files]);
                //$this->model->writeLog([date("d-m-Y H:i:s")]);
            } catch(\Exception $e){
                $this->model->writeLog($e);
            }
        }
    }

    private function filterDocuments($arrDocs)
    {
        $tmpDocs = array();
        foreach ($arrDocs as $doc) { //search and filter created files for optimization process
            $id = $doc["id"];
            if ($doc["has_electronic_view"] == 1 and !file_exists("files/{$id}.json")) {
                $tmpDocs[] = intval($id);
            }
        }
        //debug($tmpDocs);

        foreach ($tmpDocs as $id) {
            $this->model->getCurrent($id);
        }

        if (count($tmpDocs) == 0) {
            $tmpFiles = array();
            foreach (array_diff(scandir("files/"), array('.', '..')) as $file) {
                $fileContents = json_decode(file_get_contents("files/$file"),true);
                if(is_null($fileContents["id"])){
                    $fileContents = $this->model->getCurrent(str_replace(".json","",$file),true);
                    //debug($fileContents);
                }
                $tmpFiles[] = $fileContents;
                //add logic to adding records to DB
                $this->model->setCurrent($fileContents);
            }

            return ($tmpFiles);
        }
    }

    private function array_to_xml($data, &$xml_data)
    {
        foreach ($data as $key => $value) {

            if (is_array($value) or is_object($value)) {
                if (is_numeric($key)) {
                    $key = 'usluga' . $key; //dealing with <0/>..<n/> issues
                }
                $subnode = $xml_data->addChild($key);
                $this->array_to_xml($value, $subnode);
            } else {
                //debug(["$key", gettype($value)]);
                $xml_data->addChild("$key", html_entity_decode($value, ENT_COMPAT, 'cp1252'));
            }
        }
    }
}
