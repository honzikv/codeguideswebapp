<?php


namespace app\core;


class BaseController {

    protected Session $session;

    public function __construct() {
        $this->session = Application::getInstance()->getSession();
    }

    /**
     * Provede render daneho view - tzn posle ho frontendu jako hotovou stranku
     * @param $view
     * @param array $params
     */
    protected function __render($view, $params = []) {
        if ($this->session->isUserLoggedIn()) {
            $params['user'] = $this->session->getUserInfo();
        }

        $params['view'] = $view;

        print(Application::getInstance()->getTwig()->render($view, $params));
    }

    protected function getRenderedView($view, $params = []) {
        if ($this->session->isUserLoggedIn()) {
            $params['user'] = $this->session->getUserInfo();
        }

        $params['view'] = $view;

        return Application::getInstance()->getTwig()->render($view, $params);
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

    protected function redirectTo404() {
        header('Location: /error');
        die();
    }
}