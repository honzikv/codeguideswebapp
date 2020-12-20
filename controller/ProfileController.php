<?php


namespace app\controller;


use app\core\BaseController;

class ProfileController extends BaseController {

    private static string $VIEW = 'profile.twig';

    function render() {
        $this->__render(self::$VIEW);
    }
}