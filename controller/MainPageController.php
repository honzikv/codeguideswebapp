<?php


namespace app\controller;


use app\core\BaseController;

class MainPageController extends BaseController {

    private const VIEW = 'index.twig';

    function render() {
        $this->__render(self::VIEW);
    }
}