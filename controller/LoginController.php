<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;

class LoginController extends BaseController {

    private const VIEW = 'login.twig';
    private const OTHER_VIEW = 'register.twig';

    function render() {
         $this->__render(self::VIEW);
    }

    function processLogin(Request $request) {
        $this->__render(self::OTHER_VIEW);
    }
}