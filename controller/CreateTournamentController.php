<?php


namespace app\controller;


use app\core\BaseController;

class CreateTournamentController extends BaseController {

    private const VIEW = 'create_tournament.twig';

    function render() {
        $this->__render(self::VIEW);
    }
}