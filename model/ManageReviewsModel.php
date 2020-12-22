<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class ManageReviewsModel extends BaseModel {

    var string $guideId;

    function validate() {
        if (empty($this->guideId)) {
            throw new Exception('Error, guide id not provided');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Error, guide id is not a number');
        }
    }

    function getGuideReviews() {
        $statement = 'SELECT * from review WHERE guide_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$statement]);
        return $query->fetchAll();
    }
}