<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

/**
 * Model pro odstraneni recenze
 * Class DeleteReviewModel
 * @package app\model
 */
class DeleteReviewModel extends BaseModel {

    var string $reviewId;
    var string $guideId;

    function validate() {
        if (empty($this->reviewId)) {
            throw new Exception('Error, review id is empty');
        }

        if (!is_numeric($this->reviewId)) {
            throw new Exception('Error, review id is not a number');
        }

        if (empty($this->guideId)) {
            throw new Exception('Error, guide id is empty');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Error, guide id is not a number');
        }

        if (!$this->existsInDatabase('review', 'id', $this->reviewId)) {
            throw new Exception('Error, review id does not exist');
        }

        if (!$this->existsInDatabase('guide', 'id', $this->guideId)) {
            throw new Exception('Error, guide id does not exist');
        }
    }

    /**
     * Smazani review z databaze
     */
    function deleteReview() {
        $statement = 'DELETE FROM review WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->reviewId]);
    }
}