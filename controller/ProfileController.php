<?php


namespace app\controller;


use app\core\BaseController;

class ProfileController extends BaseController {

    private const VIEW = 'profile.twig';

    function render() {
        $this->__render(self::VIEW);
    }
}