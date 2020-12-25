<?php


namespace app\core;


class BaseController {

    protected Session $session;

    public function __construct() {
        $this->session = Application::getInstance()->getSession();
    }

    /**
     * Provede render daneho view - tzn posle ho frontendu jako hotovou stranku
     * @param $view: lokace souboru s VIEW - twig, html nebo jine
     * @param array $params: parametry, ktere se predaji view
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
     * @param $response: response, ktera se ma odeslat - musi by prevedena pomoci json_encode pokud chceme json
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