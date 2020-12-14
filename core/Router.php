<?php

namespace app\core;

class Router {

    private array $routes = []; # routes, kam se ukladaji callbacky pro danou path
    public Request $request;

    /**
     * Konstruktor pro vytvoreni v Application
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * Nastavi pro GET metodu s path dany callback
     * @param $path path pro spusteni callbacku
     * @param $callback callback funkce, ktera se spusti
     */
    function setGetMethod($path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * Nastavi pro POST metodu s path dany callback
     * @param $path path pro spusteni callbacku
     * @param $callback funkce, ktera se spusti
     */
    function setPostMethod($path, $callback) {
        $this->routes['post'][$path];
    }

    function resolve() {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        # ziskani callback funkce z requestu, pokud neni nastavi na false
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback == false) {
            print(404);
            exit();
        }

    }
}