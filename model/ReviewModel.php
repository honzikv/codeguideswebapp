<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class ReviewModel extends BaseModel {

    var string $reviewId;

    function validate() {
        if (empty($this->reviewId)) {
            throw new Exception('Error, review id is empty');
        }

        if (!is_numeric($this->reviewId)) {
            throw new Exception('Error, review id is not a number');
        }

        if (!$this->existsInDatabase('review', 'id', $this->reviewId)) {
            throw new Exception('Error, review id does not exist');
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

    function getFinishedReviewsForGuide(string $guideId) {
        $statement = 'SELECT * FROM review WHERE guide_id = (?) and is_finished = true';
        $query = $this->prepare($statement);
        $query->execute([$guideId]);
        return $query->fetchAll();
    }

}