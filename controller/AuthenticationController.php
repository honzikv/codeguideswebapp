<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;
use app\model\LoginModel;
use app\model\UserModel;
use Exception;

class AuthenticationController extends BaseController {

    private const VIEW = 'login.twig';
    private const LOGIN_SUCCESSFUL = 'successful_login.html';

    private UserModel $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    function renderLoginPage() {
        if ($this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        } else {
            $this->__render(self::VIEW);
        }
    }

    function processLogin(Request $request) {
        if ($this->session->isUserLoggedIn()) {
            return;
        }

        $loginModel = new LoginModel();
        $loginModel->loadData($request->getBody());

        try {
            $loginModel->validate();
            $loginModel->checkPassword($this->userModel);
        } catch (Exception $exception) {
            $response = ['error' => $exception->getMessage()];
            $response = json_encode($response);
            $this->sendResponse($response);
            return;
        }

        # pokud doposud nebyla vyhozena zadna exception pak je uzivatel autentifikovany
        $user = $this->userModel->getUserFromUsername($loginModel->username);
        $role = $this->userModel->getRole($user['role_id']);
        $this->session->setUserInfo($user['username'], $role['role']);

        $response = ['html' => $this->getRenderedView(self::LOGIN_SUCCESSFUL)];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

    function processLogout() {
        $this->session->removeUser();
        $this->redirectToIndex();
    }
}