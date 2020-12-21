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
            $this->__render(self::VIEW, ['error' => $exception->getMessage(),
                'username' => $loginModel->username]);
            return;
        }

        # pokud doposud nebyla vyhozena zadna exception pak je uzivatel autentifikovany
        $user = $this->userModel->getUser($loginModel->username);
        $role = $this->userModel->getRole($user['role_id']);
        $this->session->setUserInfo($user['username'], $role['role']);

        $this->__render(self::LOGIN_SUCCESSFUL);
    }

    function processLogout() {
        $this->session->removeUser();
        $this->redirectToIndex();
    }
}