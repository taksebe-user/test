<?php

namespace application\controllers;

use application\core\Controller;
use application\core\View;


class MainController extends Controller
{

    public function indexAction()
    {
        if ($this->isAjax()) {
            //todo
            View::errorCode(403, '');
        } else {
            //todo curl get data
            return ($this->filterDocuments($this->model->getAllData()));
        }
    }

    private function filterDocuments($arrDocs)
    {
        foreach ($arrDocs as $doc) {
            if ($doc["has_electronic_view"] == 1) {
                $current = $this->model->getCurrent($doc["id"]);
                //if ($doc["description"]["payment"]["account_number"] != "") 
                $this->model->setCurrent([
                    $current["id"],
                    $current["name"],
                    $current["organization"],
                    $current["description"]["payment"],
                    $current["description"]["state_duty_payment"],
                    $current["description"]["has_electronic_view"],
                ]);
            }
        }
        return $arrDocs;
    }
}

?>