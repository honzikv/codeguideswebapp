<?php

namespace app\core;

use app\controller\ErrorController;

class Router {

    private array $routes = []; # routes, kam se ukladaji callbacky pro danou path
    private Request $request;
    private Response $response;

    /**
     * Konstruktor pro vytvoreni v Application
     * @param Request $request
     * @param Response $response
     */
    function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Nastavi pro GET metodu s path dany callback
     * @param string $path path pro spusteni callbacku
     * @param $callback: callback funkce, ktera se spusti
     */
    function setGetMethod(string $path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * Nastavi pro POST metodu s path dany callback
     * @param string $path : pro spusteni callbacku
     * @param $callback : funkce, ktera se spusti
     */
    function setPostMethod(string $path, $callback) {
        $this->routes['post'][$path] = $callback;
    }

    function resolve() {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        # ziskani callback funkce z requestu, pokud neni nastavi na false
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback == false) {
            header('Location: /error');
            die();
        }

        # Pokud se jedna o array, tak vytvorime controller. Tento array by mel mit vzdy 2 prvky
        if (is_array($callback)) {
            Application::getInstance()->setController(new $callback[0]());
            $callback[0] = Application::getInstance()->getController();
        }

        return call_user_func($callback, $this->request);
    }

}