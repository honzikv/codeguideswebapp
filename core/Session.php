<?php


namespace app\core;


class Session {

    function __construct() {
        session_start();
    }

    function isUserLoggedIn(): bool {
        if (isset($_SESSION['username'])) {
            return true;
        } else {
            $_SESSION['role'] = 'viewer';
            $_SESSION['banned'] = '0';
            return false;
        }
    }

    function setUserInfo($username, $role, $id, $banned) {
        $_SESSION['id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['banned'] = $banned;
    }

    public function getUserInfo(): array {
        return [
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ];
    }

    function removeUser() {
        unset($_SESSION['username']);
        unset($_SESSION['id']);
        $_SESSION['banned'] = '0';
        $_SESSION['role'] = 'viewer';
    }

    function getUsername() {
        return $_SESSION['username'];
    }

    function getUserId() {
        return $_SESSION['id'];
    }

    public function isUserBanned(): bool {
        return $_SESSION['banned'] == '1' || $_SESSION['banned'] == true;
    }
}