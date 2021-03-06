<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

/**
 * Model pro zabanovani uzivatele
 * Class BanModel
 * @package app\model
 */
class BanModel extends BaseModel {

    var string $userId;

    function validate() {

        if (empty($this->userId)) {
            throw new Exception('Error, user id not provided');
        }

        if (!is_numeric($this->userId)) {
            throw new Exception('Error, provided value is not a number');
        }

        if (!$this->existsInDatabase('user', 'id', $this->userId)) {
            throw new Exception('Error, user id not found');
        }
    }

    /**
     * Provede ban - tzn. uzivatel, ktery je zabanovany je odbanovan a ten, co zabanovany nebyl naopak ban dostane
     * (pouze flip booleanu v tabulce)
     * @param $value : hodnota na, kterou se ban nastavi - tzn. true (pro ban) nebo false (nema ban)
     */
    function banUser($value) {
        $statement = 'UPDATE user SET banned = (?) WHERE id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$value, $this->userId]);
    }
}