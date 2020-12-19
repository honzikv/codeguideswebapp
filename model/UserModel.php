<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class UserModel extends BaseModel {



    function validate() {
        throw new Exception("Invalid validate call");
    }
}