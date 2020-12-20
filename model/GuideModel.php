<?php


namespace app\model;


use app\core\BaseModel;

class GuideModel extends BaseModel {

    const NAME_LIMIT = 60;
    const MEDIUM_TEXT_LIMIT_CHARACTERS = 30000;

    const NAME = 'name';
    const ABSTRACT = 'abstract';
    const FILE = 'file';
    

    function validate() {
        // TODO: Implement validate() method.
    }


}