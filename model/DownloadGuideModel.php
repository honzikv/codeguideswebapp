<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

/**
 * Model pro stazeni guide
 * Class DownloadGuideModel
 * @package app\model
 */
class DownloadGuideModel extends BaseModel{

    var string $guideId;

    function validate() {
        if (empty($this->guideId)) {
            throw new Exception('Error, guide id is empty');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Error, guide id is not a number');
        }

        if (!$this->existsInDatabase('guide', 'id', $this->guideId)) {
            throw new Exception('Error, guide id does not exist');
        }
    }

}