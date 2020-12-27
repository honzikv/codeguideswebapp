<?php


namespace app\controller;

use app\core\BaseController;

class ErrorController extends BaseController {

    private const NOT_FOUND_VIEW = "error_view.html"; # zobrazeni 404
    private const TEAPOT_VIEW = "teapot_error.html"; # zobrazeni 418

    /**
     * Render not found - zobrazi se kdykoliv, kdy uzivatel zada nesmyslnou URL
     */
    function render404() {
        $this->__render(self::NOT_FOUND_VIEW);
    }

    /**
     * Render stranky s 418 - tzn. kdyz by uzivatel posilal requesty, ktere nedavaji smysl
     */
    function render418() {
        $this->__render(self::TEAPOT_VIEW);
    }
}