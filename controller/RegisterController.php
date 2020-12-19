<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;
use app\model\RegisterModel;

/**
 * Class RegisterBaseController controller pro register stranku
 * @package app\controller
 */
class RegisterController extends BaseController {

    private static string $VIEW = 'register';

    function show() {
        $this->render(self::$VIEW);
    }

    function processRegistration(Request $request) {
    }
}