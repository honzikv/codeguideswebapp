<?php


namespace app\core;


class BaseController {

    static function getLayout(): string {
        return 'main_layout.php';
    }

    function render($view, $params = []) {
        echo Application::getInstance()->router->render($view, $params);
    }

}