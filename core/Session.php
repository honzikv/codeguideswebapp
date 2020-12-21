<?php


namespace app\core;


class Session {

    function __construct() {
        session_start();
    }

    function isUserLoggedIn(): bool {
        if (isset($_SESSION['username'])) {
            return true;
        }
        else {
            $_SESSION['role'] = 'viewer';
            return false;
        }
    }

    function setUserInfo($username, $role) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
    }

    public function getUserInfo(): array {
        return [
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ];
    }

    function removeUser() {
        unset($_SESSION['username']);
        $_SESSION['role'] = 'viewer';
    }
}