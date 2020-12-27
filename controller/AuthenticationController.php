<?php


namespace app\controller;


use app\core\BaseController;
use app\core\Request;
use app\model\LoginModel;
use app\model\RegistrationModel;
use app\model\UserModel;
use Exception;

/**
 * Tento controller slouzi k autentizaci uzivatele - registrace, login a odhlaseni
 * Class AuthenticationController
 * @package app\controller
 */
class AuthenticationController extends BaseController {

    private const LOGIN_VIEW = 'login.twig'; # view s loginem
    private const REGISTRATION_VIEW = 'register.twig'; # view s registraci
    private const SUCCESSFUL_REGISTRATION = 'successful_registration.html'; # html pro zobrazeni uspesne registrace
    private const LOGIN_SUCCESSFUL = 'successful_login.html'; # html pro zobrazeni uspesneho loginu
    private const BANNED_VIEW = 'banned.html'; # view pro zobrazeni, kdyz je uzivatel zabanovany

    private UserModel $userModel; # user model pro pristup k databazi

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    function renderLoginPage() {
        if ($this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        } else {
            $this->__render(self::LOGIN_VIEW);
        }
    }

    function renderRegistrationPage() {
        if ($this->session->isUserLoggedIn()) {
            $this->redirectToIndex();
        } else {
            $this->__render(self::REGISTRATION_VIEW);
        }
    }

    /**
     * Zpracovani prihlaseni uzivatele
     * @param Request $request POST request s informacemi o loginu - heslo a username
     */
    function processLogin(Request $request) {
        if ($this->session->isUserLoggedIn()) { # nelze se prihlasit pokud se uzivatel uz prihlasil
            return;
        }

        $loginModel = new LoginModel(); # data pro login
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
        $this->session->setUserInfo($user['username'], $role['role'], $user['id'], $user['banned']);

        $response = ['html' => $this->getRenderedView(self::LOGIN_SUCCESSFUL)];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

    /**
     * Zpracovani pozadavku na registraci
     * @param Request $request POST request s udaji pro registraci
     */
    function processRegistration(Request $request) {
        if ($this->session->isUserLoggedIn()) {
            return; # asi by nedavalo smysl odpovidat na POST pro registraci kdyz je uzivatel uz prihlaseny
        }

        $userRegistrationModel = new RegistrationModel(); # model s daty z formulare na registraci
        $userRegistrationModel->loadData($request->getBody()); # nacteni dat

        try {
            $userRegistrationModel->validate(); # validace
            $userRegistrationModel->register();
        } catch (Exception $exception) {
            $response = ['error' => $exception->getMessage()];
            $response = json_encode($response);
            $this->sendResponse($response);
            return;
        }

        # render uspesne registrace
        $response = ['html' => $this->getRenderedView(self::SUCCESSFUL_REGISTRATION)];
        $response = json_encode($response);
        $this->sendResponse($response);
    }

    /**
     * Zpracovani odhlaseni - odstranime uzivatelska data ze session
     */
    function processLogout() {
        $this->session->removeUser(); # odstraneni uzivatelskych dat
        $this->redirectToIndex(); # presmerovani na hlavni stranku
    }

    /**
     * Render banned stranky, pokud je uzivatel zabanovany
     */
    function renderBanned() {
        $this->__render(self::BANNED_VIEW);
    }
}