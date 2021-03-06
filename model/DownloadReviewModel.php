<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

/**
 * Model pro stazeni guide z formulare pro recenzi
 * Class DownloadReviewModel
 * @package app\model
 */
class DownloadReviewModel extends BaseModel {

    var string $reviewId;

    function validate() {
        if (empty($this->reviewId)) {
            throw new Exception('Error, file id is empty');
        }

        if (!is_numeric($this->reviewId)) {
            throw new Exception('Error file id is not a number');
        }

        if (!$this->existsInDatabase('review', 'id' , $this->reviewId)) {
            throw new Exception('Error, review id does not exist');
        }
    }

}