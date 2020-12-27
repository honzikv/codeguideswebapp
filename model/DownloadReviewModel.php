<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class DownloadReviewModel extends BaseModel {

    var string $reviewId;

    function validate() {
        if (empty($this->reviewId)) {
            throw new Exception('Error, file id is empty');
        }

        if (!is_numeric($this->reviewId)) {
            throw new Exception('Error file id is not a number');
        }
    }

}