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
            //file_put_contents ("filterAll.json",
            $this->filterDocuments($this->model->getAllData());
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
        debug($tmpDocs);

        foreach ($tmpDocs as $id) {
            $this->model->getCurrent($id);
        }
    }
}
