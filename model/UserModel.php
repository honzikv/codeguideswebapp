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
}