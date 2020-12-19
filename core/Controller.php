<?php


namespace app\core;


class Controller {

    function render($view, $params = []) {
        return Application::getInstance()->router->render($view, $params);
    }
}