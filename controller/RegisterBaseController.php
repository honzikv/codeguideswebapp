<?php


namespace app\controller;


use app\core\BaseController;

/**
 * Class RegisterBaseController controller pro register stranku
 * @package app\controller
 */
class RegisterBaseController extends BaseController {

    private static string $VIEW = 'register';

    function show() {
        $this->render(self::$VIEW);
    }
}