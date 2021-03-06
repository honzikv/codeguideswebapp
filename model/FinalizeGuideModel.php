<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

/**
 * Model pro finalizaci guide - tzn. prijmuti nebo odmitnuti
 * Class FinalizeGuideModel
 * @package app\model
 */
class FinalizeGuideModel extends BaseModel {

    var string $guideId;

    function validate() {
        if (empty($this->guideId)) {
            throw new Exception('Error, guide id is empty');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Error, guide id is not a number');
        }

        if (!$this->existsInDatabase('guide', 'id', $this->guideId)) {
            throw new Exception('Error, guide id does not exist');
        }
    }

    function acceptGuide($publishedId) {
        $statement = 'UPDATE guide SET guide_state = (?) WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$publishedId, $this->guideId]);
    }

    function rejectGuide() {
        $guideModel = new GuideModel();
        $stateRejected = $guideModel->getGuideState('rejected');
        $statement = 'UPDATE guide SET guide_state = (?) WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$stateRejected['id'], $this->guideId]);
    }
}