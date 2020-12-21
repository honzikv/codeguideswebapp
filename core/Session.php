<?php


namespace app\core;


class Session {

    function __construct() {
        session_start();
        $_SESSION['role'] = 'viewer';
    }

    function isUserLoggedIn(): bool {
        return isset($_SESSION['username']);
    }

    function setUserInfo($username, $role) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
    }

    public function getUserInfo(): array {
        return [
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'password' => $_SESSION['password']
        ];
    }

    function removeUser() {
        unset($_SESSION['username']);
        $_SESSION['role'] = 'viewer';
    }
}