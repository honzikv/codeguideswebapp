<?php


namespace app\model;


use app\core\Application;
use app\core\BaseModel;
use Exception;

class DeleteUserModel extends BaseModel {

    var string $userId;

    function validate() {
        if (empty($this->userId)) {
            throw new Exception('Error, user id is empty');
        }

        if (!is_numeric($this->userId)) {
            throw new Exception('Error, user id is not a number');
        }

        if (!$this->existsInDatabase('user', 'id', $this->userId)) {
            throw new Exception('Error, user id does not exist');
        }
    }

    function removeUser() {
        $statement = 'DELETE FROM user where id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->userId]);
    }
}