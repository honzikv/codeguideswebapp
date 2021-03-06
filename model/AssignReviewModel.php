<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

/**
 * Model pro prirazeni review pro daneho recenzenta
 * Class AssignReviewModel
 * @package app\model
 */
class AssignReviewModel extends BaseModel {

    const REVIEWS_PER_GUIDE = 3; # pozadovany pocet recenzi pro zverejneni

    var string $userId;
    var string $guideId;

    function validate() {
        if (empty($this->userId)) {
            throw new Exception('Error, user id is empty');
        }

        if (!is_numeric($this->userId)) {
            throw new Exception('Error, user id is not a number');
        }

        if (empty($this->guideId)) {
            throw new Exception('Error, guide id is empty');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Error, guide id is not a number');
        }

        if (!$this->existsInDatabase('user', 'id', $this->userId)) {
            throw new Exception('Error, user id not found');
        }

        if (!$this->existsInDatabase('guide', 'id', $this->guideId)) {
            throw new Exception('Error, guide id not found');
        }
    }

    /**
     * Priradi recenzentovi dane review
     * @throws Exception
     */
    function assignReview() {
        $guideModel = new GuideModel();
        $guideReviews = $guideModel->getGuideReviews($this->guideId);

        # pokud by nekdo chtel hodnotit vice nez trikrat
        if (count($guideReviews) >= self::REVIEWS_PER_GUIDE) {
            throw new Exception('Error, there are already three reviews in progress for this guide');
        }

        # stejny recenzent muze recenzovat danou guide nejvyse jednou
        foreach ($guideReviews as $guideReview) {
            if ($guideReview['reviewer_id'] == $this->userId) {
                throw new Exception('Error, this user already reviews/reviewed this guide');
            }
        }

        $statement = 'INSERT INTO review (reviewer_id, guide_id) VALUES (?, ?)';
        $query = $this->prepare($statement);
        $query->execute([$this->userId, $this->guideId]);

    }

}