<?php


namespace app\controller;


class ManageContentController {

    private const VIEW = 'manage_content.twig';

    function render() {
        $this->__render(self::VIEW);
    }
}