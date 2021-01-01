<?php


namespace app\core;

use PDO;

require_once '../config/config.php';

/**
 * Trida, ktera slouzi jako wrapper PDO, je vytvorena pouze jednou jako soucast tridy Application
 * Class Database
 * @package app\core
 */
class Database {

    private PDO $pdo; # pdo instance pro pristup k db

    function __construct() { # konstruktor vytvori nove pdo pro pripojeni k databazi
        $this->pdo = new PDO('mysql:host=' . DB_SERVER . 'charset=utf8' . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
#        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); # pouze pro debug
    }

    /**
     * Provede pdo->prepare pro dany SQL statement a vrati query jako vysledek
     * @param $statement
     * @return false|\PDOStatement vrati query (nebo false pri chybe)
     */
    function prepare($statement) {
        return $this->pdo->prepare($statement);
    }

}