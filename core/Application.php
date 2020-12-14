<?php

namespace app\core;

class Application {

    var Router $router;

    function __construct() {
        $this->router = new Router();
    }

    function run() {

    }
}