<?php


namespace app\core;


class BaseController {

    function render($view, $params = []) {
        echo Application::getInstance()->router->render($view, $params);
    }

}