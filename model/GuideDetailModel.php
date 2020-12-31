<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

/**
 * Model pro detail dane guide
 * Class GuideDetailModel
 * @package app\model
 */
class GuideDetailModel extends BaseModel {

    var string $guideId;

    /**
     * Validace zda-li ma guideId spravny format a zda-li existuje
     * @throws Exception
     */
    function validate() {
        if (empty($this->guideId)) {
            throw new Exception('Guide id is empty');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Guide id is not a number');
        }

        if (!$this->existsInDatabase('guide', 'id', $this->guideId)) {
            throw new Exception('Error, guide id does not exist');
        }
    }

    /**
     * Ziska guide s jmenem autora
     */
    function getGuideWithAuthor() {
        $statement = 'SELECT guide.id, u.id as user_id, name, guide_state, abstract, username, filename FROM guide INNER JOIN user u on guide.user_id = u.id
                    WHERE guide.id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->guideId]);
        return $query->fetch();
    }

}