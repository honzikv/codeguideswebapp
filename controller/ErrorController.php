<?php


namespace app\controller;

use app\core\BaseController;

class ErrorController extends BaseController {

    private const NOT_FOUND_VIEW = "error_view.html";
    private const TEAPOT_VIEW = "teapot_error.html";

    function render404() {
        $this->__render(self::NOT_FOUND_VIEW);
    }

    function render418() {
        $this->__render(self::TEAPOT_VIEW);
    }
}