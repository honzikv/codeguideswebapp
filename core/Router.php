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
     * @param string $path path pro spusteni callbacku
     * @param $callback callback funkce, ktera se spusti
     */
    function setGetMethod(string $path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * Nastavi pro POST metodu s path dany callback
     * @param $path path pro spusteni callbacku
     * @param $callback funkce, ktera se spusti
     */
    function setPostMethod($path, $callback) {
        $this->routes['post'][$path] = $callback;
    }

    function resolve() {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        # ziskani callback funkce z requestu, pokud neni nastavi na false
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback == false) {
            return "Not Found";
        }

        if (is_string($callback)) {
            return $this->renderView($callback);
        }

        return call_user_func($callback);
    }

    private function renderView(string $view) {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderView($view);
        include_once __DIR__."/../view/$view.php"; # render view
    }

    private function layoutContent() {
        ob_start(); # aby se nic nevytisklo
        include_once Application::$ROOT_PATH.'/views/layouts/main_layout.php';
        return ob_get_clean(); # vratime template jako string
    }
}