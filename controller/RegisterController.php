<?php


namespace app\controller;


use app\core\Application;
use app\core\BaseController;
use app\core\Request;
use app\model\RegistrationModel;
use Exception;

/**
 * Class RegisterBaseController controller pro register stranku
 * @package app\controller
 */
class RegisterController extends BaseController {

    private const VIEW = 'register.twig';
    private const SUCCESSFUL_REGISTRATION = 'successful_registration.html';


    function render() {
        $this->__render(self::VIEW);
    }

    private function renderSuccessfulRegistration() {
        $this->__render(self::SUCCESSFUL_REGISTRATION);
    }

    function processRegistration(Request $request) {
        $userRegistrationModel = new RegistrationModel();
        $userRegistrationModel->loadData($request->getBody());

        try {
            $userRegistrationModel->validate();
        }
        catch (Exception $exception) {
            $this->__render(self::VIEW, [$exception->getMessage()]);
        }

        if ($userRegistrationModel->register()) {
            $session = Application::getInstance()->getSession();
            $session->setUser($userRegistrationModel);
            $this->renderSuccessfulRegistration();
        }

    }
}