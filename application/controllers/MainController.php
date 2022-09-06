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
            //todo curl get data
            //file_put_contents ("filterAll.json",
            $files = $this->filterDocuments($this->model->getAllData());
            file_put_contents(
                "clearJSON.json",
                json_encode($files, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE)
            );
            $xml_data = new SimpleXMLElement('<?xml version="1.0"?><uslugi></uslugi>');

            // function call to convert array to xml
            $this->array_to_xml($files, $xml_data);

            //saving generated xml file; 
            $result = $xml_data->asXML('clearXML.xml');
            
            debug($this->model->getAllFromDB());
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
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }
}
