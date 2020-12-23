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
class RegistrationController extends BaseController {

    private const VIEW = 'register.twig';
    private const SUCCESSFUL_REGISTRATION = 'successful_registration.html';

    function render() {
        if ($this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        } else {
            $this->__render(self::VIEW);
        }
    }

    function processRegistration(Request $request) {
        if ($this->session->isUserLoggedIn()) {
            return; # asi by nedavalo smysl odpovidat na POST pro registraci kdyz je uzivatel uz prihlaseny
        }

        $userRegistrationModel = new RegistrationModel();
        $userRegistrationModel->loadData($request->getBody());

        try {
            $userRegistrationModel->validate();
            $userRegistrationModel->checkIfExists();
        } catch (Exception $exception) {
            $response = ['error' => $exception->getMessage()];
            $response = json_encode($response);
            $this->sendResponse($response);
            return;
        }

        if ($userRegistrationModel->register()) {
            $response = ['html' => $this->getRenderedView(self::SUCCESSFUL_REGISTRATION)];
            $response = json_encode($response);
            $this->sendResponse($response);
        } else {
            $response = ['error' => 'Internal server error, try later'];
            $response = json_encode($response);
            $this->sendResponse($response);
        }

    }
}