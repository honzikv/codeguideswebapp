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
        $this->__render(self::VIEW);
    }

    function processLogin(Request $request) {
        $loginModel = new LoginModel();
        $loginModel->loadData($request->getBody());

        try {
            $loginModel->validate();
            $loginModel->checkPassword($this->userModel);
        } catch (Exception $exception) {
            $this->__render(self::VIEW, ['error' => $exception->getMessage()]);
            return;
        }

        # pokud doposud nebyla vyhozena zadna exception pak je uzivatel autentifikovany
        $user = $this->userModel->getUser($loginModel->username);
        $role = $this->userModel->getRole($user['role_id']);
        $this->session->setUserInfo($user['username'], $role);

        $this->__render(self::LOGIN_SUCCESSFUL);
    }

    function processLogout() {
        $this->session->removeUser();
        header('Location: /'); # presmerovani na hlavni stranku
        die();
    }
}