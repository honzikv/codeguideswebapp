<?php

namespace app\core;

class Router {

    private array $routes = []; # routes, kam se ukladaji callbacky pro danou path
    public Request $request;
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
     * @param $callback callback funkce, ktera se spusti
     */
    function setGetMethod(string $path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * Nastavi pro POST metodu s path dany callback
     * @param string $path: pro spusteni callbacku
     * @param $callback: funkce, ktera se spusti
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
            Application::getInstance()->response->setStatusCode(404);
            return $this->renderNotFound();
        }

        if (is_string($callback)) {
            return $this->render($callback);
        }

        # Pokud se jedna o array, tak vytvorime controller. Tento array by mel mit vzdy 2 prvky
        if (is_array($callback)) {
            $callback[0] = new $callback[0](); # zde je jako prvni parametr objekt controlleru
        }

        return call_user_func($callback, $this->request);
    }

    function render(string $view, $params = []) {
        $layoutContent = $this->getLayoutContent();
        $viewContent = $this->renderView($view, $params);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    private function renderView($view, $params) {

        foreach ($params as $key => $value) {
            $$key = $value; # predani parametru do stranky v include_once
        }

        ob_start(); # aby se nic nevytisklo
        include_once Application::$ROOT_PATH."/view/$view.php";
        return ob_get_clean(); # vratime view jako string
    }

    private function getLayoutContent() {
        ob_start(); # aby se nic nevytisklo
        include_once Application::$ROOT_PATH.'/view/layout/main_layout.php';
        return ob_get_clean(); # vratime template jako string
    }

    private function renderNotFound() {
        ob_start();
        include_once Application::$ROOT_PATH.'/view/error_view.php';
        return ob_get_clean();
    }
}