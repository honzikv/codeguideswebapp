<?php


namespace app\controller;


use app\core\BaseController;

class TournamentsController extends BaseController {

    private static string $VIEW = 'tournaments';

    function show() {
        $this->render(self::$VIEW);
    }
}