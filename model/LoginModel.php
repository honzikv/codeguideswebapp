<?php


namespace app\model;


use app\core\BaseModel;
use Exception;

class LoginModel extends BaseModel {

    var string $username;
    var string $password;

    function validate() {
        if (empty($this->username)) {
            throw new Exception('Error, username is empty');
        }

        if (empty($this->password)) {
            throw new Exception('Error, password is empty');
        }
    }

    function checkPassword(UserModel $userModel) {
        $user = $userModel->getUser($this->username);
        if ($user == false || !password_verify($this->password, $user['password'])) {
            throw new Exception('Error, incorrect username password combination');
        }

    }


}