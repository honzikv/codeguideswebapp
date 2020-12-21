<?php


namespace app\controller;


use app\core\BaseController;

class GuideListController extends BaseController {

    private const VIEW = 'guide_list.twig';

    function render() {
        $this->__render(self::VIEW);
    }
}