<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;
use app\model\UserRegistrationModel;

/**
 * Class RegisterBaseController controller pro register stranku
 * @package app\controller
 */
class RegisterController extends BaseController {

    private const VIEW = 'register.twig';

    function render() {
        $this->__render(self::VIEW);
    }

    function processRegistration(Request $request) {
        $userRegistrationModel = new UserRegistrationModel();
        $userRegistrationModel->loadData($request->getBody());
    }
}