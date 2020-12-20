<?php

namespace app\core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Application {

    public Router $router; # router pro routing v aplikaci
    public Request $request; # request pri spusteni aplikace
    public Response $response; # response pro zobrazeni 404 apod.
    public static string $ROOT_PATH;

    private static Application $instance;
    private FilesystemLoader $loader;
    private Environment $twig;
    private BaseController $controller;

    function __construct(string $rootPath) {
        self::$ROOT_PATH = $rootPath;
        self::$instance = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->loader = new FilesystemLoader("$rootPath/view/");
        $this->twig = new Environment($this->loader, [
            'cache' => '../cache'
        ]);
    }

    static function getInstance(): Application {
        return self::$instance;
    }

    function run() {
        echo $this->router->resolve();
    }

    /**
     * @return BaseController
     */
    public function getController(): BaseController {
        return $this->controller;
    }

    /**
     * @return FilesystemLoader
     */
    public function getLoader(): FilesystemLoader {
        return $this->loader;
    }

    /**
     * @return Environment
     */
    public function getTwig(): Environment {
        return $this->twig;
    }


    /**
     * @param BaseController $controller
     */
    public function setController(BaseController $controller): void {
        $this->controller = $controller;
    }
}