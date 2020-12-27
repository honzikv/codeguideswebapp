<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class DownloadGuideModel extends BaseModel{

    var string $guideId;

    function validate() {
        if (empty($this->guideId)) {
            throw new Exception('Error, guide id is empty');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Error, guide id is not a number');
        }
    }

}