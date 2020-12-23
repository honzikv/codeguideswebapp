<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class ReviewModel extends BaseModel {

    function validate() {
    }

    function getPendingReviews($userId) {
        $statement = 'SELECT review.id as review_id, reviewer_id, abstract, is_finished, name FROM review 
                        INNER JOIN guide g on review.guide_id = g.id WHERE is_finished = false AND reviewer_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$userId]);
        return $query->fetchAll();
    }
}