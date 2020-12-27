<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class GuideDetailModel extends BaseModel {

    var string $guideId;

    function validate() {
        if (empty($this->guideId)) {
            throw new Exception('Guide id is empty');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Guide id is not a number');
        }
    }

    function getGuideWithAuthor() {
        $statement = 'SELECT guide.id, name, abstract, username, filename FROM guide INNER JOIN user u on guide.user_id = u.id
                    WHERE guide.id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->guideId]);
        return $query->fetch();
    }
}