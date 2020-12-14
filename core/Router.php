<?php

namespace app\core;

class Router {

    private array $routes = [];
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
}