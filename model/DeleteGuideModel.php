<?php


namespace app\model;


use app\core\Application;
use app\core\BaseModel;
use Exception;

/**
 * Model pro odstraneni guide uzivatele
 * Class DeleteGuideModel
 * @package app\model
 */
class DeleteGuideModel extends BaseModel {

    var string $guideId;

    function validate() {
        if (empty($this->guideId)) {
            throw new Exception('Error, guide id is empty');
        }

        if (!is_numeric($this->guideId)) {
            throw new Exception('Error, guide id is not a number');
        }

        if (!$this->existsInDatabase('guide','id',$this->guideId)) {
            throw new Exception('Error, guide id not found');
        }
    }

    /**
     * Smazani dane guide
     * @throws Exception
     */
    function delete() {
        $statement = 'SELECT * FROM guide where id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->guideId]);
        $guide = $query->fetch();

        $path = Application::$FILES_PATH . $guide['filename'];
        if (!unlink($path)) {
            throw new Exception('Error, file could not be removed');
        }

        $statement = 'DELETE FROM guide WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->guideId]);
    }


}