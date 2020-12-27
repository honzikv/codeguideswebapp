<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class DeleteGuideModel extends BaseModel {

    var string $guideId;


    function validate() {
        if (empty($this->guideId)) {
            throw new Exception('Error, guide id is empty');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Error, guide id is not a number');
        }
    }

    function delete() {
        $statement = 'DELETE FROM guide WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->guideId]);
    }


}