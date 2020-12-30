<?php


namespace app\core;


class BaseController {
    private const BANNED_VIEW = 'banned.html'; # view pro zobrazeni, kdyz je uzivatel zabanovany

    protected Session $session; # reference na session pro snazsi pristup

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

    /**
     * Ziska renderovane view - tzn. html jako string
     * @param $view: cesta k view
     * @param array $params: parametry pro twig
     */
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

    /**
     * Funkce pro presmerovani na hlavni stranku
     */
    protected function redirectToIndex() {
        header('Location: /'); # presmerovani na hlavni stranku
        die();
    }

    /**
     * Funkce pro presmerovani na 404 pri spatne URL
     */
    protected function redirectTo404() {
        Application::getInstance()->response->setStatusCode(404);
        header('Location: /error'); # presmerovani na error
        die();
    }

}