<?php


namespace app\core;


class BaseController {

    protected function __render($view, $params = []) {
        echo Application::getInstance()->getTwig()->render($view, $params);
    }

}