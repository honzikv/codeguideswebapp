<?php

namespace app\core;

class Application {

    public Router $router; # router pro routing v aplikaci
    public Request $request; # request pri spusteni aplikace
    public Response $response; # response pro zobrazeni 404 apod.
    public static string $ROOT_PATH;

    private static Application $instance;

    function __construct(string $rootPath) {
        self::$ROOT_PATH = $rootPath;
        self::$instance = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
    }

    static function getInstance() {
        return self::$instance;
    }

    function run() {
        echo $this->router->resolve();
    }
}