<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class RoleChangeModel extends BaseModel {

    var string $userId;
    var string $role;

    function validate() {
        if (empty($this->userId)) {
            throw new Exception('Error, user id is empty');
        }

        if (!is_numeric($this->userId)) {
            throw new Exception('Error, provided value is not a number');
        }

        if (empty($this->role)) {
            throw new Exception('Error, role is empty');
        }
    }

    function changeRole() {
        $userModel = new UserModel();
        $roleId = $userModel->getRoleId($this->role);
        $statement = 'UPDATE user SET role_id = (?) where id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$roleId, $this->userId]);
    }

     function getResult() {
        $statement = 'SELECT user.id, role from user INNER JOIN role_lov rl on user.role_id = rl.id WHERE user.id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$this->userId]);
        return $query->fetch();
    }
}