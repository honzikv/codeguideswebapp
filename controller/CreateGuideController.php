<?php


namespace app\controller;


use app\core\BaseController;

class CreateGuideController extends BaseController {

    private const VIEW = 'create_guide.twig';

    function render() {
        $this->__render(self::VIEW);
    }
}