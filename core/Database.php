<?php


namespace app\core;

use PDO;

require_once '../config/config.php';

class Database {

    private PDO $pdo;

    function __construct() {
        $this->pdo = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    }

    function prepare($statement) {

    }

}