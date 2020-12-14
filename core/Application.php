<?php

namespace app\core;

class Application {

    var Router $router; # router pro routing v aplikaci
    var Request $request; # request pri spusteni aplikace

    function __construct() {
        $this->request = new Request();
        $this->router = new Router($this->request);
    }

    function run() {
        $this->router->resolve();
    }
}