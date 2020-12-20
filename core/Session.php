<?php


namespace app\core;


use app\model\RegistrationModel;

class Session {

    function __construct() {
        session_start();
    }

    function isUserLoggedIn(): bool {
        return isset($_SESSION['username']);
    }

    function setUser(RegistrationModel $userRegistrationData) {
        $_SESSION['username'] = $userRegistrationData->username;
        $_SESSION['role'] = $userRegistrationData->role;
        $_SESSION['password'] = $userRegistrationData->password;
    }

    public function getUserInfo(): array {
        return [
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'password' => $_SESSION['password']
        ];
    }
}