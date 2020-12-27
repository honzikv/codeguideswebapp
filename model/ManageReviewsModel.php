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

        if ($this->existsInDatabase('guide', 'id', $this->guideId)) {
            throw new Exception('Error, guide id does not exist');
        }
    }

    function getGuideReviews() {
        $statement = 'SELECT username, info_score, efficiency_score, complexity_score, quality_score, overall_score,
                       notes, is_finished FROM review INNER JOIN user u ON review.reviewer_id = u.id  WHERE guide_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->guideId]);
        return $query->fetchAll();
    }
}