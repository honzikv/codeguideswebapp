<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class ReviewModel extends BaseModel {

    var string $reviewId;

    private const MIN_SCORE = 0;
    private const MAX_SCORE = 10;

    function validate() {
        if (empty($this->reviewId)) {
            throw new Exception('Error, review id is empty');
        }

        if (!is_numeric($this->reviewId)) {
            throw new Exception('Error, review id is not a number');
        }

    }

    function getPendingReviews($userId) {
        $statement = 'SELECT review.id as review_id, reviewer_id, abstract, is_finished, name FROM review 
                        INNER JOIN guide g on review.guide_id = g.id WHERE is_finished = false AND reviewer_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$userId]);
        return $query->fetchAll();
    }

    function getReviewFromUserId($userId) {
        $statement = 'SELECT * FROM review WHERE id = (?) AND reviewer_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->reviewId, $userId]);
        return $query->fetch();
    }

    function getReview($reviewId) {
        $statement = 'SELECT * FROM review WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$reviewId]);
        return $query->fetch();
    }

}