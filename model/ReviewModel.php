<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

/**
 * Model pro recenzi
 * Class ReviewModel
 * @package app\model
 */
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

    /**
     * Ziska recenze, ktere lze upravit
     * @param $userId
     * @param $reviewedId
     * @return array
     */
    function getEditableReviews($userId, $reviewedId) {
        $statement = 'SELECT review.id as review_id, reviewer_id, abstract, is_finished, name FROM review 
                        INNER JOIN guide g on review.guide_id = g.id WHERE g.guide_state = (?) AND reviewer_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$reviewedId, $userId]);
        return $query->fetchAll();
    }

    /**
     * Ziska recenzu z user id a nacteneho id
     * @param $userId
     * @return mixed
     */
    function getReviewFromUserId($userId) {
        $statement = 'SELECT * FROM review WHERE id = (?) AND reviewer_id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->reviewId, $userId]);
        return $query->fetch();
    }

    /**
     * Ziska recenzi z reviewId
     * @param $reviewId
     * @return mixed
     */
    function getReview($reviewId) {
        $statement = 'SELECT * FROM review WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$reviewId]);
        return $query->fetch();
    }

    /**
     * Ziska dokoncene recenze pro danou guide
     * @param string $guideId
     * @return array
     */
    function getFinishedReviewsForGuide(string $guideId) {
        $statement = 'SELECT * FROM review WHERE guide_id = (?) and is_finished = true';
        $query = $this->prepare($statement);
        $query->execute([$guideId]);
        return $query->fetchAll();
    }

}