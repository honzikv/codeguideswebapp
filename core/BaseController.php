<?php


namespace app\core;


class BaseController {

    protected Session $session;

    public function __construct() {
        $this->session = Application::getInstance()->getSession();
    }

    protected function __render($view, $params = []) {
        if ($this->session->isUserLoggedIn()) {
            $params['user'] = $this->session->getUserInfo();
        }

        $params['view'] = $view;

        print(Application::getInstance()->getTwig()->render($view, $params));
    }

    /**
     * Posle response - uzitecne pro async odpovedi
     */
    protected function sendResponse($response) {
        print($response);
    }

    protected function redirectToIndex() {
        header('Location: /'); # presmerovani na hlavni stranku
        die();
    }
}