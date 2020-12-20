<?php


namespace app\controller;

use app\core\Application;
use app\core\BaseController;

class ErrorController extends BaseController {

    private const VIEW = "error_view.html";

    function render() {
        $this->__render(self::VIEW);
    }
}