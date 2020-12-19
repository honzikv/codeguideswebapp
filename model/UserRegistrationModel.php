<?php

namespace app\model;

use app\core\BaseModel;
use Exception;

class UserRegistrationModel extends BaseModel {

    private const USERNAME_LIMIT = 30;
    private const EMAIL_LIMIT = 60;
    private const PASSWORD_LIMIT = 255;

    var string $username;
    var string $email;
    var string $password;
    var string $role;

    function roleMatches($input) {

    }

    function validate() {
        if (empty($this->username)) {
            throw new Exception("Username is empty");
        }

        if (strlen($this->username) > self::USERNAME_LIMIT) {
            throw new Exception("User name is too long (max " . self::USERNAME_LIMIT . " characters).");
        }

        if (empty($this->email)) {
            throw new Exception("Email is empty.");
        }

        if (strlen($this->email) > self::EMAIL_LIMIT) {
            throw new Exception("Email is too long (max " . self::EMAIL_LIMIT . " characters).");
        }

        if (empty($this->role)) {
            throw new Exception("Error, role is not selected.");
        }

        if (!roleMatches($this->role)) {
            throw new Exception("Error, invalid role.");
        }

        if (empty($this->password)) {
            throw new Exception("Error, password is empty.");
        }

        if (strlen($this->password) > 255) {
            throw new Exception("Error, password is too long (max " . self::PASSWORD_LIMIT . " characters).");
        }
    }
}