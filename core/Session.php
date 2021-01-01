<?php


namespace app\core;


/**
 * Trida, ktera slouzi jako wrapper pro session. Session je automaticky vytvorena pri startu jako soucast Application
 * Class Session
 * @package app\core
 */
class Session {

    function __construct() {
        session_start(); # start session
    }

    /**
     * Funkce pro zjisteni, zda-li je uzivatel prihlaseny
     * @return bool
     */
    function isUserLoggedIn(): bool {
        if (isset($_SESSION['username'])) {
            return true;
        } else {
            $_SESSION['role'] = 'viewer';
            $_SESSION['banned'] = '0';
            return false;
        }
    }

    /**
     * Nastavi uzivatelske informace pro prihlaseneho uzivatele
     * @param $username - uzivatelske jmeno
     * @param $role - role uzivatele
     * @param $id - id uzivatele
     * @param $banned - zda-li je uzivatel zabanovany
     */
    function setUserInfo($username, $role, $id, $banned) {
        $_SESSION['id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['banned'] = $banned;
    }

    /**
     * Vrati ulozene uzivatelske informace
     * @return array asociativni pole s uzivatelskymi informacemi
     */
    public function getUserInfo(): array {
        return [
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'id' => $_SESSION['id'],
            'banned' => $_SESSION['banned']
        ];
    }

    /**
     * Odstrani uzivatele ze session (pri logoutu)
     */
    function removeUser() {
        unset($_SESSION['username']);
        unset($_SESSION['id']);
        $_SESSION['banned'] = '0';
        $_SESSION['role'] = 'viewer';
    }

    /**
     * Ziska username uzivatele
     * @return mixed
     */
    function getUsername() {
        return $_SESSION['username'];
    }

    /**
     * Ziska id uzivatele
     * @return mixed
     */
    function getUserId() {
        return $_SESSION['id'];
    }

    /**
     * Vrati zda-li je uzivatel zablokovany
     * @return bool - true pokud ano, jinak false
     */
    public function isUserBanned(): bool {
        if (!isset($_SESSION['banned'])) {
            return false;
        }
        return $_SESSION['banned'] == '1' || $_SESSION['banned'] == true;
    }
}