<?php

namespace app\core;

class Application {

    var Router $router; # router pro routing v aplikaci
    var Request $request; # request pri spusteni aplikace
    public static string $ROOT_PATH;

    function __construct(string $rootPath) {
        self::$ROOT_PATH = $rootPath;
        $this->request = new Request();
        $this->router = new Router($this->request);
    }

    function run() {
        $this->router->resolve();
    }
}