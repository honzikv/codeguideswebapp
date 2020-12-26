<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class FinalizeGuideModel extends BaseModel {

    var string $guideId;

    function validate() {
        if (empty($this->guideId)) {
            throw new Exception('Error, guide id is empty');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Error, guide id is not a number');
        }
    }

    function acceptGuide() {
        $guideModel = new GuideModel();
        $statePublished = $guideModel->getGuideState('published');
        $statement = 'UPDATE guide SET guide_state = (?) WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$statePublished['id'], $this->guideId]);
    }

    function removeGuide() {
        $statement = 'DELETE FROM guide WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->guideId]);
    }

    function rejectGuide() {
        $guideModel = new GuideModel();
        $stateRejected = $guideModel->getGuideState('rejected');
        $statement = 'UPDATE guide SET guide_state = (?) WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$stateRejected['id'], $this->guideId]);
    }
}