<?php


namespace app\controller;


use app\core\BaseController;

class ProfileController extends BaseController {

    private static string $VIEW = 'profile';

    function show() {
        $this->render(self::$VIEW);
    }
}