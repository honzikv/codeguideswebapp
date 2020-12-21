<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class UserModel extends BaseModel {

    const TABLE_NAME = 'user';

    # sloupce
    const ID = 'id';
    const USERNAME = 'username';
    const EMAIL = 'email';
    const RANK = 'rank';
    const PREFERRED_ROLE = 'preferred_role';

    function validate() {
        throw new Exception("Invalid validate call");
    }

    function getUser($username) {
        $statement = 'SELECT * FROM USER WHERE username = (?)';
        $query = $this->prepare($statement);
        $query->execute([$username]);
        return $query->fetch();
    }

    function getRole($roleId) {
        $statement = 'SELECT role FROM ROLE_LOV where id = (?)';
        $query = $this->prepare($statement);
        $query->execute([$roleId]);
        return $query->fetch();
    }

    function getAllUsers() {
        $statement = 'SELECT * FROM USER';
        $query = $this->prepare($statement);
        $query->execute();
        return $query->fetchAll();
    }

     function getAllRoles() {
         $statement = 'SELECT * FROM ROLE_LOV';
         $query = $this->prepare($statement);
         $query->execute();
         return $query->fetchAll();
    }

     function getUserId($username) {
        $statement = 'SELECT * FROM USER where username = (?)';
        $query = $this->prepare($statement);
        $query->execute([$username]);
        return $query->fetch()['id'];
    }
}