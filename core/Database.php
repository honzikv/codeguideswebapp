<?php


namespace app\core;

use PDO;

require_once '../config/config.php';

class Database {

    private PDO $pdo;

    function __construct() {
        $this->pdo = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); # pouze pro debug
    }

    function prepare($statement) {
        return $this->pdo->prepare($statement);
    }

}